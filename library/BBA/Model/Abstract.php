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
 * @package    BBA
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * DAO to represent a single Abstract.
 *
 * @category   BBA
 * @package    BBA
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
abstract class BBA_Model_Abstract extends ZendSF_Model_Abstract
{
    /**
     * Default date format when converting to an array.
     *
     * @var string
     */
    protected $_dateFormat = 'yyyy-MM-dd';

    /**
     * Sets the create date for this record.
     *
     * @param string $date
     * @return Power_Model_Abstract
     */
    public function setDateCreate($date = null)
    {
        if (Zend_Date::isDate($date, $this->_dateFormat)) {
            $this->_data->dateCreate = new Zend_Date($date);
        } else {
            $this->_data->dateCreate = Zend_Date::now();
        }
        return $this;
    }

    /**
     * Sets the modified date
     *
     * @param string $date
     * @return Power_Model_Abstract
     */
    public function setDateModify($date = null)
    {
        if (Zend_Date::isDate($date, $this->_dateFormat)) {
            $this->_data->dateModify = new Zend_Date($date);
        } else {
            $this->_data->dateModify = Zend_Date::now();
        }
        return $this;
    }
}