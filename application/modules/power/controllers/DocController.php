<?php
/**
 * DocController.php
 *
 * Copyright (c) 2012 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of BBA Power.
 *
 * BBA Power is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BBA Power is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BBA Power.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Controller Class DocController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_DocController extends Zend_Controller_Action
{
    /**
     * @var Power_Model_Doc
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        $this->_model = new Power_Model_Doc();
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

    public function indexAction()
    {}

    /**
     * Default action
     */
    public function viewAction()
    {
        $request = $this->getRequest();
        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if ($request->getParam('dir') && $request->getParam('file')) {
            $doc = $this->_model->getDocFile($request->getParam('dir'), $request->getParam('file'));

            return $this->getResponse()
                ->setHeader('Content-disposition: attachment; filename=' . $request->getParam('file'))
                ->setHeader('Content-Type', $doc[1])
                ->setBody($doc[0]);
        }

        return $this->_helper->redirector('index', 'doc');
    }
}
