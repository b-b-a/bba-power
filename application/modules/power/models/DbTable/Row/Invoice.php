<?php
/**
 * Invoice.php
 *
 * Copyright (c) 2012 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of BBA Power.
 *
 * BBA Power is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BBA Power is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BBA Power.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable_Row
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Database class for the Invoice table row.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable_Row
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Row_Invoice extends ZendSF_Model_DbTable_Row_Abstract
{
    /**
     * @var Power_Model_DbTable_Row_Supplier
     */
    protected $_supplier;

    /**
     * Array of all columns with need date format applied
     * to it when outputting row as an array.
     *
     * @var array
     */
    protected $_dateKeys = array(
        'invoice_dateInvoice',
        'invoice_dateStart',
        'invoice_dateEnd',
        'invoice_dateCreated'
    );

    /**
     * @var string
     */
    protected $_dateFormat = 'dd/MM/yyyy';

    /**
     * Gets supplier parent row.
     *
     * @param string $row
     * @return string|Power_Model_DbTable_Row_Supplier
     */
    public function getSupplier($row = null)
    {
        if (!$this->_supplier instanceof Power_Model_DbTable_Row_Supplier) {
            $this->_supplier = $this->getRow()
                ->findParentRow('Power_Model_DbTable_Supplier', 'supplier');
        }

        return (null === $row) ? $this->_supplier : $this->_supplier->$row;
    }

    /**
     * Returns row as an array, with optional date formating.
     *
     * @param string $dateFormat
     * @return array
     */
    public function toArray($dateFormat = null, $raw = false)
    {
        $array = array();

        foreach ($this->getRow() as $key => $value) {

            if (in_array($key, $this->_dateKeys)) {
                $date = new Zend_Date($value);
                $value = $date->toString((null === $dateFormat) ? $this->_dateFormat : $dateFormat);
            }

            if (true === $raw) {
                $array[$key] = $value;
            } else {
                switch($key) {
                    case 'invoice_nameSupplier':
                        $supplier = $this->getSupplier();

                        if (strtolower($supplier->supplier_name) === 'unknown') {
                            $array[$key] = $this->invoice_nameSupplier;
                        } else {
                            $array[$key] = $supplier->supplier_nameShort;
                        }
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
