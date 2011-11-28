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
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Database adapter class for the User table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_User extends ZendSF_Model_DbTable_Abstract
{
     /**
     * @var string database table
     */
    protected $_name = 'user';

    /**
     * @var string primary key
     */
    protected $_primary = 'user_idUser';

    /**
     * @var string row class
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_User';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array();

    public function getUserById($id)
    {
        return $this->find($id)->current();
    }

    /**
     * Gets a single user from the database using their username.
     *
     * @param string $username
     * @return Zend_Db_Table_Row
     */
    public function getUserByUsername($username)
    {
        $select = $this->select()
            ->where('user_name = ?', $username);

        return $this->fetchRow($select);
    }

    public function searchUsers(array $search, $sort = '', $count = null, $offset = null)
    {
        $select = $this->select();

        if (!$search['user'] == '') {
            if (substr($search['user'], 0, 1) == '=') {
                $id = (int) substr($search['user'], 1);
                $select->where('user_idUser = ?', $id);
            } else {
                $select->orWhere('user_name like ?', '%' . $search['user'] . '%')
                    ->orWhere('user_fullName like ?', '%' . $search['user'] . '%');
            }
        }

        if (!$search['role'] == '') {
            $select->orWhere('user_role like ?', '%' . $search['role'] . '%')
                ->orWhere('user_accessClient like ?', '%' . $search['role'] . '%');
        }

        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $result = $this->searchUsers($search);
        return $result->count();
    }
}
