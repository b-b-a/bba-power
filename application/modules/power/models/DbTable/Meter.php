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
class Power_Model_DbTable_Meter extends BBA_Model_DbTable_Abstract
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
        'site'          => array(
            'columns'       => 'meter_idSite',
            'refTableClass' => 'Power_Model_DbTable_Site',
            'refColumns'    => 'site_idSite'
        ),
        'meterType'     => array(
            'columns'       => 'meter_type',
            'refTableClass' => 'Power_Model_DbTable_Tables',
            'refColumns'    => 'tables_key'
        ),
        'meterStatus'   => array(
            'columns'       => 'meter_status',
            'refTableClass' => 'Power_Model_DbTable_Tables',
            'refColumns'    => 'tables_key'
        ),
        'userCreate'    => array(
            'columns'       => 'meter_userCreate',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        ),
        'userModify'    => array(
            'columns'       => 'meter_userModify',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    protected $_nullAllowed = array(
        'meter_userModify'
    );

    public function getMeterById($id)
    {
        return $this->find($id)->current();
    }
    
    public function getAvailableMeters(Power_Model_DbTable_Row_Contract $thisContract)
    {	
    	$idClient = $thisContract->contract_idClient;
    	$contractType = $thisContract->getContract_type(true);
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
    	
    	$select = $this->select(false)->setIntegrityCheck(false)
    		->from('meter', array(
    				'meter_idMeter',
    				'meter_type',
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
    		->where('meter_idMeter NOT IN (?)', new Zend_Db_Expr($subQuery2))
    		->order(array('contract_status_sort', 'contract_dateStart'));
    	
    	$log = Zend_Registry::get('log');
    	$log->info($select->__toString());
    	
    	return $this->fetchAll($select);
    }

    public function getMeterByNumberMain($numberMain, $ignoreMeter)
    {
        $numberMain = $this->_stripSpacesAndHyphens($numberMain);

        $select = $this->select();
        $select->where('meter_numberMain = ?', $numberMain);

        // if the ignoreMeter is set and the mpan number is equal to the meter mpan
        // being edited then filter out this meter mpan number.
        if (null !== $ignoreMeter && $numberMain === $ignoreMeter->getRow()->meter_numberMain) {
            $select->where('meter_numberMain != ?', $ignoreMeter->getRow()->meter_numberMain);
        }

        return $this->fetchRow($select);
    }
    
    public function getMeterByNumberSerial($numberSerial, $ignoreMeter)
    {
    	$select = $this->select();
    	$select->where('meter_numberSerial = ?', $numberSerial);
    
    	// if the ignoreMeter is set and the mpan number is equal to the meter mpan
    	// being edited then filter out this meter mpan number.
    	if (null !== $ignoreMeter && $numberSerial === $ignoreMeter->getRow()->meter_numberSerial) {
    		$select->where('meter_numberSerial != ?', $ignoreMeter->getRow()->meter_numberSerial);
    	}
    
    	return $this->fetchRow($select);
    }

    protected function _getSearchMetersSelect(array $search)
    {
        $select = $this->select(false)->setIntegrityCheck(false)
            ->from('meter', array(
                'meter_idMeter',
                'meter_type',
                'meter_status',
                'meter_numberTop',
                'meter_numberMain'
            ))
            ->join('site', 'site_idSite = meter_idSite', null)
            ->join('client_address', 'clientAd_idAddress = site_idAddress', array(
                'clientAd_addressName',
                'clientAd_postcode'
            ));

        if (!$search['meter'] == '') {
            if (substr($search['meter'], 0, 1) == '=') {
                $id = (int) substr($search['meter'], 1);
                $select->where('meter_idMeter = ?', $id);
            } else {
                $select->orWhere('meter_numberMain like ?', '%'. $this->_stripSpacesAndHyphens($search['meter']) . '%')
                    ->orWhere('meter_numberSerial like ?', '%'. $this->_stripSpacesAndHyphens($search['meter']) . '%');
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

        if (isset($search['idClient'])) {
            $select->where('site_idClient = ?', $search['idClient']);
        }

        return $select;
    }

    public function searchMeters(array $search, $sort = '', $count = null, $offset = null)
    {
        $select = $this->_getSearchMetersSelect($search)
            ->join('client', 'client_idClient = site_idClient', array(
                'client_name'
            ))
            ->joinLeft('meter_contract', 'meter_idMeter = meterContract_idMeter', array(
                'meterContract_kvaNominated',
                'meterContract_eac',
            ))
            ->joinLeft('contract', 'meterContract_idContract = contract_idContract', array(
                'contract_type' => $this->_getTablesValue('contract_type'),
                'contract_status' => $this->_getTablesValue('contract_status'),
                'contract_dateStart' => 'MAX(contract.contract_dateStart)',
                'contract_dateEnd'
            ))->group('meter_idMeter');
        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $select = $this->_getSearchMetersSelect($search);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(array('numRows' => 'COUNT(meter_idMeter)'));
        $result = $this->fetchRow($select);

        return $result->numRows;
    }

    public function insert(array $data)
    {
        if ($data['meter_type'] == 'electric') {
            $data['meter_numberMain'] = $this->_stripSpacesAndHyphens($data['meter_numberMain']);
            $data['meter_numberTop'] = $this->_stripSpacesAndHyphens($data['meter_numberTop']);
        }

        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        if ($data['meter_type'] == 'electric') {
            $data['meter_numberMain'] = $this->_stripSpacesAndHyphens($data['meter_numberMain']);
            $data['meter_numberTop'] = $this->_stripSpacesAndHyphens($data['meter_numberTop']);
        }

        return parent::update($data, $where);
    }
}
