<?php
/**
 * Abstract.php
 *
 * Copyright (c) 2011 Shaun Freeman <shaun@shaunfreeman.co.uk>.
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
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>k
 */

/**
 * Base model class that all our models will inherit from.
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public Licenset
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
abstract class ZendSF_Model_Abstract
{
    /**
     * @var array Class methods
     */
    protected $_classMethods;

    /**
     * Sets the default date format to save to the database.
     * if set to null then date format will be saved as a unix timestamp.<br />
     * example for MySql date field<br />
     * yyyy-MM-dd
     *
     * @var string|null
     */
    protected $_dateFormat = null;

    /**
     * An array of variables that do don't belong to
     * this model but are needed in database joins
     *
     * @var array
     */
    protected $_vars = array();

    /**
     * @var string
     */
    protected $_prefix;

    /**
     * Constructor
     *
     * @param array|Zend_Db_Table_Abstract|null $options
     */
    public function __construct($options = null)
    {
        $this->_classMethods = get_class_methods($this);

        if ($options instanceof Zend_Db_Table_Row) {
            $options = $options->toArray();
        }

        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Sets the property in this class.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
       $method = 'set' . ucfirst($name);

        if (('mapper' == $name) || !in_array($method, $this->_classMethods)) {
            throw new ZendSF_Model_Exception('Invalid ' . $name . ' property');
        }

        $this->$method($value);
    }

    /**
     * Gets the property in this class.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);

        if (('mapper' == $name) || !in_array($method, $this->_classMethods)) {
            throw new ZendSF_Model_Exception('Invalid ' . $name . ' property');
        }

        return $this->$method();
    }

    /**
     * Gets and set variables that don't live in this class
     * but are used in database joins. Has to be prefixed with either 'set' or 'get'.
     * Example:
     * <code>
     * <?php
     * // get value
     * $user->getRole('admin');
     *
     * // set value
     * $user->setRole('admin');
     * ?>
     * </code>
     * @param string $name
     * @param array $arguments
     * @return ZendSF_Model_Abstract|mixed|ZendSF_Exception
     */
    public function __call($name, $arguments)
    {
        $prefix = substr($name, 0, 3);
        $name = lcfirst(substr($name, 3));

        switch ($prefix) {
            case 'get':
                return $this->_vars[$name];
                break;

            case 'set':
                $this->_vars[$name] = (count($arguments) == 1) ?
                    $arguments[0] : $arguments;
                return $this;
                break;

            default:
                throw new ZendSF_Model_Exception(
                        'method ' . $name . ' not defined in ' . get_class($this)
                        );
                break;
        }
    }

    /**
     * Sets the options for this class.
     *
     * @param array $options
     * @return ZendSF_Model_Abstract
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $key = $this->_getCamelCase($key);
            $method = 'set' . ucfirst($key);
            if (in_array($method, $this->_classMethods)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * Turns class values into an array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();

        foreach ($this->_classMethods as $method) {
            if (substr($method, 0, 3) == 'get') {
                $value = $this->$method();

                if ($value instanceof Zend_Currency) {
                    $value = $value->getValue();
                }

                if ($value instanceof Zend_Date) {
                    $value = ($this->_dateFormat === null) ?
                        $value->getTimestamp() :
                        $value->toString($this->_dateFormat);
                }

                $key = $this->_normalise(substr($method,3));
                $array[$key] = $value;
            }
        }

        return $array;
    }

    /**
     * camel cases the string using the inflector filter
     *
     * Changes underscore_word to underscoreWord
     *
     * @param string $name The name to camelCase
     * @return string The camelCased string
     */
    protected function _getCamelCase($name)
    {
        $inflector = new Zend_Filter_Inflector(':string');
        $inflector->setRules(array(
            ':string'    => array(
                new Zend_Filter_PregReplace(array(
                    'match'     => '/' . $this->_prefix . '/',
                    'replace'   => ''
                )),
                'Word_UnderscoreToCamelCase'
            )
        ));
        return $inflector->filter(array('string' => $name));
    }

    /**
     * normalise the name using the inflector filter
     *
     * Changes camelCaseWord to camel_case_word
     *
     * @param string $name The name to normalise
     * @return string The normalised string
     */
    protected function _normalise($name)
    {
        $inflector = new Zend_Filter_Inflector(':prefix:string');
        $inflector->setRules(array(
            ':string'    => array('Word_CamelCaseToUnderscore', 'StringToLower'),
            'prefix'    => $this->_prefix
        ));
        return $inflector->filter(array('string' => $name));
    }
}