<?php
/**
 * UserController.php
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
 * Controller Class UserController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_UserController extends BBA_Controller_Action_Abstract
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

        // search form
        $this->setForm('userSearch', array(
            'controller' => 'user' ,
            'action' => 'list',
            'module' => 'power'
        ));

        $this->_setSearch(array(
            'user', 'role'
        ));
    }

    /**
     * Check to see if we are Admin user.
     */
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
        // left blank for logging in users.
    }

    public function userStoreAction()
    {
        return $this->_getAjaxDataStore('getList' ,'user_idUser');
    }

    public function listAction()
    {
        $this->getForm('userSearch')
            ->populate($this->_getSearch());

        // assign search to the view script.
        $this->view->assign(array(
            'search' => $this->_getSearchString('userSearch')
        ));
    }

    public function addAction()
    {
        if ($this->_request->isXmlHttpRequest()
                && $this->_request->getParam('type') == 'add'
                && $this->_request->isPost()) {
            $this->getForm('userSave');
            $this->render('ajax-form');
        } else {
            return $this->_helper->redirector('index', 'client');
        }
    }

    public function editAction()
    {
        if ($this->_request->getParam('idUser')
                && $this->_request->isPost()
                && $this->_request->isXmlHttpRequest()) {

            $user = $this->_model->find($this->_request->getParam('idUser'));
            $this->getForm('userSave')
                ->populate($user->toArray('dd/MM/yyyy'))
                ->getElement('user_password')
                ->setValue('')
                ->setRequired(false);

            $this->render('ajax-form');
        } else {
           return $this->_helper->redirector('index', 'client');
        }
    }

    public function saveAction()
    {
        if (!$this->_request->isPost()&& !$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('list', 'users');
        }

        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->_request->getParam('type') === 'edit') {
            $this->getForm('userSave')
                ->getElement('user_password')
                ->setRequired(false);
        }

        if (!$this->getForm('userSave')->isValid($this->_request->getPost())) {
            $html = $this->view->render('users/ajax-form.phtml');

            echo json_encode(array(
                'saved' => 0,
                'html'  => $html
            ));
        } else {
            $saved = $this->_model->save();

            $returnJson = array(
                'saved' => $saved
            );

            if ($saved == 0) {
                $html = $this->view->render('users/ajax-form.phtml');
                $returnJson['html'] = $html;
            }

            echo json_encode($returnJson);
        }
    }
}
