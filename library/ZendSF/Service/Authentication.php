<?php
/**
 * Authentication.php
 *
 * Copyright (c) 2010 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of ZendSF.
 *
 * ZendSF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ZendSF is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ZendSF.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage Service
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Description of Authentication
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage Service
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class ZendSF_Service_Authentication
{
    /**
     * @var Zend_Auth_Adapter_DbTable
     */
    protected $_authAdapter;

    /**
     * @var Storefront_Model_Mapper_User
     */
    protected $_userModel;

    /**
     * @var Zend_Auth
     */
    protected $_auth;

    /**
     * Auth options
     *
     * @var Zend_Config
     */
    protected $_options;

    /**
     * Construct
     *
     * @param null|ZendSF_Model_Abstract $userModel
     */
    public function __construct(ZendSF_Model_Abstract $userModel = null)
    {
        $this->_options = Zend_Registry::get('config')->user->auth;

        $this->_userModel = (null === $userModel) ?
            new $this->_options->userModel() : $userModel;
    }

    /**
     * Authenticate a user
     *
     * @param  array $credentials Matched pair array containing email/passwd
     * @return boolean
     */
    public function authenticate($credentials)
    {
        $adapter    = $this->getAuthAdapter($credentials);
        $auth       = $this->getAuth();
        $result     = $auth->authenticate($adapter);

        if (!$result->isValid()) {
            return false;
        }

        $user = $this->_userModel
            ->{$this->_options->method}($credentials[$this->_options->identity]);

        $auth->getStorage()->write($user);

        return true;
    }

    public function getAuth()
    {
        if (null === $this->_auth) {
            $this->_auth = Zend_Auth::getInstance();
        }

        return $this->_auth;
    }

    public function getIdentity()
    {
        $auth = $this->getAuth();

        if ($auth->hasIdentity()) {
            return $auth->getIdentity();
        }

        return false;
    }

    /**
     * Clear any authentication data
     */
    public function clear()
    {
        $this->getAuth()->clearIdentity();
    }

    /**
     * Set the auth adpater.
     *
     * @param Zend_Auth_Adapter_Interface $adapter
     */
    public function setAuthAdapter(Zend_Auth_Adapter_Interface $adapter)
    {
        $this->_authAdapter = $adapter;
    }

    /**
     * Get and configure the auth adapter
     *
     * @param  array $value Array of user credentials
     * @return Zend_Auth_Adapter_DbTable
     */
    public function getAuthAdapter($values)
    {
        if (null === $this->_authAdapter) {

            $treatment = $this->_options->credentialTreatment;

            $authAdapter = new Zend_Auth_Adapter_DbTable(
                Zend_Db_Table_Abstract::getDefaultAdapter(),
                $this->_options->dbTable,
                $this->_options->identity,
                $this->_options->credential
            );

            $this->setAuthAdapter($authAdapter);
            $this->_authAdapter->setIdentity($values[$this->_options->identity]);

            $this->_authAdapter->setCredential(
                ZendSF_Utility_Password::$treatment(
                    $values[$this->_options->credential]
                    . $this->_options->salt
                )
            );
        }

        return $this->_authAdapter;
    }
}
