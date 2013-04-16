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
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Abstract table row class.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable_Row
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
abstract class Power_Model_DbTable_Row_Abstract
{
    /**
     * @var Zend_Db_Table_Row
     */
    protected $_row = null;

    /**
     * @var Zend_Log
     */
    protected $_log;
    
    /**
     * Array of all columns with need date format applied
     * to it when outputting row as an array.
     *
     * @var array
     */
    protected $_dateKeys = array();
    
    /**
     * @var string
     */
    protected $_dateFormat = 'dd/MM/yyyy';

    /**
     * Constructor.
     *
     * Supported params for $config are:-
     * - rowClass = class name or object of type Zend_Db_Table_Abstract
     *
     * @param  array $config OPTIONAL Array of user-specified config options.
     * @return void
     */
    public function __construct(array $config = array())
    {
        $this->setRow($config);
        $this->_log = Zend_Registry::get('dblog');
    }

    /**
     * First looks for getter Methods for the columnName, that are used to lazy
     * load dependant data (data not contained in the row).
     *
     * Lastly proxies to the __get method of the row.
     *
     * @param string $columnName
     * @return mixed
     */
    public function __get($columnName)
    {
        $lazyLoader = 'get' . ucfirst($columnName);
        if (method_exists($this, $lazyLoader)) {
            return $this->$lazyLoader();
        }

        return $this->getRow()->__get($columnName);
    }

    /**
     * Checks if a row value is set, proxies to the __isset method
     * of the row
     *
     * @param string $columnName
     * @return boolean
     */
    public function __isset($columnName)
    {
        return $this->getRow()->__isset($columnName);
    }

    /**
     * Proxies to the __set method on the row
     *
     * @param string $columnName
     * @param mixed $value
     * @return mixed
     */
    public function __set($columnName, $value)
    {
        return $this->getRow()->__set($columnName, $value);
    }

    /**
     * Returns the connected row
     *
     * @return Zend_Db_Table_Abstract
     */
    public function getRow()
    {
        return $this->_row;
    }

    /**
     * Sets the row
     *
     * @param array $config
     */
    public function setRow(array $config = array())
    {
        $rowClass = 'Zend_Db_Table_Row';
        if (isset($config['rowClass'])) {
            $rowClass = $config['rowClass'];
        }

        if (is_string($rowClass)) {
            $this->_row = new $rowClass($config);
            return;
        }

        if (is_object($rowClass)) {
            $this->_row = $rowClass;
            return;
        }

        throw new Power_Model_Exception('Could not set rowClass in ' . __CLASS__);
    }

    /**
     * Proxy method calls to the connected row, thing like toArray() etc
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        return call_user_func_array(array($this->getRow(), $method), $arguments);
    }

    public function __wakeup()
    {
        if (!$this->getRow()->isConnected()) {
            $tableClass = $this->getRow()->getTableClass();
            $table = new $tableClass();
            $this->getRow()->setTable($table);
        }
    }
    
    /**
     * Returns row as an array, with optional date formating.
     *
     * @param string $dateFormat
     * @return array
     */
    public function toArray($raw=false)
    {
    	$array = array();
    
    	foreach ($this->getRow() as $key => $value) {
    
	    	if (in_array($key, $this->_dateKeys)) {
	            if ('0000-00-00' === $value || !$value) {
	                $value = '';
	            } else {
	                $date = new Zend_Date($value);
	                $value = $date->toString($this->_dateFormat);
	            }
	            
	            $array[$key] = $value;
	        } else {
	        	$lazyLoader = 'get' . ucfirst($key);
	        	$array[$key] = (method_exists($this, $lazyLoader)) ? $this->$lazyLoader($raw) : $value;
	        }
    	}
    
    	return $array;
    }
}
