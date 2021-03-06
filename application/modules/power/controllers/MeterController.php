<?php
/**
 * MeterController.php
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
 * Controller Class MeterController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_MeterController extends Zend_Controller_Action
{
    /**
     * @var Power_Model_Meter
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        $this->_model = new Power_Model_Meter();
    }

    /**
     * Checks if user is logged, if not then forwards to login.
     *
     * @return Zend_Controller_Action::_forward
     */
    public function preDispatch()
    {
        if (!$this->_helper->acl('Meter', 'view')) {
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
                case 'meter':
                    $data = $this->_model->getCached('meter')
                    	->getMeterDataStore($request->getPost());
                    break;
                case 'contract':
                    $data = $this->_model->getCached('meterContract')
                    	->getMeterContractDataStore($request->getPost());
                    break;
                case 'usage':
                    $data = $this->_model->getCached('usage')
                    	->getUsageDataStore($request->getPost());
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
        if (!$this->_helper->acl('Meter', 'view')) {
            throw new BBA_Power_Acl_Exception('Access Denied');
        }
        
        $urlHelper = $this->_helper->getHelper('url');
        $form = $this->_model->getForm('meterSearch');
        $form->populate($this->getRequest()->getPost());

        $form->setAction($urlHelper->url(array(
            'controller'    => 'meter' ,
            'action'        => 'index',
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');

        // assign search to the view script.
        $this->view->assign(array(
            'search'            => Zend_Json::encode($form->getValues()),
            'meterSearchForm'   => $form
        ));
    }

    public function addMeterAction()
    {
        if (!$this->_helper->acl('Meter', 'add')) {
            throw new BBA_Power_Acl_Exception('Access Denied');
        }
        
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if ($request->isXmlHttpRequest() && $request->getParam('type') == 'add'
                && $request->isPost()) {

            $form = $this->_getForm('meterAdd', 'save-meter');
            $form->populate($request->getPost());

            $this->view->assign(array(
            	'type'		=> 'Edit',
            	'meterForm' => $form
            ));

            $this->render('meter-form');
        } else {
            return $this->_helper->redirector('index', 'meter');
        }
    }

    public function editMeterAction()
    {
        if (!$this->_helper->acl('Meter', 'view')) {
            throw new BBA_Power_Acl_Exception('Access Denied');
        }
        
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if ($request->getParam('meter_idMeter') && $request->isPost()
                && $request->isXmlHttpRequest()) {

            $meter = $this->_model->getMeterById($request->getPost('meter_idMeter'));
            
            $defaultValues = $meter->toArray(true);
            $defaultValues['meter_typeName'] = $meter->meter_type;

            $form = $this->_getForm('meterEdit', 'save-meter');
            $form->populate($defaultValues);

            $this->view->assign(array(
            	'type'		=> 'Edit',
                'meter'     => $meter,
                'meterForm' => $form
            ));

            if ($request->getPost('type') == 'edit') {
                if (!$this->_helper->acl('Meter', 'edit')) {
                    throw new BBA_Power_Acl_Exception('Access Denied');
                }
                $this->render('meter-form');
            }
        } else {
           return $this->_helper->redirector('index', 'meter');
        }
    }

    public function printMeterAction()
    {
        if (!$this->_helper->acl('Meter', 'view')) {
            throw new BBA_Power_Acl_Exception('Access Denied');
        }
        
        $request = $this->getRequest();
        $this->_helper->layout->setLayout('print');

        if ($request->getParam('meter_idMeter') && $request->isPost()) {

            $meter = $this->_model->getMeterById($request->getPost('meter_idMeter'));

            $this->view->assign(array(
                'meter' => $meter
            ));
        } else {
           return $this->_helper->redirector('index', 'meter');
        }
    }

    public function saveMeterAction()
    {
        $request = $this->getRequest();

        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$request->isPost() && !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'meter');
        }
        
        $action = $request->getPost('type');

        try {
        	
            $saved = $this->_model->{$action . 'Meter'}($request->getPost());

            $returnJson = array('saved' => $saved);

            if (false === $saved) {
                $form = $this->_getForm('meter' . ucfirst($action), 'save-meter');
                $form->populate($request->getPost());

                $this->view->assign(array('meterForm' => $form));

                $html = $this->view->render('meter/meter-form.phtml');
                $returnJson['html'] = $html;
            } else {
                $this->view->assign(array(
                    'id'    => $saved,
                    'type'  => 'meter'
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

    public function addUsageAction()
    {
        if (!$this->_helper->acl('MeterUsage', 'add')) {
            throw new BBA_Power_Acl_Exception('Access Denied');
        }
        
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(true);

        if ($request->getParam('usage_idMeter') && $request->isXmlHttpRequest()
                && $request->isPost()) {

            $form = $this->_getForm('meterUsageSave', 'save-usage');
            $form->populate($request->getPost());

            $this->view->assign(array('meterUsageSaveForm' => $form));
            $this->render('usage-form');
        } else {
            return $this->_helper->redirector('index', 'meter');
        }
    }

    public function editUsageAction()
    {
        if (!$this->_helper->acl('MeterUsage', 'edit')) {
            throw new BBA_Power_Acl_Exception('Access Denied');
        }
        
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(true);

        if ($request->getParam('usage_idUsage') && $request->isPost()
                && $request->isXmlHttpRequest()) {

            $usage = $this->_model->getUsageById($request->getParam('usage_idUsage'));

            $form = $this->_getForm('meterUsageSave', 'save-usage');
            $form->populate($usage->toArray(true));

            $this->view->assign(array('meterUsageSaveForm' => $form));

            $this->render('usage-form');
        } else {
           return $this->_helper->redirector('index', 'meter');
        }
    }

    public function saveUsageAction()
    {
        $request = $this->getRequest();

        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$request->isPost() && !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'meter');
        }

        try {
            
            $saved = $this->_model->saveUsage($request->getPost());

            $returnJson = array('saved' => $saved);

            if (false === $saved) {
                $form = $this->_getForm('meterUsageSave', 'save-usage');
                $form->populate($request->getPost());

                $this->view->assign(array('meterUsageSaveForm' => $form));

                $html = $this->view->render('meter/usage-form.phtml');
                $returnJson['html'] = $html;
            } else {
                $this->view->assign(array(
                    'id'    => $saved,
                    'type'  => 'usage'
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
            'controller'    => 'meter',
            'action'        => $action,
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');
        return $form;
    }
}
