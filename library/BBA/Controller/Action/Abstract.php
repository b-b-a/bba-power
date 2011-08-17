<?php
/**
 * Abstract.php
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
 * @package    BBA
 * @subpackage Controller_Action
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Common controller methods.
 *
 * @category   BBA
 * @package    BBA
 * @subpackage Controller_Action
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
abstract class BBA_Controller_Action_Abstract extends ZendSF_Controller_Action_Abstract
{
    /**
     * Sets the page number for pagination.
     *
     * @var int
     */
    protected $_page;

    protected $_dateFormat = 'dd/MM/yyyy';

    public function init()
    {
        parent::init();

        $page = $this->_request->getParam('page');
        $this->_page = ($page) ? $page : 0;

        $this->view->navigation()
                ->setAcl($this->_helper->getHelper('Acl')->getAcl())
                ->setRole($this->_helper->getHelper('Acl')->getIdentity());
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

    /**
     * returns an array of database objects in Json format to use with Dojo.
     *
     * @param array $dataObj
     * @param string $id
     * @return string
     */
    public function getDataStore($dataObj, $id)
    {
        $items = array();

        foreach ($dataObj as $row) {
            $items[] = $row->toArray('dd/MM/yyyy');
        }

        $store = new Zend_Dojo_Data($id, $items);

        return $store->toJson();
    }
}
