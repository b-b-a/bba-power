<?php
/**
 * Client.php
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
 * Database adapter class for the Client table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable_Row
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Row_Client extends Power_Model_DbTable_Row_Abstract
{
    /**
     * Array of all columns with need date format applied
     * to it when outputting row as an array.
     *
     * @var array
     */
    protected $_dateKeys = array(
        'client_dateExpiryLoa',
        'client_dateCreate',
        'client_dateModify'
    );
    
    public function getShortDesc()
    {
        $desc = $this->getRow()->client_desc;
        
        if (strlen($desc) > 200) {
            $desc = substr($desc, 0, 200);
        }
        
        return $desc;
    }

    public function getClientPersonnel()
    {
        return $this->getRow()->findParentRow(
            'Power_Model_DbTable_Client_Personnel',
            'clientPers'
        );
    }

    public function getClientAddress()
    {
        return $this->getRow()->findParentRow(
            'Power_Model_DbTable_Client_Address',
            'clientAd'
        );
    }
    
    public function getClientRegAddress()
    {
    	return $this->getRow()->findParentRow(
    			'Power_Model_DbTable_Client_Address',
    			'clientRegAd'
    	);
    }

    public function getAllClientAddresses($select = null)
    {
        return $this->getRow()->findDependentRowset(
            'Power_Model_DbTable_Client_Address',
            'client',
            $select
        );
    }
    
    public function getClient_methodPay()
    {
    	return $this->getRow()->findParentRow(
    			'Power_Model_DbTable_Tables',
    			'methodPay',
    			$this->getRow()->select()->where('tables_name = ?', 'client_methodPay')
    	)->tables_value;
    }

    /**
     * Returns row as an array, with optional date formating.
     *
     * @param string $dateFormat
     * @return array
     */
    public function toArray($raw=false)
    {
        $array = array();

        foreach ($this->getRow() as $key => $value) {

            if (in_array($key, $this->_dateKeys)) {
                $date = new Zend_Date($value);
                $value = $date->toString($this->_dateFormat);
            }

            if (true === $raw) {
            	if ($value == 'Not a Registered Company') {
            		$array['client_registeredCompany'] = 1;
            	} elseif ($value == 'Not VAT Registered') {
                    $array['client_registeredVAT'] = 1;
                }
                
               	$array[$key] = $value;
            } else {
                switch ($key) {
                    case 'client_desc':
                        $array[$key] = $this->getShortDesc();
                        break;
                    default:
                        $array[$key] = $value;
                        break;
                }
            }
        }

        return $array;
    }
}
