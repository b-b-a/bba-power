<?php
/**
 * UsersController.php
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
 * Controller Class UsersController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_UsersController extends ZendSF_Controller_Action_Abstract
{
    /**
     * @var Power_Model_Mapper_User
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        parent::init();

        $this->_model = new Power_Model_Mapper_User();

        // register forms
        $this->setForm('userSave', array(
            'controller' => 'users' ,
            'action' => 'save',
            'module' => 'power'
        ));
    }

    public function preDispatch()
    {
        if (!$this->_helper->acl('Admin')) {
           return $this->_helper->redirector('index', 'index');
        }
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        // put a dashboard here?
    }

    public function listAction()
    {
        $this->view->assign(array(
            'users' => $this->_model->fetchAll()
        ));

        $this->_log->info($this->view->users[0]->toArray());
    }

    public function addAction()
    {
        $this->getForm('userSave')
                ->addHiddenElement('returnAction', 'add');
    }

    public function editAction()
    {
        if ($this->_request->getParam('userId')) {
            $user = $this->_model->find($this->_request->getParam('userId'));
            $this->getForm('userSave')
                    ->populate($user->toArray())
                    ->addHiddenElement('returnAction', 'edit')
                    ->getElement('us_password')
                    ->setRequired(false);
        } else {
           return $this->_helper->redirector('list', 'users');
        }
    }

    public function saveAction()
    {
        if (!$this->_request->isPost()) {
            return $this->_helper->redirector('list', 'users');
        }

        $action = $this->_request->getParam('returnAction');

        $this->getForm('userSave')->addHiddenElement('returnAction', $action);

        if ($action == 'edit') {
            $this->getForm('userSave')
                    ->getElement('us_password')
                    ->setRequired(false);
        }

        if (!$this->getForm('userSave')->isValid($this->_request->getPost())) {
            return $this->render($action); // re-render the edit form
        } else {
            $saved = $this->_model->save();

            if ($saved) {
                $this->_helper->FlashMessenger(array(
                    'pass' => 'User saved to database'
                ));
                return $this->_helper->redirector('list', 'users');
            } else {
                $this->_helper->FlashMessenger(array(
                    'fail' => 'User could not be saved to database'
                ));

                return $this->render($action);
            }
        }
    }

    public function deleteAction()
    {
        if ($this->_request->getParam('userId')) {
            $user = $this->_model->delete($this->_request->getParam('userId'));

            if ($user) {
                $this->_helper->FlashMessenger(array(
                    'pass' => 'User deleted from database'
                ));
            } else {
                $this->_helper->FlashMessenger(array(
                    'fail' => 'could not delete user from database'
                ));
            }
        }

        return $this->_helper->redirector('list', 'users');
    }
}
