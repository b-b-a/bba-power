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
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Invoice Model.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Invoice extends ZendSF_Model_Acl_Abstract
{
    /**
     * Get Invoice by their id
     *
     * @param  int $id
     * @return null|Power_Model_DbTable_Row_Invoice
     */
    public function getInvoiceById($id)
    {
        if (!$this->checkAcl('getInvoiceById')) {
            throw new ZendSF_Acl_Exception('Insufficient rights');
        }

        $id = (int) $id;
        return $this->getDbTable('invoice')->getInvoiceById($id);
    }

    public function getInvoiceLineById($id)
    {
        if (!$this->checkAcl('getInvoiceById')) {
            throw new ZendSF_Acl_Exception('Insufficient rights');
        }

        $id = (int) $id;
        return $this->getDbTable('invoiceLine')->getInvoiceLineById($id);
    }

    /**
     * Gets the invoice data store list, using search parameters.
     *
     * @param array $post
     * @return string JSON string
     */
    public function getInvoiceDataStore(array $post)
    {
        if (!$this->checkAcl('getInvoiceById')) {
            throw new ZendSF_Acl_Exception('Insufficient rights');
        }

        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $form = $this->getForm('invoiceSearch');
        $search = array();

        if ($form->isValid($post)) {
            $search = $form->getValues();
        }

        $dataObj = $this->getDbTable('invoice')->searchInvoice($search, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'invoice_idInvoice');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('invoice')->numRows($search)
        );

        return $store->toJson();
    }

    public function getInvoiceLinesDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $dataObj = $this->getDbTable('invoiceLine')->searchInvoiceLines($post, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'invoiceLine_idInvoiceLine');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('invoiceLine')->numRows($post)
        );

        return $store->toJson();
    }

    public function getInvoiceUsageDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $dataObj = $this->getDbTable('invoiceUsage')->searchInvoiceUsage($post, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'invoiceUsage_idInvoiceUsage');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('invoiceUsage')->numRows($post)
        );

        return $store->toJson();
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
        $this->_acl->allow('admin', $this);

        return $this;
    }
}