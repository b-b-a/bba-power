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
class Power_Model_DbTable_Row_Meter extends ZendSF_Model_DbTable_Row_Abstract
{
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

    protected $_dateFormat = 'dd/MM/yyyy';

    public function getCurrentContract()
    {
        // find the most recent contract.
        $select = $this->getRow()->select(false)->setIntegrityCheck(false)
            ->from('meter_contract')
            ->columns(array(
                'meterContract_kvaNominated'
            ))
            ->where('meter_contract.meterContract_idMeter = ?', $this->getRow()->meter_idMeter)
            ->order('contract_dateStart DESC')->limit(1);

        return $this->getRow()->findManyToManyRowset(
            'Power_Model_DbTable_Contract',
            'Power_Model_DbTable_Meter_Contract',
            'meter',
            'contract',
            $select
        )->current();
    }

    /**
     * Returns row as an array, with optional date formating.
     *
     * @param string $dateFormat
     * @return array
     */
    public function toArray($dateFormat = null)
    {
        $array = array();

        foreach ($this->getRow() as $key => $value) {

            if (in_array($key, $this->_dateKeys)) {
                $date = new Zend_Date($value);
                $value = $date->toString((null === $dateFormat) ? $this->_dateFormat : $dateFormat);
            }

            $array[$key] = $value;
        }

        return $array;
    }
}
