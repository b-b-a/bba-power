<?php
/**
 * Site.php
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
 * DAO to represent a single Site.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Site extends ZendSF_Model_Abstract
{
    /**
     * @var int
     */
    protected $_id;

    /**
     * @var int
     */
    protected $_clientId;

    /**
     * @var int
     */
    protected $_clientAddressId;

    /**
     * @var int
     */
    protected $_clientAddressIdBill;

    /**
     * @var int
     */
    protected $_clientContactId;

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

    protected $_prefix = 'si_';

    /**
     * Gets the site id.
     *
     * @return int siteId
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets the site id
     *
     * @param type $id
     * @return Power_Model_Site
     */
    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }

    /**
     * Gets the client Id
     *
     * @return int clientId
     */
    public function getClientId()
    {
        return $this->_clientId;
    }

    /**
     * Sets the client id
     *
     * @param int $id
     * @return Power_Model_Site
     */
    public function setClientId($id)
    {
        $this->_clientId = (int) $id;
        return $this;
    }

    /**
     * Gets the client address for this site.
     *
     * @return int clientAddressId
     */
    public function getClientAddressId()
    {
        return $this->_clientAddressId;
    }

    /**
     * Sets client address for this site.
     *
     * @param int $id
     * @return Power_Model_Site
     */
    public function setClientAddressId($id)
    {
        $this->_clientAddressId = (int) $id;
        return $this;
    }

    /**
     * Gets the client address id for billing
     *
     * @return int siteClientAddressIdBill
     */
    public function getClientAddressIdBill()
    {
        return $this->_clientAddressIdBill;
    }

    /**
     * Sets the client address id for billing
     * @param int $id
     * @return Power_Model_Site
     */
    public function setClientAddressIdBill($id)
    {
        $this->_clientAddressIdBill = (int) id;
        return $this;
    }

    /**
     * Gets the client contact id.
     *
     * @return int clientContactId
     */
    public function getClientContactId()
    {
        return $this->_clientContactId;
    }

    /**
     * Sets the client contact id.
     *
     * @param int $id
     * @return Power_Model_Site
     */
    public function setClientContactId($id)
    {
        $this->_clientContactId = (int) $id;
        return $this;
    }

    /**
     * Gets the user id of the user who created this record.
     *
     * @return int siteCreateBy
     */
    public function getCreateBy()
    {
        return $this->_createBy;
    }

    /**
     * Sets the user id of the user who created this record.
     *
     * @param int $id
     * @return Power_Model_Site
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
     * @return Power_Model_Site
     */
    public function setCreateDate($date)
    {
        $this->_createDate = new Zend_Date($date);
        return $this;
    }

    /**
     * Gets the user id of who modified this record.
     *
     * @return int siteModBy
     */
    public function getModBy()
    {
        return $this->_modBy;
    }

    /**
     * Sets the user id of who modified this record.
     *
     * @param int $id
     * @return Power_Model_Site
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
     * @return Power_Model_Site
     */
    public function setModDate($date)
    {
        $this->_modDate = new Zend_Date($date);
        return $this;
    }
}
