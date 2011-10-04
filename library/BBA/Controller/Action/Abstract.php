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

    protected $_search = array();

    protected $_searchString;

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

    protected function _setSearch(array $search)
    {
        foreach ($search as $value) {
            $this->_search[$value] = $this->_request->getParam($value);
        }

        return $this;
    }

    protected function _getSearch()
    {
        if (null === $this->_search) {
            throw new ZendSF_Exception('Search needs to be set.');
        }

        return $this->_search;
    }

    protected function _setSearchString()
    {
        $searchString = '{';

        foreach ($this->_getSearch() as $key => $value) {
            if ($value) {
                $searchString .= $key . ":'" . $value . "',";
            }
        }

        if (substr($searchString, -1, 1) == ',') {
            $searchString = substr($searchString, 0, -1) . '}';
        } else {
            $searchString .= '}';
        }

        $this->_searchString = $searchString;

        return $this;
    }

    protected function _getSearchString()
    {
        if (null === $this->_searchString) {
            $this->_setSearchString();
        }

        return $this->_searchString;
    }

    protected function _getAjaxDataStore($mapperMethod, $dataStoreId, $child = false)
    {
        $this->_helper->layout->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(true);

        $sort = $this->getRequest()->getParam('sort');
        $count = $this->getRequest()->getParam('count');
        $start = $this->getRequest()->getParam('start');

        $data = $this->_model->{$mapperMethod}(
            $this->_getSearch(), $sort, $count, $start
        );

        $store = $this->getDataStore($data, $dataStoreId);

        $store->setMetadata(
            'numRows',
            $this->_model->numRows($this->_getSearch(), $child)
        );

        echo $store->toJson();
    }
}
