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
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Mapper Class for Supplier.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Mapper_Supplier extends BBA_Model_Mapper_Abstract
{
   /**
     * @var string the DbTable class name
     */
    protected $_dbTableClass = 'Power_Model_DbTable_Supplier';

    /**
     * @var sting the model class name
     */
    protected $_modelClass = 'Power_Model_Supplier';

    public function numRows($search, $child = false)
    {
        if ($child) {
            if (isset($search['tender_idSupplier'])) {
                $entries = $this->getContractsBySupplierId($search);
            } else {
                $entries = $this->getContactsBySupplierId($search);
            }

            return count($entries);

        } else {
            return parent::numRows($search, $child);
        }
    }

    public function getContactsBySupplierId($search, $sort = '', $count = null, $offset = null)
    {
        $col = key($search);
        $id = current($search);

         $select = $this->getDbTable()
            ->select();
            //->where($col . ' = ?', $id);

        if ($count && $offset) $select = $this->getLimit($select, $count, $offset);

        if ($sort) $select = $this->getSort($select, $sort);

        $supplier = $this->find($id, true);

        $contactsRowset = $supplier->findDependentRowset(
            'Power_Model_DbTable_SupplierContact',
            'supplier',
            $select
        );

        $entries = array();

        foreach ($contactsRowset as $row) {
			$entries[] = new Power_Model_SupplierContact($row);
        }

        return $entries;
    }

    public function getContractsBySupplierId($search, $sort = '', $count = null, $offset = null)
    {
        $col = key($search);
        $id = current($search);

         $select = $this->getDbTable()
            ->select();
            //->where($col . ' = ?', $id);

        if ($count && $offset) $select = $this->getLimit($select, $count, $offset);

        if ($sort) $select = $this->getSort($select, $sort);

        $supplier = $this->find($id, true);

        $contractsRowset = $supplier->findManyToManyRowset(
            'Power_Model_DbTable_Contract',
            'Power_Model_DbTable_Tender',
            'supplier',
            'contract',
            $select
        );

        $entries = array();

        $contractDb = new Power_Model_DbTable_Contract();
        $cols = $contractDb->info('cols');

        foreach ($contractsRowset as $row) {
            $model = new Power_Model_Contract($row);
            $model->setCols($cols);
			$entries[] = $model;
        }

        return $entries;
    }

    public function save()
    {
        if (!$this->checkAcl('save')) {
            throw new ZendSF_Acl_Exception('saving suppliers is not allowed.');
        }

        return parent::save('supplierSave');
    }

    public function delete($id)
    {
        if (!$this->checkAcl('delete')) {
            throw new ZendSF_Acl_Exception('Deleting suppliers is not allowed.');
        }

        $where = $this->getDbTable()
            ->getAdapter()
            ->quoteInto('supplier_idSupplier = ?', $id);

        return parent::delete($where);
    }
}
