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
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Mapper Class for Abstract.
 *
 * @category   BBA
 * @package    BBA
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class BBA_Model_Mapper_Abstract extends ZendSF_Model_Mapper_Acl_Abstract
{
    protected $_defaultDbSort;

    protected function _getSearch($search, $select)
    {
        if ($search === null) {
            return $select;
        }

        return $this->getDbTable()->getSearch($search, $select);
    }

    public function numRows($search)
    {
        /* @var $select Zend_Db_Table_Select */
        $select = $this->getDbTable()->getList();

        $select = $this->_getSearch($search, $select);

        $result = $this->fetchAll($select, true);

        return $result->count();
    }

    public function getList($search = null, $sort = '', $count = null, $offset = null)
    {
        $select = $this->getDbTable()->getList();

        $select = $this->_getSearch($search, $select);

        $select = $this->getLimit($select, $count, $offset);

        $select = $this->getSort($select, $sort);

        return $this->fetchAll($select);
    }

    public function getLimit($select, $count, $offset)
    {
        return $select->limit($count, $offset);
    }

    public function getSort($select, $sort)
    {
        if($sort == '') {
            $sort = $this->_defaultDbSort;
        }

        if(strchr($sort,'-')) {
            $sort = substr($sort, 1, strlen($sort));
            $order = 'DESC';
        } else {
            $order = 'ASC';
        }

        return $select->order($sort . ' ' . $order);
    }

    public function save($form)
    {
        $primary = current($this->getDbTable()->info('primary'));
        $form = $this->getForm($form)->getValues();

        // remove primary id if not set.
        if (!$form[$primary]) {
            unset($form[$primary]);
        }

        $model = new $this->_modelClass($form);
        $model->setCols($this->getDbTable()->info('cols'));

        // set create date and user.
        if (!$model->getId()) {
            $model->setDateCreate();
            $model->userCreate = $form['userId'];
        }

        // add modified date/user if updating record.
        if ($model->getId()) {
            $model->userModify = $form['userId'];
            $model->setDateModify();
        }

        return parent::save($model);
    }

    /**
     * Injector for the acl, the acl can be injected directly
     * via this method.
     *
     * We add all the access rules for this resource here, so we first call
     * parent method to add $this as the resource then we
     * define it rules here.
     *
     * @param Zend_Acl_Resource_Interface $acl
     * @return ZendSF_Model_Mapper_Abstract
     */
    public function setAcl(Zend_Acl $acl) {
        parent::setAcl($acl);

        // implement rules here.
        $this->_acl->allow('admin', $this)
            ->deny('admin', $this, array('delete'));

        return $this;
    }
}
