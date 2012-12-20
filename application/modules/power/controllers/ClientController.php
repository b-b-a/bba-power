<?php
/**
 * ClientController.php
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
 * Controller Class ClientController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_ClientController extends Zend_Controller_Action
{
    /**
     * @var Power_Model_Client
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        $this->_model = new Power_Model_Client();
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
                case 'client':
                    $data = $this->_model->getCached('client')
                    	->getClientDataStore($request->getPost());
                    break;
                case 'address':
                    $data = $this->_model->getCached('clientAddress')
                    	->getClientAddressDataStore($request->getPost());
                    break;
                case 'personnel':
                    $data = $this->_model->getCached('clientPersonnel')
                    	->getClientPersonnelDataStore($request->getPost());
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

    public function indexAction()
    {
        $urlHelper = $this->_helper->getHelper('url');
        $form = $this->_model->getForm('clientSearch')
            ->populate($this->getRequest()->getPost());

        $form->setAction($urlHelper->url(array(
            'controller'    => 'client' ,
            'action'        => 'index',
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');

        // assign search to the view script.
        $this->view->assign(array(
            'search'            => Zend_Json::encode($form->getValues()),
            'clientSearchForm'  => $form
        ));
    }

    public function addClientAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new Power_Model_Acl_Exception('Access Denied');
        }

        if ($request->isXmlHttpRequest() && $request->getPost('type') == 'add'
                && $request->isPost()) {

            $form = $this->_getForm('clientAdd', 'save-client');
            $docForm = $this->_getForm('docClient', 'save-contract');

            $this->view->assign(array(
                'clientForm' 	=> $form,
                'docForm'       => $docForm
            ));

            $this->render('add-client-form');
        } else {
            return $this->_helper->redirector('index', 'client');
        }
    }

    public function editClientAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if ($request->getParam('client_idClient') && $request->isPost()
                && $request->isXmlHttpRequest()) {

            $client = $this->_model->getClientById($request->getPost('client_idClient'));

            $form = $this->_getForm('clientEdit', 'save-client');
            $form->populate($client->toArray(true));

            $this->view->assign(array(
                'client'        => $client,
                'clientForm'    => $form
            ));

            if ($this->_request->getParam('type') == 'edit') {
                if (!$this->_helper->acl('User')) {
                    throw new Power_Model_Acl_Exception('Access Denied');
                }
                $this->render('edit-client-form');
            }
        } else {
           return $this->_helper->redirector('index', 'client');
        }
    }

    public function saveClientAction()
    {
        $request = $this->getRequest();

        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new Power_Model_Acl_Exception('Access Denied');
        }

        if (!$request->isPost()) {
            return $this->_helper->redirector('index', 'client');
        }

        try {
            $saved = ($request->getPost('type') === 'add') ?
                $this->_model->saveNewClient($request->getPost()) :
                $this->_model->saveClient($request->getPost());

            if (is_array($saved)) {
                $site = $saved[1];
                $saved = $saved[0];
            }

            $returnJson = array('saved' => $saved);

            if (false === $saved) {
                $type = ($request->getPost('type') == 'add') ? 'Add' : 'Edit';

                $form = $this->_getForm('client' . $type, 'save-client');
                $form->populate($request->getPost());
                
                $docForm = $this->_getForm('docClient', 'save-client');
                $docForm->populate($request->getPost());

                $this->view->assign(array(
                    'clientForm'   => $form
                ));
                $html = $this->view->render('client/'. $request->getPost('type') .'-client-form.phtml');
                $returnJson['html'] = $html;
            } else {
                $this->view->assign(array(
                    'id'    => $saved,
                    'type'  => 'client',
                    'site'  => (isset($site)) ? $site : null
                ));
                $html = $this->view->render('client/confirm.phtml');
                $returnJson['html'] = $html;
                if ($request->getParam('client_idClient')) {
                    $returnJson['client_idClient'] = $request->getParam('client_idClient');
                }
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
            ->setHeader('Content-Type', 'text/html')
            ->setBody('<textarea>' . json_encode($returnJson) . '</textarea>');
    }
    
    public function checkLoaDateAction()
    {
    	$request = $this->getRequest();
    	
    	$this->getHelper('viewRenderer')->setNoRender(true);
    	$this->_helper->layout->disableLayout();
    	
    	if (!$this->_helper->acl('User')) {
    		throw new Power_Model_Acl_Exception('Access Denied');
    	}
    	
    	if (!$request->isPost()) {
    		return $this->_helper->redirector('index', 'client');
    	}
    	
    	try {
    		//new Zend_Date($dateValue, Zend_Date::DATE_SHORT);
    		$newDate = new Zend_Date($request->getParam('newDate', '1970-01-01'), Zend_Date::DATE_SHORT);
    		$oldDate = new Zend_Date($request->getParam('oldDate', '1970-01-01'), Zend_Date::DATE_SHORT);
    		
    		//$log = Zend_Registry::get('log');
    		//$log->info('checkLoaDateAction:newDate:'.$newDate);
    		//$log->info('checkLoaDateAction:oldDate:'.$oldDate);
    		
    		//if newDate is not grater than oldDate validate form.
    		// || !$newDate->equals($oldDate)
    		if ($oldDate->isEarlier($newDate)) {
    			$returnJson = array('test' => 'pass');
    		} else {
    			$returnJson = array('test' => 'fail');
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
                'error' => true
            );
        }
        
        $this->getResponse()
        	->setHeader('Content-Type', 'text/html')
        	->setBody(json_encode($returnJson));
    }

    public function addClientAddressAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new Power_Model_Acl_Exception('Access Denied');
        }

        if ($request->getPost('clientAd_idClient') && $request->isXmlHttpRequest()
                && $request->getPost('type') == 'add' && $request->isPost()) {

            $form = $this->_getForm('clientAddressSave', 'save-client-address');
            $form->populate($request->getPost());

            $this->view->assign(array('clientAddressSaveForm' => $form));

            $this->render('address-form');
        }
    }

    public function editClientAddressAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if ($request->getPost('clientAd_idAddress') && $request->isXmlHttpRequest()
                && $request->isPost()) {

            $clientAd = $this->_model->getClientAddressById($request->getPost('clientAd_idAddress'));

            $form = $this->_getForm('clientAddressSave', 'save-client-address');
            $values = $clientAd->toArray(true);
            $values['site_idSite'] = $request->getPost('site_idSite');
            $form->populate($values);

            $this->view->assign(array(
                'clientAd'              => $clientAd,
                'clientAddressSaveForm' => $form
            ));

            if ($this->_request->getParam('type') == 'edit') {
                if (!$this->_helper->acl('User')) {
                    throw new Power_Model_Acl_Exception('Access Denied');
                }
                $this->render('address-form');
            }

        } else {
           return $this->_helper->redirector('index', 'client');
        }
    }
    
    public function checkAddressDuplicatesAction()
    {
    	$request = $this->getRequest();
    	 
    	if (!$request->isPost() && !$request->isXmlHttpRequest()) {
    		return $this->_helper->redirector('index', 'client');
    	}
    	 
    	$this->getHelper('viewRenderer')->setNoRender(true);
    	$this->_helper->layout->disableLayout();
    	 
    	$dups = $this->_model->checkDuplicateAddresses($request->getPost());
    	 
    	$returnJson = array();
    	 
    	if ($dups) {
    		$returnJson['dups'] = true;
    
    		$this->view->assign(array(
    				'dups'  => $dups,
    		));
    
    		$html = $this->view->render('client/check-address-duplicates.phtml');
    		$returnJson['html'] = $html;
    	} else {
    		$returnJson['dups'] = false;
    	}
    	 
    	$this->getResponse()
    		->setHeader('Content-Type', 'application/json')
    		->setBody(json_encode($returnJson));
    }

    public function saveClientAddressAction()
    {
        $request = $this->getRequest();

        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new Power_Model_Acl_Exception('Access Denied');
        }

        if (!$request->isPost()&& !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'client');
        }

        try {
            $saved = $this->_model->saveClientAddress($request->getPost());

            $returnJson = array('saved' => $saved);

            if (false === $saved) {
                $form = $this->_getForm('clientAddressSave', 'save-client-address');
                $form->populate($request->getPost());
                $this->view->assign(array('clientAddressSaveForm' => $form));
                $html = $this->view->render('client/address-form.phtml');
                $returnJson['html'] = $html;
            } else {
                $this->view->assign(array(
                    'id'    => $saved,
                    'type'  => 'client address'
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

    public function addClientPersonnelAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(true);

        if (!$this->_helper->acl('User')) {
            throw new Power_Model_Acl_Exception('Access Denied');
        }

        if ($request->getPost('clientPers_idClient') && $request->isXmlHttpRequest()
                && $request->getPost('type') == 'add' && $request->isPost()) {

            $form = $this->_getForm('clientPersonnelSave', 'save-client-personnel');
            $form->populate($request->getPost());

            $this->view->assign(array('clientPersonnelSaveForm' => $form));

            $this->render('personnel-form');
        }
    }

    public function editClientPersonnelAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(true);

        if (!$this->_helper->acl('User')) {
            throw new Power_Model_Acl_Exception('Access Denied');
        }

        if ($request->getPost('clientPers_idClientPersonnel') && $request->isXmlHttpRequest()
                && $request->isPost()) {

            $clientPers = $this->_model->getClientPersonnelById($request->getParam('clientPers_idClientPersonnel'));

            $form = $this->_getForm('clientPersonnelSave', 'save-client-personnel');
            $form->populate($clientPers->toArray(true));

            $this->view->assign(array('clientPersonnelSaveForm' => $form));

            $this->render('Personnel-form');

        } else {
           return $this->_helper->redirector('index', 'client');
        }
    }
    
    public function checkEmailDuplicatesAction()
    {
    	$request = $this->getRequest();
    
    	if (!$request->isPost() && !$request->isXmlHttpRequest()) {
    		return $this->_helper->redirector('index', 'client');
    	}
    
    	$this->getHelper('viewRenderer')->setNoRender(true);
    	$this->_helper->layout->disableLayout();
    
    	$dups = $this->_model->checkDuplicateEmails($request->getPost());
    
    	$returnJson = array();
    
    	if ($dups) {
    		$returnJson['dups'] = true;
    
    		$this->view->assign(array(
    				'dups'  => $dups,
    		));
    
    		$html = $this->view->render('client/check-email-duplicates.phtml');
    		$returnJson['html'] = $html;
    	} else {
    		$returnJson['dups'] = false;
    	}
    
    	$this->getResponse()
    		->setHeader('Content-Type', 'application/json')
    		->setBody(json_encode($returnJson));
    }

    public function saveClientPersonnelAction()
    {
        $request = $this->getRequest();

        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new Power_Model_Acl_Exception('Access Denied');
        }

        if (!$request->isPost()&& !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'client');
        }

        try {
            $saved = $this->_model->saveClientPersonnel($request->getPost());

            $returnJson = array('saved' => $saved);

            if (false === $saved) {
                $form = $this->_getForm('clientPersonnelSave', 'save-client-personnel');
                $form->populate($request->getPost());
                $this->view->assign(array('clientPersonnelSaveForm' => $form));
                $html = $this->view->render('client/personnel-form.phtml');
                $returnJson['html'] = $html;
            } else {
                $this->view->assign(array(
                    'id'    => $saved,
                    'type'  => 'client personnel'
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
            'controller'    => 'client',
            'action'        => $action,
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');
        return $form;
    }
}
