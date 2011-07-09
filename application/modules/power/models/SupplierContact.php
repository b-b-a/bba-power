<?php
/**
 * SupplierContact.php
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
 * DAO to represent a single SupplierContact.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_SupplierContact extends ZendSF_Model_Abstract
{

    protected $_id;
    protected $_supplierId;
    protected $_name;
    protected $_phone;
    protected $_email;
    protected $_address1;
    protected $_address2;
    protected $_address3;
    protected $_postcode;

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

    /**
     * @var string
     */
    protected $_prefix = 'suco_';

    public function getSupplierId()
    {
        return $this->_supplierId;
    }

    public function setSupplierId($id)
    {
        $this->_supplierId = (int) $id;
        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setName($name)
    {
        $this->_name = (string) $name;
        return $this;
    }

    public function getPhone()
    {
        return $this->_phone;
    }

    public function setPhone($phone)
    {
        $this->_phone = (string) $phone;
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

    public function getAddress1()
    {
        return $this->_address1;
    }

    public function setAddress1($address1)
    {
        $this->_address1 = (string) $address1;
        return $this;
    }

    public function getAddress2()
    {
        return $this->_address2;
    }

    public function setAddress2($address2)
    {
        $this->_address2 = (string) $address2;
        return $this;
    }

    public function getAddress3()
    {
        return $this->_address3;
    }

    public function setAddress3($address3)
    {
        $this->_address3 = (string) $address3;
        return $this;
    }

    public function getPostcode()
    {
        return $this->_postcode;
    }

    public function setPostcode($postcode)
    {
        $this->_postcode = (string) $postcode;
        return $this;
    }
}