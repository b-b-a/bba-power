<?php
/**
 * User.php
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
 * DAO to represent a single User.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_User extends ZendSF_Model_Abstract
{
    /**
     * @var int userId
     */
    protected $_id;

    /**
     * @var string username
     */
    protected $_username;

    /**
     * @var string password
     */
    protected $_password;

    /**
     * @var string realName
     */
    protected $_realName;

    /**
     * @var string role
     */
    protected $_role;

    /**
     * @var string clientName
     */
    protected $_clientName;

    /**
     * @var string
     */
    protected $_prefix = 'us_';

    /**
     * Gets the userId
     *
     * @return int userId
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets the userId
     *
     * @param int $id
     * @return Power_Model_User
     */
    public function setId($id)
    {
        $this->_id = (int) $id;
        return $this;
    }

    /**
     * Gets the username
     *
     * @return string username
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Sets the username
     *
     * @param string $user
     * @return Power_Model_User
     */
    public function setUsername($user)
    {
        $this->_username = (string) $user;
        return $this;
    }

    /**
     * Gets the password
     *
     * @return string password
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Sets the password
     *
     * @param type $password
     * @return Power_Model_User
     */
    public function setPassword($password)
    {
        $this->_password = (string) $password;
        return $this;
    }

    /**
     * Gets the users real name
     *
     * @return string realName
     */
    public function getRealName()
    {
        return $this->_realName;
    }

    /**
     * Set the users real name
     *
     * @param string $name
     * @return Power_Model_User
     */
    public function setRealName($name)
    {
        $this->_realName = (string) $name;
        return $this;
    }

    /**
     * Gets the users role
     *
     * @return string role
     */
    public function getRole()
    {
        return $this->_role;
    }

    /**
     * Sets the users role
     *
     * @param string $role
     * @return Power_Model_User
     */
    public function setRole($role)
    {
        $this->_role = (string) $role;
        return $this;
    }

    /**
     * Gets the users clien name
     *
     * @return string clientName
     */
    public function getClientName()
    {
        return $this->_clientName;
    }

    /**
     * Sets the users client name
     *
     * @param string $name
     * @return Power_Model_User
     */
    public function setClientName($name)
    {
        $this->_clientName = (string) $name;
        return $this;
    }
}
