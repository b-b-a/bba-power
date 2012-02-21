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
                case 'meter':
                    $data = $this->_model->getMeterDataStore($request->getPost());
                    break;
                case 'contract':
                    $data = $this->_model->getMeterContractDataStore($request->getPost());
                    break;
                case 'usage':
                    $data = $this->_model->getUsageDataStore($request->getPost());
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
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if ($request->isXmlHttpRequest() && $request->getParam('type') == 'add'
                && $request->isPost()) {

            $form = $this->_getForm('meterSave', 'save-meter');
            $form->populate(array('meter_idSite' => $request->getParam('idSite')));

            $this->view->assign(array('meterSaveForm' => $form));

            $this->render('meter-form');
        } else {
            return $this->_helper->redirector('index', 'client');
        }
    }

    public function editMeterAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if ($request->getParam('idMeter') && $request->isPost()
                && $request->isXmlHttpRequest()) {

            $meter = $this->_model->getMeterDetailsById($request->getPost('idMeter'));

            $form = $this->_getForm('meterSave', 'save-meter');
            $form->populate($meter->toArray());

            $this->view->assign(array(
                'meter'         => $meter,
                'meterSaveForm' => $form
            ));

            if ($request->getPost('type') == 'edit') {
                if (!$this->_helper->acl('User')) {
                    throw new ZendSF_Acl_Exception('Access Denied');
                }
                $this->render('meter-form');
            }
        } else {
           return $this->_helper->redirector('index', 'site');
        }
    }

    public function saveMeterAction()
    {
        $request = $this->getRequest();

        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if (!$request->isPost() && !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'meter');
        }

        $saved = $this->_model->saveMeter($request->getPost());

        $returnJson = array('saved' => $saved);

        if (false === $saved) {
            $form = $this->_getForm('meterSave', 'save-meter');
            $form->populate($request->getPost());

            $this->view->assign(array('meterSaveForm' => $form));

            $html = $this->view->render('meter/meter-form.phtml');
            $returnJson['html'] = $html;
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }

    public function addUsageAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(true);

        if (!$this->_helper->acl('MeterReading')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if ($request->getParam('usage_idMeter') && $request->isXmlHttpRequest()
                && $request->isPost()) {

            $form = $this->_getForm('meterUsageSave', 'save-usage');
            $form->populate(array('usage_idMeter' => $request->getParam('usage_idMeter')));

            $this->view->assign(array('meterUsageSaveForm' => $form));
            $this->render('usage-form');
        } else {
            return $this->_helper->redirector('index', 'client');
        }
    }

    public function editUsageAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(true);

        if (!$this->_helper->acl('MeterReading')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if ($request->getParam('idUsage') && $request->isPost()
                && $request->isXmlHttpRequest()) {

            $usage = $this->_model->getUsageById($request->getParam('idUsage'));

            $form = $this->_getForm('meterUsageSave', 'save-usage');
            $form->populate($usage->toArray('dd/MM/yyyy'));

            $this->view->assign(array('meterUsageSaveForm' => $form));

            $this->render('usage-form');
        } else {
           return $this->_helper->redirector('index', 'client');
        }
    }

    public function saveUsageAction()
    {
        $request = $this->getRequest();

        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('MeterReading')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if (!$request->isPost() && !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'meter');
        }

        $saved = $this->_model->saveUsage($request->getPost());

        $returnJson = array('saved' => $saved);

        if (false === $saved) {
            $form = $this->_getForm('meterUsageSave', 'save-usage');
            $form->populate($request->getPost());

            $this->view->assign(array('meterUsageSaveForm' => $form));

            $html = $this->view->render('meter/usage-form.phtml');
            $returnJson['html'] = $html;
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
