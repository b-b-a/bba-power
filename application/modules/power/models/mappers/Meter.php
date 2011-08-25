<?php
/**
 * Meter.php
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
 * Mapper Class for Meter.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Mapper_Meter extends ZendSF_Model_Mapper_Acl_Abstract
{
    /**
     * @var Power_Model_DbTable_Meter
     */
    protected $_dbTable;

    /**
     * @var Power_Model_Meter
     */
    protected $_modelClass;

    /**
     * Searches for a meter by either meter number, client or both.
     *
     * @return array
     */
    public function meterSearch($search, $paged = null)
    {
        $select = $this->_dbTable->getMeterList();

        if (!$search['meter'] == '') {
            $select->where('meter_numberMain like ?', '%'. $search['meter'] . '%');
        }

        if (!$search['site'] == '') {
            $select->where('clientAd_addressName like ? ', '%' . $search['site'] . '%')
                    ->orWhere('clientAd_address1 like ?', '%' . $search['site'] . '%')
                    ->orWhere('clientAd_address2 like ?', '%' . $search['site'] . '%')
                    ->orWhere('clientAd_address3 like ?', '%' . $search['site'] . '%')
                    ->orWhere('clientAd_postcode like ?', '%' . $search['site'] . '%');
        }

        return $this->listMeters($paged, $select);
    }

    /**
     * Get a list of all meters and creates a paged result.
     *
     * @param Zend_Db_Table_Select $select
     * @return Power_Model_Meter
     */
    public function listMeters($paged = null, $select = null)
    {
        if ($select === null) {
            $select = $this->_dbTable->getMeterList();
        }

        if (null !== $paged) {
            $numDisplay = Zend_Registry::get('config')
                ->layout
                ->meter
                ->paginate
                ->itemCountPerPage;

            return $this->_paginate($select, $paged, $numDisplay);
        } else {
            return $this->fetchAll($select);
        }
    }

    public function getMetersBySiteId($id)
    {
        $select = $this->_dbTable->select()
                ->where('meter_idSite = ?', $id);

        return $this->fetchAll($select);
    }

    public function getMeterDetails($id)
    {
        /* @var $select Zend_Db_Table_Select */
        $select = $this->_dbTable->getMeterDetails();
        $select->where('meter_idMeter = ?', $id);

        $row = $this->fetchRow($select, true);

        /* @var $model Power_Model_Meter */
        $model = new $this->_modelClass($row);

        return $model;
    }

    public function save()
    {
        if (!$this->checkAcl('save')) {
            throw new ZendSF_Acl_Exception('saving meters is not allowed.');
        }

        $form = $this->getForm('meterSave')->getValues();

        // remove client id if not set.
        if (!$form['meter_idMeter']) unset($form['meter_idMeter']);

        /* @var $model Power_Model_Client */
        $model = new $this->_modelClass($form);

        // set modified and create dates.
        if ($form['returnAction'] == 'add') {
            $model->dateCreate = time();
            $model->userCreate = $form['userId'];
        }

        // add modified date and by if updating record.
        if ($model->getId()) {
            $model->userModify = $form['userId'];
            $model->dateModify = time();
        }

        return parent::save($model);
    }

    public function delete($id)
    {
        if (!$this->checkAcl('delete')) {
            throw new ZendSF_Acl_Exception('Deleting meters is not allowed.');
        }

        $where = $this->getDbTable()
                ->getAdapter()
                ->quoteInto('meter_idMeter = ?', $id);

        return parent::delete($where);
    }

    /**
     * Injector for the acl, the acl can be injected directly
     * via this method.
     *
     * We add all the access rules for this resoaccessurce here, so we first call
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
