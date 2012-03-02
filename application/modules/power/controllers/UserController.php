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
class Power_UserController extends Zend_Controller_Action
{
    /**
     * @var Power_Model_User
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        $this->_model = new Power_Model_User();
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

        if (!$this->_helper->acl('Admin')) {
            throw new ZendSF_Acl_Exception('Access denied');
        }
    }

    public function userStoreAction()
    {
        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $data = $this->_model->getUserDataStore($this->_request->getPost());

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody($data);
    }

    public function indexAction()
    {
        $urlHelper = $this->_helper->getHelper('url');
        $form = $this->_model->getForm('userSearch')
            ->populate($this->_request->getPost());

        $form->setAction($urlHelper->url(array(
            'controller'    => 'user' ,
            'action'        => 'index',
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');

        // assign search to the view script.
        $this->view->assign(array(
            'search' => Zend_Json::encode($form->getValues()),
            'userSearchForm'  => $form
        ));
    }

    public function addUserAction()
    {
        if ($this->_request->isXmlHttpRequest()
                && $this->_request->getParam('type') == 'add'
                && $this->_request->isPost()) {

            $this->_helper->layout->disableLayout();

            $form = $this->_getUserForm();

            $this->view->assign(array('userSaveForm' => $form));

            $this->render('user-form');
        } else {
            return $this->_helper->redirector('index', 'user');
        }
    }

    public function editUserAction()
    {
        if ($this->_request->getParam('user_idUser')
                && $this->_request->isPost()
                && $this->_request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $user = $this->_model->getUserById($this->_request->getParam('user_idUser'));

            $form = $this->_getUserForm();
            $form->populate($user->toArray())
                ->getElement('user_password')
                ->setValue('')
                ->setRequired(false);

            $this->view->assign(array('userSaveForm' => $form));

            $this->render('user-form');
        } else {
           return $this->_helper->redirector('index', 'user');
        }
    }

    public function saveUserAction()
    {
        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$this->_request->isPost() && !$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'user');
        }

        try {
            $saved = $this->_model->saveUser($this->_request->getPost());

            $returnJson = array('saved' => $saved);

            if ($saved == 0) {
                $this->view->assign(array('userSaveForm' => $this->_getUserForm()));
                $html = $this->view->render('user/user-form.phtml');
                $returnJson['html'] = $html;
            } else {
                $this->view->assign(array(
                    'id'    => $saved,
                    'type'  => 'user'
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

    private function _getUserForm()
    {
        $urlHelper = $this->_helper->getHelper('url');
        $form = $this->_model->getForm('userSave');

        $form->setAction($urlHelper->url(array(
            'controller'    => 'user',
            'action'        => 'user-save',
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');
        return $form;
    }
}
