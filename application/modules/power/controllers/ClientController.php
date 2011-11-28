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

    public function clientStoreAction()
    {
        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $data = $this->_model->getClientDataStore($this->_request->getPost());

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody($data);
    }

    public function indexAction()
    {
        $urlHelper = $this->_helper->getHelper('url');
        $form = $this->_model->getForm('clientSearch')
            ->populate($this->_request->getPost());

        $form->setAction($urlHelper->url(array(
            'controller'    => 'client' ,
            'action'        => 'index',
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');

        // assign search to the view script.
        $this->view->assign(array(
            'search' => Zend_Json::encode($form->getValues()),
            'clientSearchForm'  => $form
        ));
    }

    public function clientAddAction()
    {
        if ($this->_request->isXmlHttpRequest()
                && $this->_request->getParam('type') == 'add'
                && $this->_request->isPost()) {

            $this->_helper->layout->disableLayout();

            $form = $this->_getClientForm('clientAdd');

            $this->view->assign(array('clientSaveForm' => $form));

            $this->render('client-add-form');
        } else {
            return $this->_helper->redirector('index', 'client');
        }
    }

    public function clientEditAction()
    {
        if ($this->_request->getParam('idClient')
                && $this->_request->isPost()
                && $this->_request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $client = $this->_model->getClientById($this->_request->getParam('idClient'));

            $form = $this->_getClientForm('clientSave');
            $form->populate($client->toArray('dd/MM/yyyy'));

            $this->view->assign(array(
                'client'            => $client,
                'clientSaveForm'    => $form
            ));

            if ($this->_request->getParam('type') == 'edit') {
                $this->render('client-edit-form');
            }
        } else {
           return $this->_helper->redirector('index', 'client');
        }
    }

    public function clientSaveAction()
    {
        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$this->_request->isPost()&& !$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'client');
        }

        $request = $this->_request->getPost();

        $saved = $this->_model->saveClient($request);

        $returnJson = array('saved' => $saved);

        if ($saved == 0) {
            $html = $this->view->render('client/client-'. $request['type'] .'-form.phtml');
            $returnJson['html'] = $html;
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }

    private function _getClientForm($action)
    {
        $urlHelper = $this->_helper->getHelper('url');
        $form = $this->_model->getForm($action);

        $form->setAction($urlHelper->url(array(
            'controller'    => 'client',
            'action'        => 'client-save',
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');
        return $form;
    }
}
