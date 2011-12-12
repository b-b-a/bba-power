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
class Power_Model_DbTable_Meter_Contract extends Zend_Db_Table_Abstract
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
        'user'      => array(
            'columns'       => array(
                'meterContract_userCreate',
                'meterContract_userModify'
            ),
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    public function geMetertContractById($id)
    {
        return $this->find($id)->current();
    }

    /**
     * Selects all availiable meters that are not on current contract by the contract id.
     * Query example:
     *
     * <code>
     * SELECT meter_idMeter, meter_type, meter_numberMain, meterContract_kvaNominated,
     * contract_idContract, contract_idContractPrevious, contract_type, contract_status,
     * MAX(contract_dateStart) AS contract_dateStart, contract_dateEnd
     * FROM meter
     * LEFT JOIN meter_contract ON meter_idMeter = meterContract_idMeter
     * LEFT JOIN contract ON meterContract_idContract = contract_idContract
     * WHERE meter_idSite IN (
     *     SELECT site_idSite
     *     FROM contract
     *     JOIN site ON contract_idClient = site_idClient
     *     WHERE contract_idContract = 1708)
     * AND meter_idMeter NOT IN (
     *     SELECT meterContract_idMeter
     *     FROM meter_contract
     *     WHERE meterContract_idContract = 1708)
     * AND meter_type = (
     *     SELECT SUBSTRING_INDEX(contract_type,'-',1)
     *     FROM contract
     *     WHERE contract_idContract = 1708)
     * AND (contract_dateEnd < (
     *         SELECT contract_dateStart
     *         FROM contract
     *         WHERE contract_idContract = 1708)
     *     AND contract_status IN ('current', 'signed', 'selected', 'choose')
     *     OR contract_idContract IS NULL)
     * GROUP BY meter_numberMain
     * Order by contract_idContract, meter_numberMain;
     * </code>
     *
     * @param string $id
     * @return array
     */
    public function getAvailableClientMetersByContractId($id)
    {
        $log = Zend_Registry::get('log');

        // optimised query to get all availiable meter in one query.
        $select = $this->getDbTable()->select(false)->setIntegrityCheck(false)
            ->from('meter', array(
                'meter_idMeter', 'meter_type', 'meter_numberMain'
            ))
            ->joinLeft('meter_contract', 'meter_idMeter = meterContract_idMeter', array(
                'meterContract_kvaNominated'
            ))
            ->joinLeft('contract', 'meterContract_idContract = contract_idContract', array(
                'contract_idContract',
                'contract_idContractPrevious',
                'contract_type',
                'contract_status',
                'contract_dateStart' => 'MAX(contract_dateStart)',
                'contract_dateEnd'
            ))
            ->where('meter_idSite IN (?)', new Zend_Db_Expr(
                $this->getDbTable()->select(false)->setIntegrityCheck(false)
                    ->from('contract', null)
                    ->join('site', 'contract_idClient = site_idClient', array(
                        'site_idSite'
                    ))
                    ->where('contract_idContract = ?', $id)
            ))
            ->where('meter_idMeter NOT IN (?)', new Zend_Db_Expr(
                $this->getDbTable()->select(false)->setIntegrityCheck(false)
                    ->from('meter_contract', array('meterContract_idMeter'))
                    ->where('meterContract_idContract = ?', $id)
            ))
            ->where('meter_type = (?)', new Zend_Db_Expr(
                $this->getDbTable()->select(false)->setIntegrityCheck(false)
                    ->from('contract', array(
                        'contract_type' => 'SUBSTRING_INDEX(contract_type,\'-\',1)'
                    ))
                    ->where('contract_idContract = ?', $id)
            ))
            ->where('(contract_dateEnd < (?)', new Zend_Db_Expr(
                $this->getDbTable()->select(false)->setIntegrityCheck(false)
                    ->from('contract', array('contract_dateStart'))
                    ->where('contract_idContract = ?', $id)
            ))
            ->where('contract_status IN (?)', array(
                'current', 'signed', 'selected', 'choose'
            ))
            ->orWhere('contract_idContract IS NULL)')
            ->group('meter_numberMain')
            ->order(array('contract_idContract', 'meter_numberMain'));

        //$log->info($select->__toString());
        $meters = $this->fetchAll($select);

        return $meters;
    }

    public function searchMeterContracts($search, $sort = '', $count = null, $offset = null)
    {

    }

    public function numRows($search)
    {
        $result = $this->searchContracts($search);
        return $result->count();
    }

    public function insert(array $data)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['meterContract_dateCreate'] = new Zend_Db_Expr('CURDATE()');
        $data['meterContract_userCreate'] = $auth->getId();
        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['meterContract_dateModify'] = new Zend_Db_Expr('CURDATE()');
        $data['meterContract_userModify'] = $auth->getId();
        return parent::update($data, $where);
    }
}
