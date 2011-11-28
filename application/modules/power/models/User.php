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
 * User model.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_User extends ZendSF_Model_Acl_Abstract
{
    /**
     * Get User by their id
     *
     * @param  int $id
     * @return null|Power_Model_DbTable_Row_User
     */
    public function getUserById($id)
    {
        $id = (int) $id;
        return $this->getDbTable('user')->getUserById($id);
    }

    /**
     * Get User by their username
     *
     * @param  string $username The email address to search for
     * @param  Power_Model_DbTable_Row_User $ignoreUser User to ignore from the search
     * @return null|Power_Model_DbTable_Row_User
     */
    public function getUserByUsername($username, $ignoreUser=null)
    {
        return $this->getDbTable('User')->getUserByUsername($username, $ignoreUser);
    }

    public function getUserDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $form = $this->getForm('userSearch');
        $search = array();

        if ($form->isValid($post)) {
            $search = $form->getValues();
        }

        $dataObj = $this->getDbTable('user')->searchUsers($search, $sort, $count, $start);

        $store = $this->getDojoDataStore($dataObj, 'user_idUser');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('user')->numRows($search)
        );

        return $store->toJson();
    }

    /**
     * Update a user
     *
     * @param  array  $post The data
     * @return false|int
     */
    public function saveUser($post)
    {
        if (!$this->checkAcl('saveUser')) {
            throw new ZendSF_Acl_Exception('Insufficient rights');
        }

        $form = $this->getForm('userSave');

        if ($post['type'] === 'edit') {
            $form->getElement('user_password')->setRequired(false);
        }

        if (!$form->isValid($post)) {
            return false;
        }

        // get filtered values
        $data = $form->getValues();

        // add password treatment if set.
        if (array_key_exists('user_password', $data) && '' != $data['user_password']) {
            $auth = Zend_Registry::get('config')->user->auth;
            $treatment = $auth->credentialTreatment;
            $pwd = ZendSF_Utility_Password::$treatment($data['user_password'] . $auth->salt);
            $data['user_password'] = $pwd;
        } else {
            unset($data['user_password']);
        }

        $user = array_key_exists('user_idUser', $data) ?
            $this->getDbTable('user')->getUserById($data['user_idUser']) : null;

        return $this->getDbTable('user')->saveRow($data, $user);
    }

    /**
     * Deletes a user.
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        if (!$this->checkAcl('delete')) {
            throw new ZendSF_Acl_Exception('Deleting users is not allowed.');
        }

        if ($user instanceof Power_Model_DbTable_Row_User) {
            $userId = (int) $user->userId;
        } else {
            $userId = (int) $user;
        }

        $user = $this->getUserById($userId);

        if (null !== $user) {
            $user->delete();
            return true;
        }

        return false;
    }

    /**
     * Injector for the acl, the acl can be injected directly
     * via this method.
     *
     * We add all the access rules for this resource here, so we first call
     * parent method to add $this as the resource then we
     * define it rules here.
     *
     * @param Zend_Acl_Resource_Interface $acl
     * @return ZendSF_Model_Abstract
     */
    public function setAcl(Zend_Acl $acl) {
        parent::setAcl($acl);

        // implement rules here.
        $this->_acl->allow('admin', $this)
            ->deny('admin', $this, array('delete'));

        return $this;
    }
}
