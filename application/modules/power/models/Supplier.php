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

    /**
     * Gets the data for filtering selects.
     *
     * @param array $param
     * @return string
     */
    public function getFileringSelectData($params)
    {
        switch ($params['type']) {

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
}