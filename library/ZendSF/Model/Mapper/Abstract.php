<?php
/**
 * MapperAbstract.php
 *
 * Copyright (c) 2010 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of ZendSF.
 *
 * ZendSF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ZendSF is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ZendSF.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Provides some common db functionality that is shared
 * across our db-based resources.
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
abstract class ZendSF_Model_Mapper_Abstract
{
    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTable;

    /**
     * @var string the DbTable class name
     */
    protected $_dbTableClass;

    /**
     * @var sting the model class name
     */
    protected $_modelClass;

    /**
     * @var array the model namespace split into an array
     */
    protected $_namespace;

    /**
     * @var array Form instances
     */
    protected $_forms = array();

    public function __construct()
    {
        $this->_namespace = $this->_getNamespace();

        $this->_dbTableClass = join('_', array(
            $this->_namespace[0],
            'Model_DbTable',
            end($this->_namespace)
        ));

        $this->_modelClass = join('_', array(
            $this->_namespace[0],
            'Model',
            end($this->_namespace)
        ));

        $this->setDbTable($this->_dbTableClass);
    }

    /**
     * Sets the database table object.
     *
     * @param string $dbTable
     * @return ZendSF_Model_Mapper_Abstract
     */
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }

        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new ZendSF_Model_Exception('Invalid table data gateway provided');
        }

        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
     * Gets the database table object.
     *
     * @return Zend_Db_Table_Abstract
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable($this->_dbTableClass);
        }

        return $this->_dbTable;
    }

    /**
     * Finds a single record by it's id.
     *
     * @param int $id
     * @return ZendSF_Model_Abstract
     */
    public function find($id)
    {
        $result = $this->getDbTable()->find($id);

        if (0 == count($result)) {
            return;
        }

        $row = $result->current();
        return new $this->_modelClass($row);
    }

    /**
     * Fetches all entries in table or from a select object.
     *
     * @param object $select dbTable select object
     * @return array ZendSF_Model_Abstract
     */
    public function fetchAll($select = null, $raw = false)
    {
        $resultSet = $this->getDbTable()->fetchAll($select);

        if (!$raw) {
            $rows = array();

            foreach ($resultSet as $row) {
                $rows[] = new $this->_modelClass($row);
            }

            $resultSet = $rows;
            unset($rows);
        }

        return $resultSet;
    }

    /**
     * Fetches one row from database.
     *
     * @param object $select dbTable select object
     * @param bool $raw Weather to retrun the model class or Zend_Db_Table_Abstract
     * @return ZendSF_Model_Abstract|Zend_Db_Table_Row_Abstract|null
     */
    public function fetchRow($select, $raw = false)
    {
        $row = $this->getDbTable()->fetchRow($select);

        if (0 == count($row)) {
            return;
        }

        return ($raw) ? $row : new $this->_modelClass($row);
    }

    /**
     * Saves a row to database
     *
     * @param ZendSF_Model_Abstract $model
     * @return mixed
     */
    public function save(ZendSF_Model_Abstract $model)
    {
        $primary = current($this->getDbTable()->info('primary'));
        $cols = $this->getDbTable()->info('cols');

        $data = $model->toArray();

        foreach ($data as $key => $value) {
            if (!in_array($key, $cols)) {
                unset($data[$key]);
            } elseif (!$value) {
                unset($data[$key]);
            }
        }

        if (null === ($id = $model->getId())) {
            unset($data[$primary]);
            return $this->getDbTable()->insert($data);
        } else {
            return $this->getDbTable()->update($data, array(
                $primary . ' = ?' => $id
            ));
        }
    }

    /**
     * Deletes records from database.
     *
     * @param string $where clause for record deletion
     * @return int number of rows deleted
     */
    public function delete($where)
    {
        return $this->getDbTable()->delete($where);
    }

    /**
     * Paginates the Db result.
     *
     * @param Zend_Db_Table_Select $select
     * @param int $paged
     * @param int $numDisplay
     * @return Zend_Paginator
     */
    protected function _paginate($select, $paged, $numDisplay = 25)
    {
        $adapter = new ZendSF_Paginator_Adapter_DbTableSelect($select);

        $primary = current($this->getDbTable()->info('primary'));
        $fromParts = $select->getPart('from');
        $mainTable = strtolower(end($this->_namespace));

        unset($fromParts[$mainTable]);

        $count = clone $select;
        $count->reset(Zend_Db_Select::COLUMNS);
        $count->reset(Zend_Db_Select::FROM);

        $count->from(
            $mainTable,
            new Zend_Db_Expr(
                'COUNT(' . $primary . ') AS `zend_paginator_row_count`'
            )
        );

        if (count($fromParts) > 1) {
            foreach($fromParts as $part) {
                $count->join(
                    $part['tableName'],
                    $part['joinCondition'],
                    null
                );
            }
        }

        $adapter->setRowCount($count);
        $adapter->modelClass = $this->_modelClass;

        $paginator = new Zend_Paginator($adapter);

        $paginator->setItemCountPerPage((int) $numDisplay)
                ->setCurrentPageNumber((int) $paged);

        return $paginator;
    }

    /**
     * Gets a Form
     *
     * @param string $name
     * @return Zend_Form
     */
    public function getForm($name)
    {
        if (!isset($this->_forms[$name])) {
            $class = join('_', array(
                    $this->_namespace[0],
                    'Form',
                    $this->_getInflected($name)
            ));
            $this->_forms[$name] = new $class(array('model' => $this));
        }
	    return $this->_forms[$name];
    }

    /**
     * Classes are named spaced using their module name
     * this returns that module name or the first class name segment.
     *
     * @return string This class namespace
     */
    protected function _getNamespace()
    {
        $ns = explode('_', get_class($this));
        return $ns;
    }

    /**
     * Inflect the name using the inflector filter
     *
     * Changes camelCaseWord to Camel_Case_Word
     *
     * @param string $name The name to inflect
     * @return string The inflected string
     */
    protected function _getInflected($name)
    {
        $inflector = new Zend_Filter_Inflector(':class');
        $inflector->setRules(array(
            ':class'  => array('Word_CamelCaseToUnderscore')
        ));
        return ucfirst($inflector->filter(array('class' => $name)));
    }
}
