<?php
/**
 * Abstract.php
 *
 * Copyright (c) 2012 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of BBA Power.
 *
 * BBA Power is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BBA Power is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BBA Power.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   BBA
 * @package    BBA
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Abstract Model.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
abstract class Power_Model_DbTable_Abstract extends Zend_Db_Table_Abstract
{
    /**
     * @var string
     */
    protected $_rowPrefix = '';

    /**
     * @var array
     */
    protected $_nullAllowed = array();
    
    /**
     * @var Zend_Log
     */
    protected $_log;
    
    public function init()
    {
    	$this->_log = Zend_Registry::get('dblog');
    	$primary = (is_string($this->_primary)) ? $this->_primary : $this->_primary[0];
    	
    	$this->_rowPrefix = strstr($primary, '_', true);
    }
    
    public function getRowById($id)
    {
    	$id = (int) $id;
    	return $this->find($id)->current();
    }
    
    /**
     * Save a row to the database
     *
     * @param array $data The data to insert/update
     * @param Zend_DB_Table_Row $row Optional The row to use
     * @return mixed The primary key
     */
    public function saveRow($data, $row = null)
    {
    	if (null === $row) {
    		$row = $this->createRow();
    	}
    
    	$columns = $this->info('cols');
    	foreach ($columns as $column) {
    		if (array_key_exists($column, $data)) {
    			$row->$column = $data[$column];
    		}
    	}
    
    	return $row->save();
    }
    
    /**
     * Delete a row in the database. Maybe should only be defined in parent class
     * as can be different if we have a compound primary key. or use mixed interger or
     * array.
     *
     * @param int $id Primary key of the row to be deleted
     * @return int The number of rows deleted
     */
    /*public function deleteRow($id)
     {
    if (!is_numeric($id)) {
    throw new ZendSF_Model_Exception('Could not delete row in ' . __CLASS__);
    }
    
    $row = $this->find($id)->current();
    return $row->delete();
    }*/
    
    /**
     * Adds limit and offset to query.
     *
     * @param Zend_Db_Table_Select
     * @param int $count
     * @param int $offset
     * @return Zend_Db_Table_Select
     */
    public function getLimit(Zend_Db_Table_Select $select, $count, $offset)
    {
    	if ($count === null) {
    		return $select;
    	}
    
    	return $select->limit($count, $offset);
    }
    
    /**
     * Adds an order by to query.
     *
     * @param Zend_Db_Table_Select
     * @param string $sort
     * @return Zend_Db_Table_Select
     */
    public function getSortOrder(Zend_Db_Table_Select $select, $sort)
    {
    	if ($sort === '' || null === $sort) {
    		return $select;
    	}
    
    	if (strchr($sort,'-')) {
    		$sort = substr($sort, 1, strlen($sort));
    		$order = 'DESC';
    	} else {
    		$order = 'ASC';
    	}
    
    	return $select->order($sort . ' ' . $order);
    }
    
    protected function _getAccessClient($select, $table)
    {
        $auth = Zend_Auth::getInstance();
        $access = $auth->getIdentity()->getUser_accessClient(true);
        
        if ($access != '') {
            return $select->where($table . '_idClient IN (' . $access . ')');
        }
        
        return $select;
    }

    protected function _checkConstraints($data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->_nullAllowed) && ('0' == $value || '' == $value)) {
                $data[$key] = null;
            }
        }

        return $data;
    }
    
    protected function _getTablesValue($name)
    {
    	return new Zend_Db_Expr('(
    		SELECT tables_value
    		FROM tables
    		WHERE tables_key = ' . $name . '
    		AND tables_name = "' . $name . '"
    	)');
    }

    public function insert(array $data)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data[$this->_rowPrefix . '_dateCreate'] = new Zend_Db_Expr('CURDATE()');
        $data[$this->_rowPrefix . '_userCreate'] = $auth->getId();

        $data = $this->_checkConstraints($data);

        $this->_log->info(Zend_Debug::dump($data, "\nINSERT: " . __CLASS__ . "\n", false));

        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data[$this->_rowPrefix . '_dateModify'] = new Zend_Db_Expr('CURDATE()');
        $data[$this->_rowPrefix . '_userModify'] = $auth->getId();

        $data = $this->_checkConstraints($data);

        $this->_log->info(Zend_Debug::dump($data, "\nUPDATE: " . __CLASS__ . "\n", false));

        return parent::update($data, $where);
    }

    public function delete($where)
    {
        $this->_log->info(Zend_Debug::dump($where, "DELETE: " . __CLASS__, false));

        return parent::delete($where);
    }

    protected function _stripSpacesAndHyphens($subject)
    {
        $filter = new Zend_Filter_PregReplace(array(
                'match' => '/\s+|-+/',
                'replace' => ''
            )
        );

        return $filter->filter($subject);
    }
}