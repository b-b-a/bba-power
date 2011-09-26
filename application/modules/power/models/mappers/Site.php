<?php
/**
 * Sites.php
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
 * Mapper Class for Sites.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Mapper_Site extends ZendSF_Model_Mapper_Acl_Abstract
{
    /**
     * @var string the DbTable class name
     */
    protected $_dbTableClass = 'Power_Model_DbTable_Site';

    /**
     * @var sting the model class name
     */
    protected $_modelClass = 'Power_Model_Site';

    protected function _getSearch($search, $select)
    {
        if ($search === null) {
            return $select;
        }

        if (!$search['client'] == '') {
            $select->where('client_name like ?', '%' . $search['client'] . '%')
                ->orWhere('client_desc like ?', '%' . $search['client'] . '%');
        }

        if (!$search['site'] == '') {
            $select->where('clientAd_addressName like ?', '%' . $search['site'] . '%')
                ->orWhere('clientAd_address1 like ?', '%' . $search['site'] . '%')
                ->orWhere('clientAd_address2 like ?', '%' . $search['site'] . '%')
                ->orWhere('clientAd_address3 like ?', '%' . $search['site'] . '%')
                ->orWhere('clientAd_postcode like ?', '%' . $search['site'] . '%');
        }

        return $select;
    }

    public function numRows($search)
    {
        /* @var $select Zend_Db_Table_Select */
        $select = $this->getDbTable()->getSiteList();

        $select = $this->_getSearch($search, $select);

        $result = $this->fetchAll($select, true);

        return $result->count();
    }

    public function listSites($search = null, $sort = '', $count = null, $offset = null)
    {
        $select = $this->getDbTable()->getSiteList();

        $select = $this->_getSearch($search, $select);

        if ($count && $offset) {
            $select->limit($count, $offset);
        }

        if($sort == '') {
            $sort = 'client_name';
        }

        if(strchr($sort,'-')) {
            $sort = substr($sort, 1, strlen($sort));
            $order = 'DESC';
        } else {
            $order = 'ASC';
        }

        $select->order($sort . ' ' . $order);

        return $this->fetchAll($select);
    }

    public function getSiteDetails($id)
    {
        /* @var $select Zend_Db_Table_Select */
        $select = $this->getDbTable()->getSiteDetails();
        $select->where('site_idSite = ?', $id);

        $row = $this->fetchRow($select, true);

        $model = new Power_Model_Site($row);

        return $model;
    }

    public function save($form)
    {
        if (!$this->checkAcl('save')) {
            throw new ZendSF_Acl_Exception('saving sites is not allowed.');
        }

        $form = $this->getForm($form)->getValues();

        // remove client id if not set.
        if (!$form['site_idSite']) unset($form['site_idSite']);

        $model = new Power_Model_Site($form);

        // set modified and create dates.
        if ($form['returnAction'] == 'add') {
            $model->setDateCreate();
            $model->userCreate = $form['userId'];
        }

        // add modified date and by if updating record.
        if ($model->getId()) {
            $model->userModify = $form['userId'];
            $model->setDateModify();
        }

        return parent::save($model);
    }

    public function delete($id)
    {
        if (!$this->checkAcl('delete')) {
            throw new ZendSF_Acl_Exception('Deleting sites is not allowed.');
        }

        $where = $this->getDbTable()
            ->getAdapter()
            ->quoteInto('site_idSite = ?', $id);

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
