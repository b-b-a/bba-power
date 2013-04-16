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
 * @subpackage Model_DbTable_Row
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Database class for the User table row.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable_Row
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Row_User extends Power_Model_DbTable_Row_Abstract
{
    public function getId()
    {
        return $this->getRow()->user_idUser;
    }

    public function getRole()
    {
        return $this->getRow()->user_role;
    }

    public function getName()
    {
        return $this->getRow()->user_name;
    }

    public function getUser_role($raw=false)
    {
        return Power_Model_Acl_Power::$bbaRoles[$this->getRow()->user_role]['label'];
    }

    public static function getRoles()
    {
        return Power_Model_Acl_Power::$bbaRoles;
    }
    
    public function getUser_accessClient($raw=false)
    {
        $client = ($raw) ? $this->getRow()->user_accessClient : 
            $this->getRow()->findParentRow(
                'Power_Model_DbTable_Client',
                'clientAccess'
            )->client_name;
        
        return $client;
    }

    /**
     * Returns row as an array.
     *
     * @return array
     */
    public function toArray($raw = false)
    {
        $array = array();

        foreach ($this->getRow() as $key => $value) {
            if (true === $raw) {
                $array[$key] = $value;
            } else {
                switch ($key) {
                    case 'user_role':
                        $array[$key] = $this->getUser_role();
                        break;
                    case 'user_accessClient':
                        $array[$key] = $this->getUser_accessClient();
                        break;
                    default:
                        $array[$key] = $value;
                        break;
                }
            }
        }

        return $array;
    }
}
