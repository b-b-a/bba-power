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
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * DAO to represent a single Supplier.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Supplier extends ZendSF_Model_Acl_Abstract
{
    public function getSupplierById($id)
    {
        $id = (int) $id;
        return $this->getDbTable('supplier')->getSupplierById($id);
    }

    public function getSupplierContactById($id)
    {
        $id = (int) $id;
        return $this->getDbTable('supplierContact')->getSupplierContactById($id);
    }

    /**
     * Gets the supplier data store list, using search parameters.
     *
     * @param array $post
     * @return string JSON string
     */
    public function getSupplierDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $form = $this->getForm('supplierSearch');
        $search = array();

        if ($form->isValid($post)) {
            $search = $form->getValues();
        }

        $dataObj = $this->getDbTable('supplier')->searchSuppliers($search, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'supplier_idSupplier');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('supplier')->numRows($search)
        );

        return $store->toJson();
    }

    public function getSupplierContactDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $id = (int) $post['supplierCo_idSupplier'];

        $dataObj = $this->getDbTable('supplierContact')->searchContacts($id, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'supplierCo_idSupplierContact');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('supplierContact')->numRows($id)
        );

        return $store->toJson();
    }

    public function getSupplierContractDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $id = (int) $post['tender_idSupplier'];

        $dataObj = $this->getDbTable('supplier')
            ->getSupplierContractsBySupplierId($id, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'contract_idContract');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('supplier')
                ->getSupplierContractsBySupplierId($id)
                ->count()
        );

        return $store->toJson();
    }

    /**
     * Gets the data for filtering selects.
     *
     * @param array $param
     * @return string
     */
    public function getFileringSelectData($params)
    {
        switch ($params['type']) {
            case 'supplierList':
                $result = $this->getDbTable('supplier')->fetchAll(null, 'supplier_name ASC');
                $identifier = 'supplier_idSupplier';
                $searchItems = array('supplier_idSupplier', 'supplier_name');
                break;
        }

        $items = array();

        foreach ($result as $row) {
            $items[] = array(
                $identifier     => $row->{$searchItems[0]},
                $searchItems[1] => $row->{$searchItems[1]}
            );
        }

        $data = new Zend_Dojo_Data($identifier, $items);

        return $data->toJson();
    }

    public function saveSupplier($post)
    {
        if (!$this->checkAcl('saveSupplier')) {
            throw new ZendSF_Acl_Exception('Insufficient rights');
        }

        $form = $this->getForm('supplierSave');

        if (!$form->isValid($post)) {
            return false;
        }

        // get filtered values
        $data = $form->getValues();

        $supplier = array_key_exists('supplier_idSupplier', $data) ?
            $this->getSupplierById($data['supplier_idSupplier']) : null;

        return $this->getDbTable('supplier')->saveRow($data, $supplier);
    }

    public function saveSupplierContact($post)
    {
        if (!$this->checkAcl('saveSupplierContact')) {
            throw new ZendSF_Acl_Exception('Insufficient rights');
        }

        $form = $this->getForm('supplierContactSave');

        if ($post['type'] == 'edit') {
            $form->excludeEmailFromValidation('supplierCo_email', array(
                'field' => 'supplierCo_email',
                'value' => $this->getSupplierContactById($post['supplierCo_idSupplierContact'])
                    ->clientCo_email
            ));
        }

        if (!$form->isValid($post)) {
            return false;
        }

        // get filtered values
        $data = $form->getValues();

        $supplierCo = array_key_exists('supplierCo_idSupplierContact', $data) ?
            $this->getSupplierContactById($data['supplierCo_idSupplierContact']) : null;

        return $this->getDbTable('supplierContact')->saveRow($data, $supplierCo);
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
     * @return ZendSF_Model_Abstract
     */
    public function setAcl(Zend_Acl $acl)
    {
        parent::setAcl($acl);

        // implement rules here.
        $this->_acl->allow('user', $this)
            ->allow('admin', $this);

        return $this;
    }
}