<?php
/**
 * Array.php
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
 * @subpackage Utility
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public Licens
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Utility class to preform certain array operations.
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage Utility
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public Licens
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class ZendSF_Utility_Array
{
    /**
     * Merges arrays together into one. If input array in an Zend_Config object
     * then it converts it to an array and then merges it.
     *
     * @param mixed $array
     * @return array
     */
    public static function mergeMultiArray($array)
    {
        if ($array instanceof Zend_Config) {
            $array = $array->toArray();
        }

        foreach(new RecursiveIteratorIterator(
		    new RecursiveArrayIterator((array) $array),
		    RecursiveIteratorIterator::SELF_FIRST
		) as $key => $value) {
		    if(!is_array($value)) {
		        $return_array[$key] = $value;
		    }
		}

        return $return_array;
    }
	
    /**
     * Turns an array into an object.
     *
     * @param array $array
     * @return object
     */
    public static function arrayToObject($array)
    {
        $object = new stdClass();
		if (is_array($array) && count($array) > 0):
			foreach ($array as $name=>$value):
				$name = lcfirst(trim($name));
				if (!empty($name)) $object->$name = $value;
			endforeach;
		endif;
		return $object;
    }

    /**
     * Turns an object into an array.
     *
     * @param object $obj
     * @return array
     */
    public static function objectToArray($obj)
    {
        $array = array();
		if (is_object($object)) $array = get_object_vars($object);
		return $array;
    }
}
