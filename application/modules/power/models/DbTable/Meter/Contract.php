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
class Power_Model_DbTable_Meter_Contract extends Power_Model_DbTable_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'meter_contract';

    /**
     * @var array compound primary key
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
        'userCreate'    => array(
            'columns'       => 'meterContract_userCreate',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        ),
        'userModify'    => array(
            'columns'       => 'meterContract_userModify',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    protected $_nullAllowed = array(
        'meterContract_userModify'
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
    
    public function getAvailableMeters(Power_Model_DbTable_Row_Contract $thisContract)
    {
    	$idClient = $thisContract->contract_idClient;
    	$contractType = $thisContract->contract_type;
    	$newContractStartDate = $thisContract->contract_dateStart;
    	$thisContract = $thisContract->contract_idContract;
    	 
    	$meterType = explode('-', $contractType);
    	$meterType = $meterType[0];
    	 
    	$subQuery1 = $this->select(false)->setIntegrityCheck(false)
    		->from('meter', array('meter_idMeter'))
    		->joinLeft('meter_contract', 'meter_idMeter = meterContract_idMeter', null)
    		->joinLeft('contract', 'meterContract_idContract = contract_idContract', null)
    		->where('contract_idClient = ?', $idClient)
    		->where('meter_type = ?', $meterType)
    		->where('contract_status IN ("signed", "selected", "choose")')
    		->where('CAST("' . $newContractStartDate . '" AS DATE) BETWEEN contract_dateStart AND contract_dateEnd');
    
    	$subQuery2 = $this->select(false)->setIntegrityCheck(false)
    		->from('meter', array('meter_idMeter'))
    		->joinLeft('meter_contract', 'meter_idMeter = meterContract_idMeter', null)
    		->joinLeft('contract', 'meterContract_idContract = contract_idContract', null)
    		->where('contract_idContract = ?', $thisContract);
    	 
    	$query1 = $this->select(false)->setIntegrityCheck(false)
    		->from('meter', array(
    			'meter_idMeter',
    			'meter_type' => $this->_getTablesValue('meter_type'),
    			'meter_status' => $this->_getTablesValue('meter_status'),
    			'meter_numberMain'
    		))
    		->joinLeft('site', 'site_idSite = meter_idSite', null)
    		->joinLeft('meter_contract', 'meterContract_idMeter = meter_idMeter', array(
    			'meterContract_contractLatest',
    			'meterContract_kvaNominated'
    		))
    		->joinLeft('contract', 'contract_idContract = meterContract_idContract', array(
    			'contract_idContract',
    			'contract_type' => $this->_getTablesValue('contract_type'),
    			'contract_status' => $this->_getTablesValue('contract_status'),
    			'contract_dateStart',
    			'contract_dateEnd'
    		))
    		->joinLeft(
    			array('contract_status_table' => 'tables'),
    			'tables_name = "contract_status" AND tables_key = contract_status',
    			array('contract_status_sort' => 'tables_sort')
    		)
    		->where('site_idClient = ?', $idClient)
    		->where('meter_type = ?', $meterType)
    		->where('meter_status NOT IN ("dis", "old")')
    		->where('((meterContract_contractLatest = TRUE')
    		->where('contract_dateEnd < ?', $newContractStartDate)
    		->where('contract_type = ?)', $contractType)
    		->orWhere('contract_idContract IS NULL)')
    		->where('meter_idMeter NOT IN (?)', new Zend_Db_Expr($subQuery1))
    		->where('meter_idMeter NOT IN (?)', new Zend_Db_Expr($subQuery2));
    	 
    	$query2 = clone $query1;
    	$query2->reset(Zend_Db_Select::WHERE);
    	$query2->where('contract_idContract = ?', $thisContract);
    
    	$select = $this->select()
    		->union(array($query1, $query2))
    		->order(array('contract_status_sort', 'contract_dateStart'));
    	 
    	return $this->fetchAll($select);
    }

    protected function _getSearchMeterContractsSelect(array $search)
    {
        $select = $this->select(false)->setIntegrityCheck(false)
            ->from('meter', array(
            	'meter_idMeter',
            	'meter_type' => $this->_getTablesValue('meter_type'),
            	'meter_status' => $this->_getTablesValue('meter_status'),
            	'meter_numberTop',
            	'meter_numberMain'
            ))
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
            ->joinLeft('client_personnel', 'client_idClientPersonnel = clientPers_idClientPersonnel', array(
                'clientPers_name'
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
    
    public function getAllContractsByMeterId($post)
    {
    	$sort = (string) $post['sort'];
    	$count = (int) $post['count'];
    	$offset = (int) $post['start'];
    	$meterId = (int) $post['meter_idMeter'];
    	
    	$select = $this->select(false)->setIntegrityCheck(false)
    		->from('meter_contract')
    		->joinCross('contract', array(
    			'contract_idContract',
    			'contract_reference',
    			'contract_type' => $this->_getTablesValue('contract_type'),
    			'contract_status' => $this->_getTablesValue('contract_status'),
    			'contract_dateStart',
    			'contract_dateEnd',
    			'contract_desc' => 'SUBSTR(contract_desc, 1, 40)'
    		))
    		->joinCross('client', array('client_name'))
    		->where('contract_idContract = meterContract_idContract')
    		->where('contract_idClient = client_idClient')
    		->where('meterContract_idMeter = ?', $meterId);
    	
    	$select = $this->getLimit($select, $count, $offset);
    	$select = $this->getSortOrder($select, $sort);
    	
    	return $this->fetchAll($select);
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
            throw new Power_Model_Exception('Could not delete row in ' . __CLASS__);
        }

        $row = $this->find($idMeter, $idContract)->current();
        return $row->delete();
    }
}
