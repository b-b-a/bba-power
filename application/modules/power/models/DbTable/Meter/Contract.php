<?php
/**
 * MeterContract.php
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
 * Database adapter class for the MeterContract table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Meter_Contract extends ZendSF_Model_DbTable_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'meter_contract';

    /**
     * @var string primary key
     */
    protected $_primary = array('meterContract_idMeter', 'meterContract_idContract');

    /**
     * @var string row class
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_Meter_Contract';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'meter'     => array(
            'columns'       => 'meterContract_idMeter',
            'refTableClass' => 'Power_Model_DbTable_Meter',
            'refColumns'    => 'meter_idMeter'
        ),
        'contract'  => array(
            'columns'       => 'meterContract_idContract',
            'refTableClass' => 'Power_Model_DbTable_Contract',
            'refColumns'    => 'contract_idContract'
        ),
        'meterType' => array(
            'columns'       => 'meter_type',
            'refTableClass' => 'Power_Model_DbTable_Tables',
            'refColumns'    => 'tables_key'
        ),
        'meterStatus' => array(
            'columns'       => 'meter_status',
            'refTableClass' => 'Power_Model_DbTable_Tables',
            'refColumns'    => 'tables_key'
        ),
        'user'      => array(
            'columns'       => array(
                'meterContract_userCreate',
                'meterContract_userModify'
            ),
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    public function getMeterContractById($idMeter, $idContract)
    {
        return $this->find($idMeter, $idContract)->current();
    }

    public function getMeterContractByContractId($id)
    {
        $select = $this->select()->where('meterContract_idContract = ?', $id);
        return $this->fetchAll($select);
    }

    protected function _getSearchMeterContractsSelect(array $search)
    {
        $select = $this->select(false)->setIntegrityCheck(false)
            ->from('meter')
            ->join('meter_contract', 'meter_idMeter = meterContract_idMeter')
            ->where('meterContract_idContract = ?', $search['meterContract_idContract']);

        return $select;
    }

    public function searchMeterContracts($search, $sort = '', $count = null, $offset = null)
    {
        $select = $this->_getSearchMeterContractsSelect($search)
            ->join('site', 'site_idSite = meter_idSite', null)
            ->join('client_address', 'clientAd_idAddress = site_idAddress', array(
                'clientAd_addressName','clientAd_address1','clientAd_address2','clientAd_address3','clientAd_postcode'
            ))
            ->join('client', 'client_idClient = site_idClient', null)
            ->joinLeft('client_contact', 'client_idClientContact = clientCo_idClientContact', array(
                'clientCo_name'
            ));
        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $select = $this->_getSearchMeterContractsSelect($search);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(array('numRows' => 'COUNT(meter_idMeter)'));
        $result = $this->fetchRow($select);

        return $result->numRows;
    }

    /**
     * Delete a row in the database.
     *
     * @param int $idMeter
     * @param int $idContract
     * @return int The number of rows deleted
     */
    public function deleteRow($idMeter, $idContract)
    {
        if (!is_numeric($idMeter) && !is_numeric($idContract)) {
            throw new ZendSF_Model_Exception('Could not delete row in ' . __CLASS__);
        }

        $row = $this->find($idMeter, $idContract)->current();
        return $row->delete();
    }

    public function insert(array $data)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['meterContract_dateCreate'] = new Zend_Db_Expr('CURDATE()');
        $data['meterContract_userCreate'] = $auth->getId();

        $this->_log->info(Zend_Debug::dump($data, "INSERT: " . __CLASS__, false));

        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['meterContract_dateModify'] = new Zend_Db_Expr('CURDATE()');
        $data['meterContract_userModify'] = $auth->getId();

        $this->_log->info(Zend_Debug::dump($data, "\nUPDATE: " . __CLASS__ . "\n", false));

        return parent::update($data, $where);
    }

    public function delete($where)
    {
        $this->_log->info(Zend_Debug::dump($where, "DELETE: " . __CLASS__, false));

        return parent::delete($where);
    }
}
