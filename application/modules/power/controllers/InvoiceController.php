<?php
/**
 * InvoiceController.php
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
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Controller Class InvoiceController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_InvoiceController extends Zend_Controller_Action
{
    /**
     * @var Power_Model_Invoice
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        $this->_model = new Power_Model_Invoice();
    }

    /**
     * Checks if user is logged, if not then forwards to login.
     *
     * @return Zend_Controller_Action::_forward
     */
    public function preDispatch()
    {
        if (!$this->_helper->acl('Invoice', 'view')) {
            throw new BBA_Power_Acl_Exception('Access Denied');
        }
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        if (!$this->_helper->acl('Invoice', 'view')) {
            throw new BBA_Power_Acl_Exception('Access Denied');
        }
        
        $urlHelper = $this->_helper->getHelper('url');
        $form = $this->_model->getForm('invoiceSearch')
            ->populate($this->_request->getPost());

        $form->setAction($urlHelper->url(array(
            'controller'    => 'invoice' ,
            'action'        => 'index',
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');

        // assign search to the view script.
        $this->view->assign(array(
            'search' => Zend_Json::encode($form->getValues()),
            'invoiceSearchForm'  => $form
        ));
    }

    public function invoiceAction()
    {

        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();
        
        if (!$this->_helper->acl('Invoice', 'view')) {
            throw new BBA_Power_Acl_Exception('Access Denied');
        }

        if ($request->getParam('invoice_idInvoice') && $request->isPost()
                && $request->isXmlHttpRequest()) {

            $inv = $this->_model->getInvoiceById($request->getPost('invoice_idInvoice'));

            $this->view->assign(array(
                'invoice' => $inv
            ));
        } else {
           return $this->_helper->redirector('index', 'invoice');
        }
    }

    public function invoiceLineAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();
        
        if (!$this->_helper->acl('InvoiceLine', 'view')) {
            throw new BBA_Power_Acl_Exception('Access Denied');
        }

        if ($request->getParam('invoiceLine_idInvoiceLine') && $request->isPost()
                && $request->isXmlHttpRequest()) {

            $invLi = $this->_model->getInvoiceLineById($request->getPost('invoiceLine_idInvoiceLine'));

            $this->view->assign(array(
                'invoiceLine' => $invLi
            ));
        } else {
           return $this->_helper->redirector('index', 'invoice');
        }
    }

    public function dataStoreAction()
    {
        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            switch ($request->getParam('type')) {
                case 'invoice':
                    $data = $this->_model->getCached('invoice')
                    	->getInvoiceDataStore($request->getPost());
                    break;
                case 'invoice-lines':
                    $data = $this->_model->getCached('invoiceLines')
                    	->getInvoiceLinesDataStore($request->getPost());
                    break;
                case 'invoice-usage':
                    $data = $this->_model->getCached('invoiceUsage')
                    	->getInvoiceUsageDataStore($request->getPost());
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
}
