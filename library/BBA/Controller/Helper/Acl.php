<?php
/**
 * Acl.php
 *
 * Copyright (c) 2010 Shaun Freeman <shaun@shaunfreeman.co.uk>.
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
 * @package    BBA
 * @subpackage Controller_Helper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Acl action helper used for when we want to control access to resources
 * that do not have a Model.
 *
 * @category   BBA
 * @package    BBA
 * @subpackage Controller_Helper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class BBA_Controller_Helper_Acl extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Zend_Acl
     */
    protected $_acl;

    /**
     * @var string
     */
    protected $_identity;

    /**
     * Init the acl instance for this module
     */
    public function init()
    {
        $module = $this->getRequest()->getModuleName();
        $acl = ucfirst($module) . '_Model_Acl_' . ucfirst($module);

        if (class_exists($acl)) {
            $this->_acl = new $acl();
        }
    }

    /**
     * Get the current acl
     *
     * @return Zend_Acl
     */
    public function getAcl()
    {
        return $this->_acl;
    }

    /**
     * Check the acl
     *
     * @param string $resource
     * @param string $privilege
     * @return boolean
     */
    public function isAllowed($resource = null, $privilege = null)
    {
        if (null === $this->_acl) {
            return null;
        }

        return $this->_acl->isAllowed($this->getIdentity(), $resource, $privilege);
    }

    /**
     * Set the identity of the current request
     *
     * @param array|string|null|Zend_Acl_Role_Interface $identity
     * @return ZendSF_Controller_Helper_Acl
     * @todo move this method to ZendSF_Acl_Absract
     */
    public function setIdentity($identity)
    {
        if (is_array($identity)) {
            if (!isset($identity['role'])) {
                $identity['role'] = 'guest';
            }

            $identity = new Zend_Acl_Role($identity['role']);
        } elseif (is_object($identity) && is_string($identity->role)) {
            $identity = new Zend_Acl_Role($identity->role);
        } elseif (is_scalar($identity) && !is_bool($identity)) {
            $identity = new Zend_Acl_Role($identity);
        } elseif (null === $identity) {
            $identity = new Zend_Acl_Role('guest');
        } elseif (!$identity instanceof Zend_Acl_Role_Interface) {
            throw new Exception('Invalid identity provided');
        }

        $this->_identity = $identity;

        return $this;
    }

    /**
     * Get the identity, if no ident use Guest
     *
     * @return string
     */
    public function getIdentity()
    {
        if (null === $this->_identity) {
            $auth = Zend_Auth::getInstance();

            if (!$auth->hasIdentity()) {
                return 'guest';
            }

            $this->setIdentity($auth->getIdentity());
        }

        return $this->_identity;
    }

    /**
     * Proxy to the isAllowed method
     */
    public function direct($resource = null, $privilege = null)
    {
        return $this->isAllowed($resource, $privilege);
    }
}
