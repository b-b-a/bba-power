<?php

/**
 * Supplier.php
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
 * Database class for the Supplier table row.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable_Row
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Row_Supplier extends Power_Model_DbTable_Row_Abstract
{
    public function getFullAddress()
    {
        return $this->supplier_address1 . '<br />'
             . $this->supplier_address2 . '<br />'
             . $this->supplier_address3 . '<br />'
             . $this->supplier_postcode . '<br />';
    }

    public function getSupplierPersonnel()
    {
        return $this->getRow()->findParentRow(
            'Power_Model_DbTable_Supplier_Personnel',
            'supplierPers'
        );
    }

    public function getAllSupplierPersonnel()
    {
        return $this->getRow()->findDependentRowset(
            'Power_Model_DbTable_Supplier_Personnel',
            'supplier'
        );
    }

    public function getFullPersonnelAddress()
    {
        $row = $this->getSupplierPersonnel();

        if ($row) {
            $address = $row->supplierPers_name . '<br />'
                . $row->supplierPers_address1 . '<br />'
                . $row->supplierPers_address2 . '<br />'
                . $row->supplierPers_address3 . '<br />'
                . $row->supplierPers_postcode . '<br />'
                . $row->supplierPers_phone . '<br />'
                . $row->getMailto() . '<br />';
        } else {
            $address = 'No main contact found';
        }

        return $address;
    }

    public function getContracts($select = null)
    {
        return $this->getRow()->findManyToManyRowset(
            'Power_Model_DbTable_Contract',
            'Power_Model_DbTable_Tender',
            'supplier',
            'contract',
            $select
        );
    }
}
