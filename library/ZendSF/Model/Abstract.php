<?php
/**
 * Abstract.php
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
 * @subpackage Model
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
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
abstract class ZendSF_Model_Abstract
{
    /**
     * @var array Class methods
     */
    protected $_classMethods;

    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $_dbTables = array();

    /**
     * @var ZendSF_Model_Cache_Abstract
     */
    protected $_cache;

    /**
     * @var arrar cache options
     */
    protected $_cacheOptions;

    /**
     * @var array Form instances
     */
    protected $_forms = array();

    /**
     * Constructor
     *
     * @param array|Zend_Config|null $options
     * @return void
     */
    public function __construct($options = null)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (is_array($options)) {
            $this->setOptions($options);
        }

        $this->init();
    }

    /**
     * Constructor extensions
     */
    public function init()
    {}

    /**
     * Set options using setter methods
     *
     * @param array $options
     * @return ZendSF_Model_Abstract
     */
    public function setOptions(array $options)
    {
        if (null === $this->_classMethods) {
            $this->_classMethods = get_class_methods($this);
        }

        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $this->_classMethods)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * Gets the database table object.
     *
     * @return Zend_Db_Table_Abstract
     */
    public function getDbTable($name)
	{
        if (!isset($this->_dbTables[$name])) {
            $class = join('_', array(
                $this->_getNamespace(),
                'Model',
                'DbTable',
                ZendSF_Utility_String::getInflected($name)
            ));
            $this->_dbTables[$name] = new $class();
        }
	    return $this->_dbTables[$name];
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
                    $this->_getNamespace(),
                    'Form',
                    ZendSF_Utility_String::getInflected($name)
            ));
            $this->_forms[$name] = new $class(array('model' => $this));
        }
	    return $this->_forms[$name];
    }

    /**
     * returns an object of database values to use with Dojo data.
     *
     * @param Zend_Db_Table_Rowset
     * @param string $id
     * @return Zend_Dojo_Data
     */
    protected function _getDojoData(Zend_Db_Table_Rowset $dataObj, $id)
    {
        $items = array();

        foreach ($dataObj as $row) {
            $items[] = $row->toArray();
        }

        return new Zend_Dojo_Data($id, $items);
    }

    /**
     * Set the cache to use.
     *
     * @param ZendSF_Model_Abstract $cache
     */
    public function setCache(ZendSF_Model_Cache_Abstract $cache)
    {
        $this->_cache = $cache;
    }

    /**
     * Set the options
     *
     * @param array $options
     */
    public function setCacheOptions(array $options)
    {
        $this->_cacheOptions = $options;
    }

    /**
     * Get the cache options
     *
     * @return array
     */
    public function getCacheOptions()
    {
        if (empty($this->_cacheOptions)) {
            $cacheOptions = Zend_Registry::get('config')->cache->model;

            $this->_cacheOptions = array(
                'useCache'          => $cacheOptions->useCache,
                'frontend'          => $cacheOptions->frontend->type,
                'backend'           => $cacheOptions->backend->type,
                'frontendOptions'   => $cacheOptions->frontendOptions->toArray(),
                'backendOptions'    => $cacheOptions->backendOptions->toArray()
            );
        }

        return $this->_cacheOptions;
    }

    /**
     * Query the cache
     *
     * @param type $tagged The tag to save data to
     * @return ZendSF_Model_Cache_Abstract
     */
    public function getCached($tagged = null)
    {
        if (null === $this->_cache) {
            $options = $this->getCacheOptions();
            unset($options['useCache']);

            if ($this->_cacheOptions['useCache']) {
                $this->_cache = new ZendSF_Model_Cache(
                    $this,
                    $options
                );
            }
        }

        if ($this->_cache instanceof ZendSF_Model_Cache_Abstract) {
            $this->_cache->setTagged($tagged);

            return $this->_cache;
        } else {
            return $this;
        }
    }
    
    /**
     * Clears the cache using a tag.
     * 
     * @param string $tag
     */
    public function clearCache(array $tags)
    {
    	$cache = $this->getCached();
    	
    	if ($this->_cache instanceof ZendSF_Model_Cache_Abstract) {
    		$log = Zend_Registry::get('log');
    		$log->info($tags);
    		
    		if (count($tags) > 0) {
	    	    $cache->getCache()
	    		    ->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, $tags);
	    	} else {
	    	    $cache->getCache()->clean(Zend_Cache::CLEANING_MODE_ALL);
	    	}
    	}
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
        return $ns[0];
    }
}
