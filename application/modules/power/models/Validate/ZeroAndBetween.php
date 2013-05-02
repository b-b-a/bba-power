<?php
/**
 * ZeroAndBetween.php
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
 * @package    Power
 * @subpackage Validate
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * ZeroAndBetween Validation Model.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Validate
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Validate_ZeroAndBetween extends Zend_Validate_Between
{
    public function isValid($value)
    {
        if (0 != $value) {
            return parent::isValid($value);
        }
        
        return true;
    }
}
