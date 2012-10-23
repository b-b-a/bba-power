<?php
/**
 * string.php
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
 * Utility class to preform certain string operations.
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage Utility
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public Licens
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class ZendSF_Utility_String
{
    /**
     * Inflect the name using the inflector filter
     *
     * Changes camelCaseWord to Camel_Case_Word
     *
     * @param string $name The name to inflect
     * @return string The inflected string
     */
    public static function getInflected($name)
    {
        $inflector = new Zend_Filter_Inflector(':name');
        $inflector->setRules(array(
            ':name'  => array('Word_CamelCaseToUnderscore')
        ));
        return ucfirst($inflector->filter(array('name' => $name)));
    }

    /**
     * Checks if a string starts with a pattern.
     *
     * @param strin $needle string pattern
     * @param string $haystack string to check
     * @return bool
     */
    public static function startsWith($needle, $haystack)
    {
        return preg_match('/^' . preg_quote($needle).'/', $haystack);
    }

    /**
     * Checks if a string ends with a pattern.
     *
     * @param string $needle string pattern
     * @param strind $haystack string to check
     * @return bool
     */
    public static function endsWith($needle, $haystack)
    {
        return preg_match('/' . preg_quote($needle) .'$/', $haystack);
    }
}
