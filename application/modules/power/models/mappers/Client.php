<?php
/**
 * Clients.php
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
 * Mapper Class for Clients.
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
     * @var Power_Model_DbTable_Clients
     */
    protected $_dbTableClass;

    /**
     * @var Power_Model_Clients
     */
    protected $_modelClass;

    public function clientSearch()
    {
        $searchClient = $this->getForm('clientSearch')->getValue('search_client');

        /* @var $select Zend_Db_Table_Select */
        $select = $this->_dbTable->select();

        if (!$searchClient == '') {
            $select->where('client_name like ? COLLATE utf8_general_ci', '%' . $searchClient . '%');
        }

        return $this->fetchAll($select);
    }

    public function save()
    {
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

        $model->setModBy($form['userId']);
        $model->setModDate(time());

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

        $this->_acl->allow('admin', $this);

        // implement rules here.

        return $this;
    }

}
