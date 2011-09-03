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
class Power_Model_Mapper_Supplier extends ZendSF_Model_Mapper_Acl_Abstract
{
   /**
     * @var string the DbTable class name
     */
    protected $_dbTableClass = 'Power_Model_DbTable_Supplier';

    /**
     * @var sting the model class name
     */
    protected $_modelClass = 'Power_Model_Supplier';

    public function supplierSearch($search, $paged = null)
    {
        $select = $this->getDbTable()->getSupplierList();

        if (!$search['supplier'] == '') {
            $select
                ->where('supplier_name like ?', '%' . $search['supplier'] . '%')
                ->orWhere('supplier_address1 like ?', '%' . $search['supplier'] . '%')
                ->orWhere('supplier_address2 like ?', '%' . $search['supplier'] . '%')
                ->orWhere('supplier_address3 like ?', '%' . $search['supplier'] . '%')
                ->orWhere('supplier_postcode like ?', '%' . $search['supplier'] . '%');
        }

        if (!$search['contact'] == '') {
            $select
                ->where('supplierCo_name like ?', '%' . $search['contact'] . '%')
                ->orWhere('supplierCo_email like ?', '%' . $search['contact'] . '%')
                ->orWhere('supplierCo_address1 like ?', '%' . $search['contact'] . '%')
                ->orWhere('supplierCo_address2 like ?', '%' . $search['contact'] . '%')
                ->orWhere('supplierCo_address3 like ?', '%' . $search['contact'] . '%')
                ->orWhere('supplierCo_postcode like ?', '%' . $search['contact'] . '%');
        }

        return $this->listSuppliers($paged, $select);
    }

    public function listSuppliers($paged = null, $select = null)
    {
        if ($select === null) {
            $select = $this->getDbTable()->getSupplierList();
        }

        if (null !== $paged) {
            $numDisplay = Zend_Registry::get('config')
                ->layout
                ->supplier
                ->paginate
                ->itemCountPerPage;

            return $this->_paginate($select, $paged, $numDisplay);
        } else {
            return $this->fetchAll($select);
        }
    }

    public function getContactsBySupplierId($id)
    {
        $supplier = $this->find($id, true);

        $contactsRowset = $supplier->findDependentRowset(
            'Power_Model_DbTable_SupplierContact',
            'supplier'
        );

        $entries = array();

        foreach ($contactsRowset as $row) {
			$entries[] = new Power_Model_SupplierContact($row);
        }

        return $entries;
    }

    public function getContractsBySupplierId($id)
    {
        $supplier = $this->find($id, true);

        $contractsRowset = $supplier->findManyToManyRowset(
            'Power_Model_DbTable_Contract',
            'Power_Model_DbTable_Tender',
            'supplier',
            'contract'
        );

        $entries = array();

        foreach ($contractsRowset as $row) {
			$entries[] = new Power_Model_Contract($row);
        }

        return $entries;
    }

    public function save()
    {
        if (!$this->checkAcl('save')) {
            throw new ZendSF_Acl_Exception('saving suppliers is not allowed.');
        }

        $form = $this->getForm('supplierSave')->getValues();

        // remove client id if not set.
        if (!$form['supplier_idSupplier']) unset($form['supplier_idSupplier']);

        $model = new Power_Model_Supplier($form);

        // set create date and user.
        if ($form['returnAction'] == 'add') {
            $model->setDateCreate();
            $model->userCreate = $form['userId'];
        }

        // add modified date/user if updating record.
        if ($model->getId()) {
            $model->userModify = $form['userId'];
            $model->setDateModify();
        }

        return parent::save($model);
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

    /**
     * Injector for the acl, the acl can be injected directly
     * via this method.
     *
     * We add all the access rules for this resource here, so we first call
     * parent method to add $this as the resource then we
     * define it rules here.
     *
     * @param Zend_Acl_Resource_Interface $acl
     * @return ZendSF_Model_Mapper_Abstract
     */
    public function setAcl(Zend_Acl $acl)
    {
        parent::setAcl($acl);

        // implement rules here.
        $this->_acl->allow('admin', $this)
            ->deny('admin', $this, array('delete'));

        return $this;
    }

}
