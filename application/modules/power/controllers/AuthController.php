<?php
/**
 * AuthController.php
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
 * Controller Class AuthController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_AuthController extends Zend_Controller_Action
{
    /**
     * @var ZendSF_Service_Authentication
     */
    protected $_authService;

    /**
     * @var Core_Model_User
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        $this->_model = new Power_Model_User();
        $this->_authService = new ZendSF_Service_Authentication();
    }

    public function loginAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
        }

        $this->view->assign('authLoginForm', $this->_getLoginForm());
    }

    public function logoutAction()
    {
        $this->_authService->clear();
        return $this->_helper->redirector('index', 'index');
    }

    public function authenticateAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->_forward('login');
        }

        // Validate
        $form = $this->_getLoginForm();

        if (!$form->isValid($request->getPost())) {
            $this->view->assign('authLoginForm', $form);
            return $this->render('login'); // re-render the login form
        }

        if (false === $this->_authService->authenticate($form->getValues())) {
            $form->setDescription('Login failed, Please try again.');
            $this->view->assign('authLoginForm', $form);
            return $this->render('login'); // re-render the login form
        }

        // no access for agent or decline roles.
        if (!$this->_helper->acl('Auth')) {
            $e = new Zend_Acl_Exception('Access denied for '. Zend_Auth::getInstance()->getIdentity()->user_name);
            $log = Zend_Registry::get('log');
            $log->info($e);
            $this->_authService->clear();
            throw $e;
        }

        return $this->_helper->redirector('index', 'meter');
    }

    private function _getLoginForm()
    {
        $urlHelper = $this->_helper->getHelper('url');

        $form = $this->_model->getForm('authLogin');
        $form->setAction($urlHelper->url(array(
            'controller'    => 'auth' ,
            'action'        => 'authenticate',
            'module'        => 'power'
            ),
            'default'
        ));
        $form->setMethod('post');

        return $form;
    }
}
