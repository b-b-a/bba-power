<?php
/**
 * Client.php
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
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Mapper Class for Client.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Mapper_Client extends ZendSF_Model_Mapper_Acl_Abstract
{
    /**
     * @var Power_Model_DbTable_Client
     */
    protected $_dbTableClass;

    /**
     * @var Power_Model_Client
     */
    protected $_modelClass;

    public function listClients($paged = null, $select = null)
    {
        if ($select === null) {
            $select = $this->_dbTable->getClientList();
        }

        if (null !== $paged) {
            $numDisplay = Zend_Registry::get('config')
                ->layout
                ->client
                ->paginate
                ->itemCountPerPage;

            return $this->_paginate($select, $paged, $numDisplay);
        } else {
            return $this->fetchAll($select);
        }
    }

    public function clientSearch($search, $paged = null)
    {
        /* @var $select Zend_Db_Table_Select */
        $select = $this->_dbTable->getClientList();

        if (!$search['client'] == '') {
            $select->where('client_name like ? COLLATE utf8_general_ci', '%' . $search['client'] . '%');
        }

        return $this->listClients($paged, $select);
    }

    public function save()
    {
        if (!$this->checkAcl('save')) {
            throw new ZendSF_Acl_Exception('Deleting users is not allowed.');
        }

        $form = $this->getForm('clientSave')->getValues();

        // remove client id if not set.
        if (!$form['client_idClient']) unset($form['client_idClient']);

        /* @var $model Power_Model_Client */
        $model = new $this->_modelClass($form);

        // set modified and create dates.
        if ($form['returnAction'] == 'add') {
            $model->setCreateDate(time());
            $model->setCreateBy($form['userId']);
        }

        // add modified date and by if updating record.
        if ($model->getId()) {
            $model->setModBy($form['userId']);
            $model->setModDate(time());
        }

        return parent::save($model);
    }

    public function delete($id)
    {
        if (!$this->checkAcl('delete')) {
            throw new ZendSF_Acl_Exception('Deleting clients is not allowed.');
        }

        $where = $this->getDbTable()
                ->getAdapter()
                ->quoteInto('client_idClient = ?', $id);

        return parent::delete($where);
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
    public function setAcl(Zend_Acl $acl)
    {
        parent::setAcl($acl);

        // implement rules here.
        $this->_acl->allow('admin', $this)
            ->deny('admin', $this, array('delete'));

        return $this;
    }

}
