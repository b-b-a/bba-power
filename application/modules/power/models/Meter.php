<?php
/**
 * Meter.php
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
 * DAO to represent a single Meter.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Meter extends ZendSF_Model_Abstract
{
    /**
     * @var int meter id
     */
    protected $_id;

    /**
     * @var int meter site id
     */
    protected $_siteId;

    /**
     * @var int string number
     */
    protected $_no;

    /**
     * @var Zend_Date
     */
    protected $_dateInstall;

    /**
     * @var Zend_Date
     */
    protected $_dateRemoved;

    /**
     * @var string
     */
    protected $_pPipeSize;

    /**
     * @var int
     */
    protected $_createBy;

    /**
     * @var Zend_Date
     */
    protected $_createDate;

    /**
     * @var int
     */
    protected $_modBy;

    /**
     * @var Zend_Date
     */
    protected $_modDate;

    /**
     * @var string date format
     */
    protected $_dateFormat = 'yyyy-MM-dd';

    protected $_prefix = 'me_';

    /**
     * Gets the meter id.
     *
     * @return $_meterId
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets the meter id
     *
     * @param int $id
     * @return Power_Model_Meter
     */
    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }

    /**
     * Gets the meter site id
     *
     * @return int $_meterSiteId
     */
    public function getSiteId()
    {
        return $this->_siteId;
    }

    /**
     * Sets the meter site id
     *
     * @param int $id
     * @return Power_Model_Meter
     */
    public function setSiteId($id)
    {
        $this->_siteId = (int) $id;
        return $this;
    }

    /**
     * Gets th meter number
     *
     * @return string $_meterNo
     */
    public function getNo()
    {
        return $this->_no;
    }

    /**
     * Sets the meter number
     *
     * @param string $meterNo
     * @return Power_Model_Meter
     */
    public function setNo($meterNo)
    {
        $this->_no = (string) $meterNo;
        return $this;
    }

    /**
     * Gets the install date of the meter
     *
     * @return Zend_Date
     */
    public function getDateInstall()
    {
        return $this->_dateInstall;
    }

    /**
     * Sets the install date of the meter using Zend_Date class
     *
     * @param string $date
     * @return Power_Model_Meter
     */
    public function setDateInstall($date)
    {
        $this->_dateInstall =  new Zend_Date($date);
        return $this;
    }

    public function getDateRemoved()
    {
        return $this->_dateRemoved;
    }

    public function setDateRemoved($date)
    {
        $this->_dateRemoved = new Zend_Date($date);
        return $this;
    }

    public function getPipeSize()
    {
        return $this->_pipeSize;
    }

    public function setPipeSize($text)
    {
        $this->_pipeSize = (string) $text;
        return $this;
    }

    public function getCreateBy()
    {
        return $this->_createBy;
    }

    public function setCreateBy($id)
    {
        $this->_createBy = (int) $id;
        return $this;
    }

    public function getCreateDate()
    {
        return $this->_createDate;
    }

    public function setCreateDate($date)
    {
        $this->_createDate = new Zend_Date($date);
        return $this;
    }

    public function getModBy()
    {
        return $this->_modBy;
    }

    public function setModBy($id)
    {
        $this->_modBy = (int) $id;
        return $this;
    }

    public function getModDate()
    {
        return $this->_modDate;
    }

    public function setModDate($date)
    {
        $this->_modDate = new Zend_Date($date);
        return $this;
    }
}
