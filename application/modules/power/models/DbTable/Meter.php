<?php
/**
 * Meter.php
 *
 * Copyright (c) 2011 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of BBA.
 *
 * BBA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BBA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BBA.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Database adapter class for the Meter table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Meter extends ZendSF_Model_DbTable_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'meter';

    /**
     * @var string primary key
     */
    protected $_primary = 'meter_idMeter';

    /**
     * @var string row class
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_Meter';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'site'      => array(
            'columns'       => 'meter_idSite',
            'refTableClass' => 'Power_Model_DbTable_Site',
            'refColumns'    => 'site_idSite'
        ),
        'user'      => array(
            'columns'       => array(
                'meter_userCreate',
                'meter_userModify'
            ),
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    public function getMeterById($id)
    {
        return $this->find($id)->current();
    }

    /**
     * Aggregates the site, client and client_address tables
     * into the meter details.
     *
     * @return Zend_Db_Table_Select
     */
    public function getMeterDetails($id)
    {
       $select = $this->select(false)->setIntegrityCheck(false)
            ->from('meter')
            ->join('site', 'site_idSite = meter_idSite', null)
            ->join('client_address', 'clientAd_idAddress = site_idAddress')
            ->join('client', 'client_idClient = site_idClient')
            ->joinLeft('client_contact', 'client_idClientContact = clientCo_idClientContact')
            ->joinLeft('meter_contract', 'meter_idMeter = meterContract_idMeter')
            ->joinLeft('contract', 'meterContract_idContract = contract_idContract')
            ->joinLeft('tender', 'contract_idTenderSelected = tender_idTender')
            ->joinLeft('supplier', 'tender_idSupplier = supplier_idSupplier')
            ->where('meter_idMeter = ?', $id);
       return $this->fetchRow($select);
    }

    public function searchMeters(array $search, $sort = '', $count = null, $offset = null)
    {
       $select = $this->select(false)->setIntegrityCheck(false)
            ->from('meter', array(
                'meter_idMeter',
                'meter_type',
                'meter_status',
                'meter_numberMain'
            ))
            ->join('site', 'site_idSite = meter_idSite', null)
            ->join('client_address', 'clientAd_idAddress = site_idAddress', array(
                'clientAd_addressName',
                'clientAd_postcode'
            ))
            ->join('client', 'client_idClient = site_idClient', array(
                'client_name'
            ))
            ->joinLeft('meter_contract', 'meter_idMeter = meterContract_idMeter', null)
            ->joinLeft('contract', 'meterContract_idContract = contract_idContract', array(
                'contract_type',
                'contract_status',
                'contract_dateStart' => 'MAX(contract.contract_dateStart)',
                'contract_dateEnd'
            ))->group('meter_idMeter');

        if (!$search['meter'] == '') {
            $filter = new Zend_Filter_PregReplace(array(
                    'match' => '/-/',
                    'replace' => ''
                )
            );

            if (substr($search['meter'], 0, 1) == '=') {
                $id = (int) substr($search['meter'], 1);
                $select->where('meter_idMeter = ?', $id);
            } else {
                $select->orWhere('meter_numberMain like ?', '%'. $filter->filter($search['meter']) . '%');
            }
        }

        if (!$search['site'] == '') {
            $select
                ->orWhere('clientAd_addressName like ? ', '%' . $search['site'] . '%')
                ->orWhere('clientAd_address1 like ?', '%' . $search['site'] . '%')
                ->orWhere('clientAd_address2 like ?', '%' . $search['site'] . '%')
                ->orWhere('clientAd_address3 like ?', '%' . $search['site'] . '%')
                ->orWhere('clientAd_postcode like ?', '%' . $search['site'] . '%');
        }

        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $result = $this->searchMeters($search);
        return $result->count();
    }

    public function insert(array $data)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['meter_dateCreate'] = new Zend_Db_Expr('CURDATE()');
        $data['meter_userCreate'] = $auth->getId();

        $this->_log->info(Zend_Debug::dump($data, "\nINSERT: " . __CLASS__ . "\n", false));

        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['meter_dateModify'] = new Zend_Db_Expr('CURDATE()');
        $data['meter_userModify'] = $auth->getId();

        $this->_log->info(Zend_Debug::dump($data, "\nUPDATE: " . __CLASS__ . "\n", false));

        return parent::update($data, $where);
    }
}
