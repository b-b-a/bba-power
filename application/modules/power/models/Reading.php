<?php
/**
 * Reading.php
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
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * DAO to represent a single Reading.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Reading extends BBA_Model_Abstract
{
    /**
     * @var int
     */
    protected $_meterId;

    /**
     * @var Zend_Date
     */
    protected $_dateBill;

    /**
     * @var Zend_Date
     */
    protected $_dateReading;

    /**
     * @var int
     */
    protected $_valueDay;

    /**
     * @var int
     */
    protected $_valueNight;

    /**
     * @var int
     */
    protected $_valueOther;

    /**
     * @var string
     */
    protected $_type;

    /**
     * @var string
     */
    protected $_prefix = 're_';

    /**
     * Gets the meter id
     *
     * @return int
     */
    public function getMeterId()
    {
        return $this->_meterId;
    }

    /**
     * Sets the meter id
     *
     * @param int $id
     * @return Power_Model_Reading
     */
    public function setMeterId($id)
    {
        $this->_meterId = (int) $id;
        return $this;
    }

    /**
     * Gets the bill date.
     *
     * @return Zend_Date
     */
    public function getDateBill()
    {
        return $this->_dateBill;
    }

    /**
     * Sets the bill date
     *
     * @param string $date
     * @return Power_Model_Reading
     */
    public function setDateBill($date)
    {
        $this->_dateBill = new Zend_Date($date);
        return $this;
    }

    /**
     * Get the reading date
     *
     * @return Zend_Date
     */
    public function getDateReading()
    {
        return $this->_dateReading;
    }

    /**
     * Sets the reading date.
     *
     * @param string $date
     * @return Power_Model_Reading
     */
    public function setDateReading($date)
    {
        $this->_dateReading = new Zend_Date($date);
        return $this;
    }

    /**
     * Gets meter day value.
     *
     * @return int
     */
    public function getValueDay()
    {
        return $this->_valueDay;
    }

    /**
     * Sets the meter day value
     *
     * @param int $value
     * @return Power_Model_Reading
     */
    public function setValueDay($value)
    {
        $this->_valueDay = (int) $value;
        return $this;
    }

    /**
     * Gets the meter night value
     *
     * @return int
     */
    public function getValueNight()
    {
        return $this->_valueNight;
    }

    /**
     * Sets the meter night value
     *
     * @param int $value
     * @return Power_Model_Reading
     */
    public function setValueNight($value)
    {
        $this->_valueNight = (int) $value;
        return $this;
    }

    /**
     * Gets the meter other value
     *
     * @return int
     */
    public function getValueOther()
    {
        return $this->_valueOther;
    }

    /**
     * Sets the meter other value
     *
     * @param int $value
     * @return Power_Model_Reading
     */
    public function setValueOther($value)
    {
        $this->_valueOther = (int) $value;
        return $this;
    }

    /**
     * Gets the meter type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Sets the meter type
     *
     * @param string $type
     * @return Power_Model_Reading
     */
    public function setType($type)
    {
        $this->_type = (string) $type;
        return $this;
    }
}