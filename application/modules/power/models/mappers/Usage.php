<?php
/**
 * Usage.php
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
 * Mapper Class for Usage.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Mapper_Usage extends ZendSF_Model_Mapper_Acl_Abstract
{

    /**
     * @var _Model_DbTable_Usage
     */
    protected $_dbTableClass;

    /**
     * @var _Model_Usage
     */
    protected $_modelClass;

    public function getUsageByMeterId($id)
    {
        $select = $this->_dbTable
                ->select()
                ->where('usage_idMeter =  ?', $id)
                ->order('usage_dateReading DESC');
        return $this->fetchAll($select);
    }

    public function save()
    {
        if (!$this->checkAcl('save')) {
            throw new ZendSF_Acl_Exception('saving meters usage is not allowed.');
        }

        $form = $this->getForm('usageSave')->getValues();

        // remove client id if not set.
        if (!$form['usage_idUsage']) unset($form['usage_idUsage']);

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
