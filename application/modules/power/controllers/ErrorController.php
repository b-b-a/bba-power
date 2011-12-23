<?php
/**
 * ErrorController.php
 *
 * Copyright (c) 2011 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of BAA.
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
 * Error Controller for the main application.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        switch (get_class($errors->exception)) {
            case 'Zend_Controller_Dispatcher_Exception':
                // send 404
                $this->getResponse()
                     ->setRawHeader('HTTP/1.1 404 Not Found');
                $this->view->message = '404 page not found.';
                break;
            case 'ZendSF_Exception_404':
                // send 404
                $this->getResponse()
                     ->setRawHeader('HTTP/1.1 404 Not Found');
                $this->view->message = $errors->exception->getMessage();
                break;
            case 'ZendSF_Acl_Exception':
                $this->view->message = $errors->exception->getMessage();
                break;
            default:
                // application error
                $this->view->message = $errors->exception->getMessage();
                break;
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
           // $this->getHelper('layout')->disableLayout();
        }
    }
}