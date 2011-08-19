<?php
/**
 * Contract.php
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
 * Mapper Class for Contract.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Mapper_Contract extends ZendSF_Model_Mapper_Acl_Abstract
{
    /**
     * @var Power_Model_DbTable_Contract
     */
    protected $_dbTable;

    /**
     * @var Power_Model_Contract
     */
    protected $_modelClass;

    /**
     * Searches for a contracts by either contract, meter or both.
     *
     * @return array
     */
    public function contractSearch($search, $paged = null)
    {
        /* @var $select Zend_Db_Table_Select */
        $select = $this->_dbTable->getContractDetails();

        if (!$search['contract'] == '') {
            $select->where('client_name like ? ', '%'. $search['contract'] . '%');
        }

        if (!$search['meter'] == '') {
            $select->where('meter_numberMain like ? ', '%'. $search['meter'] . '%');
        }

        $log = Zend_Registry::get('log');
        $log->info($select->__toString());

        return $this->getContractList($paged, $select);
    }

    public function getContractList($paged = null, $select = null)
    {
        if ($select === null) {
            $select = $this->_dbTable->getContractDetails();
        }

        if (null !== $paged) {
            $numDisplay = Zend_Registry::get('config')
                ->layout
                ->contract
                ->paginate
                ->itemCountPerPage;

            return $this->_paginate($select, $paged, $numDisplay);
        } else {
            return $this->fetchAll($select);
        }
    }

    public function getContractById($id)
    {

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

        return $this;
    }

}
