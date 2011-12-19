<?php
/**
 * Tender.php
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
 * Database class for the Tender table row.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable_Row
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Row_Tender extends ZendSF_Model_DbTable_Row_Abstract
{
    /**
     * @var Power_Model_DbTable_Row_Supplier
     */
    protected $_supplier;

    /**
     * @var Power_Model_DbTable_Row_Contract
     */
    protected $_contract;

    protected $_dateKeys = array(
        'tender_dateExpiresQuote',
        'tender_dateCreate',
        'tender_dateModify'
    );

    protected $_dateFormat = 'dd/MM/yyyy';

    public function getSupplier($col = null)
    {
        if (!$this->_supplier instanceof Power_Model_DbTable_Row_Supplier) {
            $this->_supplier = $this->getRow()
                ->findParentRow('Power_Model_DbTable_Supplier', 'supplier');
        }

        return (null === $col) ? $this->_supplier : $this->_supplier->$col;
    }

    public function getContract($col = null)
    {
        if (!$this->_contract instanceof Power_Model_DbTable_Row_Contract) {
            $this->_contract = $this->getRow()
                ->findParentRow('Power_Model_DbTable_Contract', 'contract');
        }

        return (null === $col) ? $this->_contract : $this->_contract->$col;
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
