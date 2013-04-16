<?php
/**
 * Cache.php
 *
 * Copyright (c) 2010 Shaun Freeman <shaun@shaunfreeman.co.uk>.
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
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Provides a concrete class for ZendSF_Model_Mapper_Cache_Abstract.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Cache
{
	/**
	 * @var array Class methods
	 */
	protected $_classMethods;
	
	/**
	 * @var Zend_Cache
	 */
	protected $_cache;
	
	/**
	 * @var string Frontend cache type, should be Class
	 */
	protected $_frontend;
	
	/**
	 * @var string Backend cache type
	 */
	protected $_backend;
	
	/**
	 * @var array Frontend options
	 */
	protected $_frontOptions = array();
	
	/**
	 * @var array Backend Options
	*/
	protected $_backendOptions = array();
	
	/**
	 * @var ZendSF_Model_Abstract
	*/
	protected $_model;
	
	/**
	 * @var string The tag this call will be stored against
	 */
	protected $_tagged;
	
	/**
	 * Constructor
	 *
	 * @param ZendSF_Model_Abstract $model
	 * @param array|Zend_Config $options
	 * @param string $tagged
	 */
	public function __construct(Power_Model_Abstract $model, $options, $tagged = null)
	{
		$this->_model = $model;
	
		if ($options instanceof Zend_Config) {
			$options = $options->toArray();
		}
	
		if (is_array($options)) {
			$this->setOptions($options);
		}
	
		$this->setTagged($tagged);
	}
	
	/**
	 * Set options using setter method
	 *
	 * @param array $options
	 * @return ZendSF_Model_Cache_Abstract
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
	 * Set the cache instance
	 *
	 * @param Zend_Cache $cache
	 */
	public function setCache(Zend_Cache $cache)
	{
		$this->_cache = $cache;
	}
	
	/**
	 * Get the cache instance or configure a
	 * new instance if one is not present
	 *
	 * @return Zend_Cache
	 */
	public function getCache()
	{
		if (null === $this->_cache) {
			$this->_cache = Zend_Cache::factory(
					$this->_frontend,
					$this->_backend,
					$this->_frontOptions,
					$this->_backendOptions
			);
		}
	
		return $this->_cache;
	}
	
	/**
	 * Set the frontend options
	 *
	 * @param array $frontend
	 */
	public function setFrontendOptions(array $frontend)
	{
		$this->_frontOptions = $frontend;
		$this->_frontOptions['cached_entity'] = $this->_model;
	}
	
	/**
	 * Set the backend options
	 *
	 * @param array $backend
	 */
	public function setBackendOptions(array $backend)
	{
		$this->_backendOptions = $backend;
	}
	
	/**
	 * Set the backend cache type
	 *
	 * @param string $backend
	 */
	public function setBackend($backend)
	{
		$this->_backend = $backend;
	}
	
	/**
	 * Set the frontend cache type
	 *
	 * @param string $frontend
	 */
	public function setFrontend($frontend)
	{
		if ('Class' != $frontend) {
			throw new Power_Model_Exception('Frontend type must be a Class');
		}
	
		$this->_frontend = $frontend;
	}
	
	/**
	 * Set the tag for this cache
	 *
	 * @param string $tagged
	 */
	public function setTagged($tagged = null)
	{
		$this->_tagged = $tagged;
	
		if (null === $tagged) {
			$this->_tagged = 'default';
		}
	}
	
	/**
	 * Proxy calls from here to Zend_Cache, Zend_Cache
	 * will be using the Class frontend which caches the model
	 * classes methods.
	 *
	 * @param string $method
	 * @param array $params
	 * @return mixed
	 */
	public function __call($method, $params)
	{
		if (!is_callable(array($this->_model, $method))) {
			throw new Power_Model_Exception(
					'Method '
					. $method
					. ' does not exist in class '
					. get_class($this->_model)
			);
		}
	
		$cache = $this->getCache();
		$cache->setTagsArray(array($this->_tagged));
		$callback = array($cache, $method);
	
		return call_user_func_array($callback, $params);
	}
}