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
class Power_Model_DbTable_Row_Contract extends ZendSF_Model_DbTable_Row_Abstract
{
    /**
     * @var Power_Model_DbTable_Row_Contract
     */
    protected $_contractPrevious;

    /**
     * @var Power_Model_DbTable_Row_Client
     */
    protected $_client;

    /**
     * @var Power_Model_DbTable_Row_Tender
     */
    protected $_tenderSelected;

    /**
     * @var Zend_Db_Table_Rowset
     */
    protected $_tenders;

    /**
     * Array of all columns with need date format applied
     * to it when outputting row as an array.
     *
     * @var array
     */
    protected $_dateKeys = array(
        'contract_dateStart',
        'contract_dateEnd',
        'contract_dateCreate',
        'contract_dateModify'
    );

    protected $_dateFormat = 'dd/MM/yyyy';

    public function getContractPrevious($row = null)
    {
        if (!$this->_contractPrevious instanceof Power_Model_DbTable_Row_Contract) {
            $this->_contractPrevious = $this->getRow()
                ->findParentRow('Power_Model_DbTable_Contract', 'contractPrevious');
        }

        return (null === $row) ? $this->_contractPrevious : $this->_contractPrevious->$row;
    }

    public function getClient($row = null)
    {
        if (!$this->_client instanceof Power_Model_DbTable_Row_Client) {
            $this->_client = $this->getRow()
                ->findParentRow('Power_Model_DbTable_Client', 'client');
        }

        return (null === $row) ? $this->_client : $this->_client->$row;
    }

    public function getTenderSelected($row = null)
    {
        if (!$this->_tenderSelected instanceof Power_Model_DbTable_Row_Tender) {
            $this->_tenderSelected = $this->getRow()
                ->findParentRow('Power_Model_DbTable_Tender', 'tenderSelected');
        }
        return (null === $row) ? $this->_tenderSelected : $this->_tenderSelected->$row;
    }

    public function getAllTenders()
    {
         if (!$this->_tenders instanceof Zend_Db_Table_Rowset_Abstract) {
            $this->_tenders = $this->getRow()
                ->findDependentRowset('Power_Model_DbTable_Tender', 'contract');
        }
        return $this->_tenders;
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
