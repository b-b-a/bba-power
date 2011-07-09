<?php
/**
 * Contract.php
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
 * DAO to represent a single Contract.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Contract extends ZendSF_Model_Abstract
{
    protected $_id;
    protected $_type;
    protected $_status;
    protected $_desc;
    protected $_tenderId;
    protected $_supplierContactId;
    protected $_dateStart;
    protected $_dateEnd;
    protected $_txtTender;
    protected $_docAnalysis;
    protected $_docTermination;
    protected $_periodBillCust;
    protected $_periodCommission;
    protected $_userIdAgent;

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

    protected $_prefix = 'co_';

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function setType($type)
    {
        $this->_type = (string) $type;
        return $this;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function setStatus($status)
    {
        $this->_contractStatus = (string) $status;
        return $this;
    }

    public function getdesc()
    {
        return $this->_desc;
    }

    public function setDesc($desc)
    {
        $this->_desc = (string) $desc;
        return $this;
    }

    public function getTenderId()
    {
        return $this->_tenderId;
    }

    public function setTenderId($id)
    {
        $this->_tenderId = (int) $id;
        return $this;
    }

    public function getSupplierContactId()
    {
        return $this->_supplierContactId;
    }

    public function setSupplierContactId($id)
    {
        $this->_supplierContactId = (int) $id;
        return $this;
    }

    public function getDateStart()
    {
        return $this->_dateStart;
    }

    public function setDateStart($date)
    {
        $this->_dateStart = new Zend_Date($date);
        return $this;
    }

    public function getDateEnd() {
        return $this->_dateEnd;
    }

    public function setDateEnd($date)
    {
        $this->_dateEnd = new Zend_Date($date);
        return $this;
    }

    public function getTxtTender()
    {
        return $this->_txtTender;
    }

    public function setTxtTender($txt)
    {
        $this->_txtTender = (string) $txt;
        return $this;
    }

    public function getDocAnalysis()
    {
        return $this->_docAnalysis;
    }

    public function setDocAnalysis($doc)
    {
        $this->_docAnalysis = (string) $doc;
        return $this;
    }

    public function getDocTermination()
    {
        return $this->_docTermination;
    }

    public function setDocTermination($doc)
    {
        $this->_docTermination = (string) $doc;
        return $this;
    }

    public function getPeriodBillCust() {
        return $this->_periodBillCust;
    }

    public function setPeriodBillCust($period)
    {
        $this->_periodBillCust = (int) $period;
        return $this;
    }

    public function getPeriodCommission()
    {
        return $this->_periodCommission;
    }

    public function setPeriodCommission($commission)
    {
        $this->_periodCommission = (int) $commission;
        return $this;
    }

    public function getUserIdAgent()
    {
        return $this->_userIdAgent;
    }

    public function setUserIdAgent($id)
    {
        $this->_userIdAgent = (int) $id;
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
     * @return Power_Model_Contract
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
     * @return Power_Model_Contract
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
     * @return Power_Model_Contract
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
     * @return Power_Model_Contract
     */
    public function setModDate($date)
    {
        $this->_modDate = new Zend_Date($date);
        return $this;
    }

}