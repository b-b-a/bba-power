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
class Power_ClientController extends BBA_Controller_Action_Abstract
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
        parent::init();

        if (!$this->_helper->acl('Guest')) {

            $this->_model = new Power_Model_Client();

            $this->setForm('clientSave', array(
                'controller'    => 'client' ,
                'action'        => 'save',
                'module'        => 'power'
            ));

            $this->setForm('clientAdd', array(
                'controller'    => 'client' ,
                'action'        => 'save',
                'module'        => 'power'
            ));

            // search form
            $this->setForm('clientSearch', array(
                'controller'    => 'client' ,
                'action'        => 'index',
                'module'        => 'power'
            ));

            $this->_setSearch(array(
                'client', 'address'
            ));
        }
    }

    public function clientStoreAction()
    {
        $this->getHelper('viewRenderer')->setNoRender(true);
        $data = $this->_model->getClientDataStore($this->_request->getPost());

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody($data);
    }

    public function indexAction()
    {
        $this->getForm('clientSearch')->populate($this->_request->getPost());

        // assign search to the view script.
        $this->view->assign(array(
            'search' => $this->_getSearchString('clientSearch')
        ));
    }

    public function addAction()
    {
        if ($this->_request->isXmlHttpRequest()
                && $this->_request->getParam('type') == 'add'
                && $this->_request->isPost()) {

            $this->render('add-form');
        } else {
            return $this->_helper->redirector('index', 'client');
        }
    }

    public function editAction()
    {
        if ($this->_request->getParam('idClient')
                && $this->_request->isPost()
                && $this->_request->isXmlHttpRequest()) {

            $client = $this->_model->find($this->_request->getParam('idClient'));

            $this->getForm('clientSave')
                ->populate($client->toArray('dd/MM/yyyy'));

            $this->view->assign(array(
                'client' => $client
            ));

            if ($this->_request->getParam('type') == 'edit') {
                $this->render('ajax-form');
            }
        } else {
           return $this->_helper->redirector('index', 'client');
        }
    }

    public function saveAction()
    {
        if (!$this->_request->isPost() && !$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'client');
        }

        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->_request->getParam('type') == 'edit') {
            $render = 'ajax-form.phtml';
            $form = 'clientSave';
        } else {
            $render = 'add-form.phtml';
            $form = 'clientAdd';
        }

        // remove client_dateExpiryLoa validation rules
        // if an empty string so that it can validate.
        if ($this->_request->getParam('client_dateExpiryLoa') === '') {
            $client_dateExpiryLoaValidateRules = $this->getForm($form)
                ->getElement('client_dateExpiryLoa')
                ->getValidator('Date');

            $this->getForm($form)->getElement('client_dateExpiryLoa')
                ->removeValidator('Date');
        }

        if (!$this->getForm($form)->isValid($this->_request->getPost())) {

            if (isset($client_dateExpiryLoaValidateRules)) {
                $this->getForm($form)
                    ->getElement('client_dateExpiryLoa')
                    ->addValidator($client_dateExpiryLoaValidateRules);
            }

            $html = $this->view->render('client/' . $render);

            $returnJson = array(
                'saved' => 0,
                'html'  => $html
            );
        } else {
            if ($this->_request->getParam('type') == 'edit') {
                $saved = $this->_model->save($form);
            } else {
                $saved = $this->_model->saveNewClient($form);
            }

            $returnJson = array(
                'saved' => $saved
            );

            if ($saved == 0) {
                if (isset($client_dateExpiryLoaValidateRules)) {
                    $this->getForm($form)
                        ->getElement('client_dateExpiryLoa')
                        ->addValidator($client_dateExpiryLoaValidateRules);
                }

                $html = $this->view->render('client/' . $render);
                $returnJson['html'] = $html;
            }
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }
}
