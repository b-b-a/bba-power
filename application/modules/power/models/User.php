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
    protected $_idUser;
    protected $_name;
    protected $_password;
   protected $_fullName;
    protected $_role;
    protected $_accessClient;
   protected $_prefix = 'user_';
   public function getId()
    {
        return $this->_idUser;
    }
   public function setId($id)
    {
        $this->_idUser = (int) $id;
        return $this;
    }
    public function getUsername()
    {
        return $this->_name;
    }
    public function setUsername($user)
    {
        $this->_name = (string) $user;
        return $this;
    }
    public function getPassword()
    {
        return $this->_password;
    }
    public function setPassword($password)
    {
        $this->_password = (string) $password;
        return $this;
    }
    public function getRealName()
    {
        return $this->_fullName;
    }
   public function setRealName($name)
    {
        $this->_fullName = (string) $name;
        return $this;
    }
   public function getRole()
    {
        return $this->_role;
    }
   public function setRole($role)
    {
        $this->_role = (string) $role;
        return $this;
    }
    public function getClientName()
    {
        return $this->_accessClient;
    }
   public function setClientName($name)
    {
        $this->_accessClient = (string) $name;
        return $this;
    }
}
