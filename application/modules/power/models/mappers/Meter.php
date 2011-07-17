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
        //$searchMeter = $this->getForm('meterSearch')->getValue('meter');
        //$searchClient = $this->getForm('meterSearch')->getValue('client');

        /* @var $select Zend_Db_Table_Select */
        $select = $this->_dbTable->getMeterDetails();

        if (!$search['meter'] == '') {
            $select->where('meter_numberSerial like ? COLLATE utf8_general_ci', '%'. $search['meter'] . '%');
        }

        if (!$search['client'] == '') {
            $select->where('client_name like ? COLLATE utf8_general_ci', '%' . $search['client'] . '%');
        }

        return $this->listMeters($select, $paged);
    }

    /**
     * Get a list of all meters and creates a paged result.
     *
     * @param Zend_Db_Table_Select $select
     * @return Power_Model_Meter
     */
    public function listMeters($select = null, $paged = null)
    {
        if ($select === null) {
            $select = $this->_dbTable->getMeterDetails();
        }

        if (null !== $paged) {
           return $this->_paginate($select, $paged);
        } else {
            $resultSet = $this->fetchAll($select, true);

            $rows = array();

            foreach ($resultSet as $row) {

                /* @var $newRow Power_Model_Meter */
                $newRow = new $this->_modelClass($row);

                $rows[] = $newRow;
            }

            return $rows;
        }
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

        return $this;
    }

}
