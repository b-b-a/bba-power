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
    }

    /**
     * Checks if user is logged, if not then forwards to login.
     *
     * @return Zend_Controller_Action::_forward
     */
    public function preDispatch()
    {
        if ($this->_helper->acl('Guest')) {
            return $this->_forward('login', 'auth');
        }
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
                case 'personnel':
                    $data = $this->_model->getSupplierPersonnelDataStore($request->getPost());
                    break;
                case 'contract':
                    $data = $this->_model->getSupplierContractDataStore($request->getPost());
                    break;
                case 'supplierList':
                case 'supplierPersonnel':
                    $data = $this->_model->getFileringSelectData($request->getParams());
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

    public function addSupplierAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if ($request->isXmlHttpRequest() && $request->getPost('type') == 'add'
                && $request->isPost()) {

            $form = $this->_getForm('supplierSave', 'save-supplier');

            $this->view->assign(array('supplierSaveForm' => $form));

            $this->render('supplier-form');
        } else {
            return $this->_helper->redirector('index', 'supplier');
        }
    }

    public function editSupplierAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if ($request->getParam('supplier_idSupplier') && $request->isPost()
                && $request->isXmlHttpRequest()) {

            $supplier = $this->_model->getSupplierById($request->getPost('supplier_idSupplier'));

            $this->view->assign(array('supplier' => $supplier));

            if ($this->_request->getParam('type') == 'edit') {
                if (!$this->_helper->acl('User')) {
                    throw new ZendSF_Acl_Exception('Access Denied');
                }
                $form = $this->_getForm('supplierSave', 'save-supplier');
                $form->populate($supplier->toArray());
                $this->view->assign(array('supplierSaveForm' => $form));
                $this->render('supplier-form');
            }
        } else {
           return $this->_helper->redirector('index', 'supplier');
        }
    }

    public function saveSupplierAction()
    {
        $request = $this->getRequest();

        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if (!$request->isPost() && !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'supplier');
        }

        try {
            $saved = $this->_model->saveSupplier($request->getPost());

            $returnJson = array(
                'saved' => $saved
            );

            if (false === $saved) {
                $form = $this->_getForm('supplierSave', 'save-supplier');
                $form->populate($request->getPost());
                $this->view->assign(array('supplierSaveForm' => $form));
                $html = $this->view->render('supplier/supplier-form.phtml');
                $returnJson['html'] = $html;
            } else {
                $this->view->assign(array(
                    'id'    => $saved,
                    'type'  => 'supplier'
                ));
                $html = $this->view->render('confirm.phtml');
                $returnJson['html'] = $html;
            }
        } catch (Exception $e) {
            $log = Zend_Registry::get('log');
            $log->err($e);
            $this->view->assign(array(
                'message' => $e
            ));
            $html = $this->view->render('error/error.phtml');
            $returnJson = array(
                'html'  => $html,
                'saved' => false,
                'error' => true
            );
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }

    public function addSupplierPersonnelAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if ($request->isXmlHttpRequest() && $request->getParam('type') == 'add'
                && $request->isPost()) {
            $form = $this->_getForm('supplierPersonnelSave', 'save-supplier-personnel');
            $form->populate($request->getPost());

            $this->view->assign(array('supplierPersonnelSaveForm' => $form));

            $this->render('supplier-personnel-form');
        } else {
            return $this->_helper->redirector('index', 'client');
        }
    }

    public function editSupplierPersonnelAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if ($request->getPost('supplierPers_idSupplierPersonnel') && $request->isXmlHttpRequest()
                && $request->isPost()) {

            $supplierPers = $this->_model->getSupplierPersonnelById($request->getPost('supplierPers_idSupplierPersonnel'));

            $form = $this->_getForm('supplierPersonnelSave', 'save-supplier-personnel');
            $form->populate($supplierPers->toArray());

            $this->view->assign(array(
                'supplierPersonnelSaveForm' => $form
            ));

            $this->render('supplier-personnel-form');

        } else {
           return $this->_helper->redirector('index', 'client');
        }
    }

    public function saveSupplierPersonnelAction()
    {
        $request = $this->getRequest();

        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if (!$request->isPost() && !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'supplier');
        }

        try {
            $saved = $this->_model->saveSupplierPersonnel($request->getPost());

            $returnJson = array(
                'saved' => $saved
            );

            if (false === $saved) {
                $form = $this->_getForm('supplierPersonnelSave', 'save-supplier-personnel');
                $form->populate($request->getPost());
                $this->view->assign(array('supplierPersonnelSaveForm' => $form));
                $html = $this->view->render('supplier/supplier-personnel-form.phtml');
                $returnJson['html'] = $html;
            } else {
                $this->view->assign(array(
                    'id'    => $saved,
                    'type'  => 'supplier personnel'
                ));
                $html = $this->view->render('confirm.phtml');
                $returnJson['html'] = $html;
            }
        } catch (Exception $e) {
            $log = Zend_Registry::get('log');
            $log->err($e);
            $this->view->assign(array(
                'message' => $e
            ));
            $html = $this->view->render('error/error.phtml');
            $returnJson = array(
                'html'  => $html,
                'saved' => false,
                'error' => true
            );
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
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
