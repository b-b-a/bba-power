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
                    $data = $this->_model->getClientDataStore($request->getPost());
                    break;
                case 'address':
                    $data = $this->_model->getClientAddressDataStore($request->getPost());
                    break;
                case 'contact':
                    $data = $this->_model->getClientContactDataStore($request->getPost());
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
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if ($request->isXmlHttpRequest() && $request->getPost('type') == 'add'
                && $request->isPost()) {

            $form = $this->_getForm('clientAdd', 'save-client');

            $this->view->assign(array('clientAddForm' => $form));

            $this->render('add-client-form');
        } else {
            return $this->_helper->redirector('index', 'client');
        }
    }

    public function editClientAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if ($request->getParam('idClient') && $request->isPost()
                && $request->isXmlHttpRequest()) {

            $client = $this->_model->getClientById($request->getPost('idClient'));

            $form = $this->_getForm('clientSave', 'save-client');
            $form->populate($client->toArray('dd/MM/yyyy'));

            $this->view->assign(array(
                'client'            => $client,
                'clientSaveForm'    => $form
            ));

            if ($this->_request->getParam('type') == 'edit') {
                if (!$this->_helper->acl('User')) {
                    throw new ZendSF_Acl_Exception('Access Denied');
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
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if (!$request->isPost()&& !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'client');
        }

        $saved = ($request->getPost('type') === 'add') ?
            $this->_model->saveNewClient($request->getPost()) :
            $this->_model->saveClient($request->getPost());

        $returnJson = array('saved' => $saved);

        if (false === $saved) {
            $type = ($request->getPost('type') == 'add') ? 'Add' : 'Save';

            $form = $this->_getForm('client' . $type, 'save-client');
            $form->populate($request->getPost());

            $this->view->assign(array('client' . $type . 'Form' => $form));
            $html = $this->view->render('client/'. $request->getPost('type') .'-client-form.phtml');
            $returnJson['html'] = $html;
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }

    public function addClientAddressAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
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

        if ($request->getPost('idAddress') && $request->isXmlHttpRequest()
                && $request->isPost()) {

            $clientAd = $this->_model->getClientAddressById($request->getPost('idAddress'));

            $form = $this->_getForm('clientAddressSave', 'save-client-address');
            $form->populate($clientAd->toArray());

            $this->view->assign(array(
                'clientAd'              => $clientAd,
                'clientAddressSaveForm' => $form
            ));

            if ($this->_request->getParam('type') == 'edit') {
                if (!$this->_helper->acl('User')) {
                    throw new ZendSF_Acl_Exception('Access Denied');
                }
                $this->render('address-form');
            }

        } else {
           return $this->_helper->redirector('index', 'client');
        }
    }

    public function saveClientAddressAction()
    {
        $request = $this->getRequest();

        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if (!$request->isPost()&& !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'client');
        }

        $saved = $this->_model->saveClientAddress($request->getPost());

        $returnJson = array('saved' => $saved);

        if (false === $saved) {
            $form = $this->_getForm('clientAddressSave', 'save-client-address');
            $form->populate($request->getPost());
            $this->view->assign(array('clientAddressSaveForm' => $form));
            $html = $this->view->render('client/address-form.phtml');
            $returnJson['html'] = $html;
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }

    public function addClientContactAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(true);

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if ($request->getPost('clientCo_idClient') && $request->isXmlHttpRequest()
                && $request->getPost('type') == 'add' && $request->isPost()) {

            $form = $this->_getForm('clientContactSave', 'save-client-contact');
            $form->populate($request->getPost());

            $this->view->assign(array('clientContactSaveForm' => $form));

            $this->render('contact-form');
        }
    }

    public function editClientContactAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(true);

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if ($request->getPost('idClientContact') && $request->isXmlHttpRequest()
                && $request->isPost()) {

            $clientCo = $this->_model->getClientContactById($request->getParam('idClientContact'));

            $form = $this->_getForm('clientContactSave', 'save-client-contact');
            $form->populate($clientCo->toArray());

            $this->view->assign(array('clientContactSaveForm' => $form));

            $this->render('contact-form');

        } else {
           return $this->_helper->redirector('index', 'client');
        }
    }

    public function saveClientContactAction()
    {
        $request = $this->getRequest();

        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if (!$request->isPost()&& !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'client');
        }

        $saved = $this->_model->saveClientContact($request->getPost());

        $returnJson = array('saved' => $saved);

        if (false === $saved) {
            $form = $this->_getForm('clientContactSave', 'save-client-contact');
            $form->populate($request->getPost());
            $this->view->assign(array('clientContactSaveForm' => $form));
            $html = $this->view->render('client/contact-form.phtml');
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
            'controller'    => 'client',
            'action'        => $action,
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');
        return $form;
    }
}
