<?php
/**
 * ClientContact.php
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
 * DAO to represent a single ClientContact.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_ClientContact extends ZendSF_Model_Abstract
{
    protected $_id;
    protected $_clientId;
    protected $_type;
    protected $_name;
    protected $_clientAddressId;
    protected $_phone;
    protected $_email;

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

    protected $_prefix = 'clco_';

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }

    public function getClientId()
    {
        return $this->_clientId;
    }

    public function setClientId($id)
    {
        $this->_clientId = (int) $id;
        return $this;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function setType($text)
    {
        $this->_type = (string) $text;
        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setName($text)
    {
        $this->_name = (string) $text;
        return $this;
    }

    public function getClientAddressId()
    {
        return $this->_clientAddressId;
    }

    public function setClientAddressId($id)
    {
        $this->_clientAddressId = (int) $id;
        return $this;
    }

    public function getPhone()
    {
        return $this->_phone;
    }

    public function setPhone($phoneNo)
    {
        $this->_phone = (string) $phoneNo;
        return $this;
    }

    public function getEmail()
    {
        return $this->_email;
    }

    public function setEmail($email)
    {
        $this->_email = (string) $email;
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
