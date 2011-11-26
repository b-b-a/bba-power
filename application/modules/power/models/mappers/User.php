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
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Mapper Class for User.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Mapper_User extends BBA_Model_Mapper_Abstract
{
   /**
     * @var string the DbTable class name
     */
    protected $_dbTableClass = 'Power_Model_DbTable_User';

    /**
     * @var sting the model class name
     */
    protected $_modelClass = 'Power_Model_User';

    /**
     * Gets a single user from the database using their username.
     *
     * @param string $username
     * @return Power_Model_User
     */
    public function getUserByUsername($username)
    {
        $select = $this->getDbTable()
            ->select()
            ->where('user_name = ?', $username);

        $row = $this->fetchRow($select, true);

        $user = new $this->_modelClass($row);

        return $user;
    }

    public function save($form)
    {
        if (!$this->checkAcl('save')) {
            throw new ZendSF_Acl_Exception('Saving users is not allowed.');
        }

        // add password treatment if set.
        $pwd = $this->getForm($form)->getValue('user_password');
        if ($pwd === '') {
            $this->getForm($form)->removeElement('user_password');
        } else {

            $auth = Zend_Registry::get('config')->user->auth;

            $treatment = $auth->credentialTreatment;
            $pwd = ZendSF_Utility_Password::$treatment($pwd . $auth->salt);
            $this->getForm($form)->getElement('user_password')->setValue($pwd);
        }

        return parent::save($form);
    }

    public function delete($id)
    {
        if (!$this->checkAcl('delete')) {
            throw new ZendSF_Acl_Exception('Deleting users is not allowed.');
        }

        $where = $this->getDbTable()
            ->getAdapter()
            ->quoteInto('user_idUser = ?', $id);

        return parent::delete($where);
    }
}
