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
class Power_Model_Mapper_User extends ZendSF_Model_Mapper_Acl_Abstract
{
    /**
     * @var Power_Model_DbTable_User
     */
    protected $_dbTableClass;

    /**
     * @var Power_Model_User
     */
    protected $_modelClass;

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
                ->where('us_username = ?', $username);

        $row = $this->fetchRow($select, true);

        $user = new $this->_modelClass($row);

        return $user;
    }

    public function save()
    {
        $form = $this->getForm('userSave')->getValues();

        // remove userId if not set.
        if (!$form['us_id']) unset($form['us_id']);

        // add password treatment if set.
        if ($form['us_password']) {
            $auth = Zend_Registry::get('config')
                    ->user
                    ->auth;

            $treatment = $auth->credentialTreatment;
            $form['us_password'] = ZendSF_Utility_Password::$treatment(
                $form['us_password']
                . $auth->salt
            );
        }

        $model = new $this->_modelClass($form);

        return parent::save($model);
    }

    public function delete($id)
    {
        if (!$this->checkAcl('delete')) {
            throw new ZendSF_Acl_Exception('Deleting users is not allowed.');
        }

        $where = $this->getDbTable()
                ->getAdapter()
                ->quoteInto('us_id = ?', $id);

        return parent::delete($where);
    }

    /**
     * Gets a users role
     * reduntent in favor of de-normalized roles.
     *
     * @param Zend_Db_Table_Row
     * @return Power_Model_Role
     * @deprecated
     */
    public function getUserRole($row = null)
    {
        if (null === $row) {
            return null;
        }

        /* @var $parentRow Zend_Db_Table_Row */
        $parentRow = $row->findParentRow(
            'Power_Model_DbTable_Roles',
            'role'
        );

        return new Power_Model_Role($parentRow);
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
     * @return ZendSF_Model_Mapper_Abstract
     */
    public function setAcl(Zend_Acl $acl)
    {
        parent::setAcl($acl);

        // implement rules here.

        return $this;
    }

}
