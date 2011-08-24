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
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $search = array(
            'client'    => $this->_request->getParam('client'),
            'address'   => $this->_request->getParam('address')
        );

        // gets all clients and assigns them to the view script.
        $this->view->assign(array(
            'clients'   => $this->_model->clientSearch($search, $this->_page),
            'search'    => $search
        ));
    }

    public function addAction()
    {
        $this->getForm('clientSave')
                ->addHiddenElement('returnAction', 'add');
    }

    public function editAction()
    {
        if ($this->_request->getParam('clientId')) {
            $client = $this->_model->find($this->_request->getParam('clientId'));

            $this->getForm('clientSave')
                    ->populate($client->toArray())
                    ->addHiddenElement('returnAction', 'edit');

            $this->view->assign('client', $client->getId());
        } else {
           return $this->_helper->redirector('index', 'client');
        }
    }

    public function saveAction()
    {
        if (!$this->_request->isPost()) {
            return $this->_helper->redirector('index', 'client');
        }

        $clientId = $this->_request->getParam('clientId');

        if ($this->_request->getParam('cancel')) {
            return $this->_helper->redirector('index', 'client', 'power', array(
                'clientId'  => $clientId
            ));
        }

        $action = $this->_request->getParam('returnAction');

        $this->getForm('clientSave')->addHiddenElement('returnAction', $action);

        // remove client_dateExpiryLoa if an empty string so that it can validate.
        if ($this->_request->getParam('client_dateExpiryLoa') === '') {
            $this->getForm('clientSave')->removeElement('client_dateExpiryLoa');
        }

        if (!$this->getForm('clientSave')->isValid($this->_request->getPost())) {
            $this->view->assign(array(
                'client'    => $clientId
            ));
            return $this->render($action); // re-render the edit form
        } else {
            $saved = $this->_model->save();

            if ($saved > 0) {
                $this->_helper->FlashMessenger(array(
                    'pass' => 'Client saved to database'
                ));

                return $this->_helper->redirector('index', 'client');
            } elseif ($saved == 0) {
                $this->_helper->FlashMessenger(array(
                    'fail' => 'Nothing new to save'
                ));

                return $this->_forward($action);
            }
        }
    }

    public function deleteAction()
    {
        if ($this->_request->getParam('clientId')) {
            $client = $this->_model->delete($this->_request->getParam('clientId'));

            if ($client) {
                $this->_helper->FlashMessenger(array(
                    'pass' => 'Client deleted from database'
                ));
            } else {
                $this->_helper->FlashMessenger(array(
                    'fail' => 'Could not delete client from database'
                ));
            }
        }

        return $this->_helper->redirector('index', 'client');
    }
}
