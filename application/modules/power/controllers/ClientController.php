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
     * @var Power_Model_Mapper_Client
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        if ($this->_helper->acl('Guest')) {
            return $this->_forward('login', 'auth');
        }

        parent::init();

        $this->_model = new Power_Model_Mapper_Client();

        $this->setForm('clientSave', array(
            'controller' => 'client' ,
            'action' => 'save',
            'module' => 'power'
        ));

        // search form
        $this->setForm('clientSearch', array(
            'controller' => 'client' ,
            'action' => 'index',
            'module' => 'power'
        ));

        $this->_setSearch(array(
            'client', 'address'
        ));
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $this->getForm('clientSearch')
            ->populate($this->_getSearch());

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
            $this->getForm('clientSave');
            $this->render('ajax-form');
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

    public function clientStoreAction()
    {
        return $this->_getAjaxDataStore('getList' ,'client_idClient');
    }

    public function saveAction()
    {
        if (!$this->_request->isPost() && !$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'client');
        }

        $this->_helper->viewRenderer->setNoRender(true);

        // remove client_dateExpiryLoa if an empty string so that it can validate.
        if ($this->_request->getParam('client_dateExpiryLoa') === '') {
            $this->getForm('clientSave')->removeElement('client_dateExpiryLoa');
        }

        if (!$this->getForm('clientSave')->isValid($this->_request->getPost())) {

            $html = $this->view->render('client/ajax-form.phtml');

            echo json_encode(array(
                'saved' => 0,
                'html'  => $html
            ));
        } else {
            $saved = $this->_model->save();

            echo json_encode(array(
                'saved' => $saved
            ));
        }
    }
}
