<?php
/**
 * Postcode.php
 *
 * Copyright (c) 2011 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of BBA Power.
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
 * @subpackage Filter
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Makes a postcode to standard format.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Filter
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Filter_Postcode implements Zend_Filter_Interface
{
	/**
	 * Defined by Zend_Filter_Interface.
	 *
	 * @param string $value
	 * @return string
	 */
	public function filter($value)
	{
		// make it upper case
		$new = strtoupper($value);
		// remove all spaces
		$new = str_replace(' ', '', $new);
		
		// get the last 3 characters and add a space before them.
		$replace = substr($new, -3, 3);
    	$new = str_replace($replace, ' ' . $replace, $new);
    	
    	return $new;
	}
}
