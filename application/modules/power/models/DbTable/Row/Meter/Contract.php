<?php
/**
 * Contract.php
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
 * Database class for the Contract table row.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable_Row
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Row_Meter_Contract extends ZendSF_Model_DbTable_Row_Abstract
{
    protected $_meter;

    public function getMeter($row = null)
    {
        if (!$this->_meter instanceof Power_Model_DbTable_Row_Meter) {
            $this->_meter = $this->getRow()
                ->findParentRow('Power_Model_DbTable_Meter', 'meter');
        }
        return (null === $row) ? $this->_meter : $this->_meter->$row;
    }

    public function getMeter_numberMain()
    {
        if ($this->getRow()->meter_type == 'gas') {
            return $this->getRow()->meter_numberMain;
        }

        $regex = '/^([0-9]{2})([0-9]{4})([0-9]{4})([0-9]{3})$/';
        preg_match($regex, $this->getRow()->meter_numberMain, $matches);

        if (count($matches) == 5) {
            unset($matches[0]);
            return implode(' ', $matches);
        } else {
            return $this->getRow()->meter_numberMain;
        }

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
            if ($key == 'meter_numberMain') {
                $array[$key] = $this->getMeter_numberMain();
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }
}
