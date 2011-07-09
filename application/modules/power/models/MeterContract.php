<?php
/**
 * MeterContract.php
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
 * DAO to represent a single MeterContract.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_MeterContract extends ZendSF_Model_Abstract
{
    protected $_id;
    protected $_contractId;
    protected $_useEstimate;

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
     * @var string
     */
    protected $_dateFormat = 'yyyy-MM-dd';

    protected $_prefix ='meco_';

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }

    public function getContractId()
    {
        return $this->_contractId;
    }

    public function setContractId($id)
    {
        $this->_contractId = (int) $id;
        return $this;
    }

    public function getUseEstimate()
    {
        return $this->_useEstimate;
    }

    public function setUseEstimate($int)
    {
        $this->_useEstimate = (int) $int;
        return $this;
    }

    /**
     * Gets the user id of the user who created this record.
     *
     * @return int
     */
    public function getCreateBy()
    {
        return $this->_createBy;
    }

    /**
     * Sets the user id of the user who created this record.
     *
     * @param int $id
     * @return Power_Model_MeterContract
     */
    public function setCreateBy($id)
    {
        $this->_createBy = (int) $id;
        return $this;
    }

    /**
     * Gets the create date of this record.
     *
     * @return Zend_Date
     */
    public function getCreateDate()
    {
        return $this->_createDate;
    }

    /**
     * Sets the create date for this record.
     *
     * @param string $date
     * @return Power_Model_MeterContract
     */
    public function setCreateDate($date)
    {
        $this->_createDate = new Zend_Date($date);
        return $this;
    }

    /**
     * Gets the user id of who modified this record.
     *
     * @return int
     */
    public function getModBy()
    {
        return $this->_modBy;
    }

    /**
     * Sets the user id of who modified this record.
     *
     * @param int $id
     * @return Power_Model_MeterContract
     */
    public function setModBy($id)
    {
        $this->_modBy = (int) $id;
        return $this;
    }

    /**
     * Gets the modified date
     *
     * @return Zend_Date
     */
    public function getModDate()
    {
        return $this->_modDate;
    }

    /**
     * Sets the modified date
     *
     * @param string $date
     * @return Power_Model_MeterContract
     */
    public function setModDate($date)
    {
        $this->_modDate = new Zend_Date($date);
        return $this;
    }

}