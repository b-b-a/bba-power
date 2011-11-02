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
class Power_AuthController extends ZendSF_Controller_Action_Abstract
{
    /**
     * @var ZendSF_Service_Authentication
     */
    protected $_authService;

    /**
     * @var Core_Model_Mapper_User
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        parent::init();

        $this->_model = new Power_Model_Mapper_User();
        $this->_authService = new ZendSF_Service_Authentication();

        $this->setForm('authLogin', array(
            'controller' => 'auth' ,
            'action' => 'authenticate',
            'module' => 'power'
        ));
    }

    public function loginAction()
    {
        if (!$this->_helper->acl('Guest')) {
            return $this->_forward('index', 'index');
        }
    }

    public function logoutAction()
    {
        if (!$this->_helper->acl('Read')) {
            return $this->_forward('login');
        }

        $this->_authService->clear();
        return $this->_helper->redirector('index', 'index');
    }

    public function authenticateAction()
    {
        if (!$this->_helper->acl('Guest')) {
            return $this->_forward('login');
        }

        if (!$this->_request->isPost()) {
            return $this->_forward('login');
        }

        $form = $this->getForm('authLogin');

        if (!$form->isValid($this->_request->getPost())) {
            return $this->render('login'); // re-render the login form
        }

        if (false === $this->_authService->authenticate($form->getValues())) {
            $form->setDescription('Login failed, Please try again.');
            return $this->render('login'); // re-render the login form
        }

        return $this->_helper->redirector('index', 'meter');
    }
}
