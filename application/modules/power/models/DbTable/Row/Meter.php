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
 * @subpackage Model_DbTable_Row
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Database class for the Meter table row.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable_Row
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Row_Meter extends Power_Model_DbTable_Row_Abstract
{
    /**
     * @var Power_Model_DbTable_Row_Site
     */
    protected $_site;

    /**
     * Array of all columns with need date format applied
     * to it when outputting row as an array.
     *
     * @var array
     */
    protected $_dateKeys = array(
        'contract_dateEnd',
        'meter_dateCreate',
        'meter_dateModify'
    );
    
    public function getShortDesc()
    {
    	$desc = $this->getRow()->meter_desc;
    
    	if (strlen($desc) > 200) {
    		$desc = substr($desc, 0, 200);
    	}
    
    	return $desc;
    }

    public function getMeter_numberMain($raw=false)
    {
    	if (!$raw) {
	    	if (!BBA_Utility_String::startsWith('electric', $this->getRow()->meter_type)) {
	            return $this->getRow()->meter_numberMain;
	        }
	        
	        $regex = '/^([0-9]{2})([0-9]{4})([0-9]{4})([0-9]{3})$/';
	        preg_match($regex, $this->getRow()->meter_numberMain, $matches);
	
	        if (count($matches) == 5) {
	            unset($matches[0]);
	            return implode(' ', $matches);
	        }
    	}
    	
    	return $this->getRow()->meter_numberMain;

    }

    public function getMeter_numberTop($raw=false)
    {
    	if (!$raw) {
	        if (!BBA_Utility_String::startsWith('electric', $this->getRow()->meter_type)) {
	            return $this->getRow()->meter_numberTop;
	        }
	
	        $regex = '/^([0-9]{2})([0-9]{3})([0-9]{3})$/';
	        preg_match($regex, $this->getRow()->meter_numberTop, $matches);
	
	        if (count($matches) == 4) {
	            unset($matches[0]);
	            return implode(' ', $matches);
	        }
        }
        
        return $this->getRow()->meter_numberTop;
    }
    
    public function getMeter_COTPending($raw=false)
    {
    	if (!$raw) {
    		return ($this->getRow()->meter_COTPending) ? 'True' : 'False';
    	}
    	
    	return $this->getRow()->meter_COTPending;
    }

    public function getSite($row=null)
    {
        if (!$this->_site instanceof Power_Model_DbTable_Row_Site) {
            $this->_site = $this->getRow()
                ->findParentRow( 'Power_Model_DbTable_Site', 'site');
        }

        return (null === $row) ? $this->_site : $this->_site->$row;
    }

    public function getCurrentContract()
    {
        // find the most recent contract.
        $select = $this->getRow()->select()
        	->where('meter_contract.meterContract_contractLatest IS TRUE');

        return $this->getAllContracts($select)->current();
    }

    public function getAllContracts($select = null)
    {
        $select->joinCross('client')
            ->joinCross('meter_contract')
            ->where('meter_contract.meterContract_idContract = m.contract_idContract')
            ->where('meter_contract.meterContract_idMeter = ?', $this->getRow()->meter_idMeter)
            ->where('m.contract_idClient = client_idClient');

        return $this->getRow()->findManyToManyRowset(
            'Power_Model_DbTable_Contract',
            'Power_Model_DbTable_Meter_Contract',
            'meter',
            'contract',
            $select
        );
    }

    public function getMeterType()
    {
        return $this->getRow()->findParentRow(
            'Power_Model_DbTable_Tables',
            'meterType',
            $this->getRow()->select()->where('tables_name = ?', 'meter_type')
        )->tables_value;
    }

    public function getMeterStatus()
    {
        return $this->getRow()->findParentRow(
            'Power_Model_DbTable_Tables',
            'meterStatus',
            $this->getRow()->select()->where('tables_name = ?', 'meter_status')
        )->tables_value;
    }
}
