<?php
/**
 * SupplierController.php
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
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Controller Class SupplierController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_SupplierController extends Zend_Controller_Action
{
    /**
     * @var Power_Model_Supplier
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {

        $this->_model = new Power_Model_Supplier();
/*
        $this->setForm('supplierSave', array(
            'controller' => 'supplier' ,
            'action' => 'save',
            'module' => 'power'
        ));

        // search form
        $this->setForm('supplierSearch', array(
            'controller' => 'supplier' ,
            'action' => 'index',
            'module' => 'power'
        ));

        $this->_setSearch(array(
            'supplier', 'contact'
        ));*/
    }

    public function dataStoreAction()
    {
        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            switch ($request->getParam('type')) {
                case 'suppliers':
                    $data = $this->_model->getSupplierDataStore($request->getPost());
                    break;
                default :
                    $data = '{}';
                    break;
            }

            $this->getResponse()
                ->setHeader('Content-Type', 'application/json')
                ->setBody($data);
        }
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $urlHelper = $this->_helper->getHelper('url');
        $form = $this->_model->getForm('supplierSearch')
            ->populate($this->getRequest()->getPost());

        $form->setAction($urlHelper->url(array(
            'controller'    => 'supplier' ,
            'action'        => 'index',
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');

        // assign search to the view script.
        $this->view->assign(array(
            'search'                => Zend_Json::encode($form->getValues()),
            'supplierSearchForm'    => $form
        ));
    }

    public function supplierContactStoreAction()
    {
        unset($this->_search);
        $this->_setSearch(array(
            'supplierCo_idSupplier'
        ));
        return $this->_getAjaxDataStore('getContactsBySupplierId' ,'supplierCo_idSupplierContact', true);
    }

    public function supplierContractStoreAction()
    {
        unset($this->_search);
        $this->_setSearch(array(
            'tender_idSupplier'
        ));
        return $this->_getAjaxDataStore('getContractsBySupplierId' ,'contract_idContract', true);
    }

    public function addSupplierAction()
    {
        if ($this->_request->isXmlHttpRequest()
                && $this->_request->getParam('type') == 'add'
                && $this->_request->isPost()) {
            $this->getForm('supplierSave');
            $this->render('ajax-form');
        } else {
            return $this->_helper->redirector('index', 'client');
        }
    }

    public function editSupplierAction()
    {
        if ($this->_request->getParam('idSupplier')
                && $this->_request->isPost()
                && $this->_request->isXmlHttpRequest()) {

            $supplier = $this->_model->find($this->_request->getParam('idSupplier'));

            $this->getForm('supplierSave')
                ->populate($supplier->toArray('dd/MM/yyyy'));

            $this->view->assign(array(
                'supplier' => $supplier
            ));

            if ($this->_request->getParam('type') == 'edit') {
                $this->render('ajax-form');
            }
        } else {
           return $this->_helper->redirector('index', 'client');
        }
    }

    public function saveSupplierAction()
    {
        if (!$this->_request->isPost() && !$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'supplier');
        }

        $this->_helper->viewRenderer->setNoRender(true);

        if (!$this->getForm('supplierSave')->isValid($this->_request->getPost())) {
            $html = $this->view->render('supplier/ajax-form.phtml');

            $returnJson = array(
                'saved' => 0,
                'html'  => $html
            );
        } else {
            $saved = $this->_model->save('supplierSave');

            $returnJson = array(
                'saved' => $saved
            );

            if ($saved == 0) {
                $html = $this->view->render('supplier/ajax-form.phtml');
                $returnJson['html'] = $html;
            }
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }

    public function autocompleteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(true);

        switch ($this->_request->getParam('param')) {
            case 'supplier':
                $identifier = 'supplier_idSupplier';
                $searchItems = array('idSupplier', 'name');
                $result = $this->_model->fetchAll();
                break;
        }

        $items = array();

        foreach ($result as $row) {
            $items[] = array(
                $identifier                     => $row->{$searchItems[0]},
                $row->prefix . $searchItems[1]  => $row->{$searchItems[1]}
            );
        }

        $data = new Zend_Dojo_Data($identifier, $items);

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody($data->toJson());
    }

    private function _getForm($name, $action)
    {
        $urlHelper = $this->_helper->getHelper('url');
        $form = $this->_model->getForm($name);

        $form->setAction($urlHelper->url(array(
            'controller'    => 'supplier',
            'action'        => $action,
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');
        return $form;
    }
}
