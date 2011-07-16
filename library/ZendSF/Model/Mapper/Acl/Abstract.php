<?php
/**
 * Abstract.php
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
 * Uthando-CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ZendSF.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Abstract model mapper acl class.
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
abstract class ZendSF_Model_Mapper_Acl_Abstract extends ZendSF_Model_Mapper_Abstract
    implements Zend_Acl_Resource_Interface
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
     * Implement the Zend_Acl_Resource_Interface, make this model
     * an acl resource
     *
     * @return string
     */
    public function  getResourceId()
    {
        return $this->_modelClass;
    }

    /**
     * Set the identity of the current request
     *
     * @param array|string|null|Zend_Acl_Role_Interface $identity
     * @return ZendSF_Model_Mapper_Abstract
     * @todo move this method to ZendSF_Acl_Absract
     */
    public function setIdentity($identity)
    {
        if (is_array($identity)) {
            if (!isset($identity['role'])) {
                $identity['role'] = 'Guest';
            }

            $identity = new Zend_Acl_Role($identity['role']);
        } elseif (is_object($identity) && is_string($identity->role)) {
            // add role here!
            $identity = new Zend_Acl_Role($identity->role);
        } elseif (is_scalar($identity) && !is_bool($identity)) {
            $identity = new Zend_Acl_Role($identity);
        } elseif (null === $identity) {
            $identity = new Zend_Acl_Role('Guest');
        } elseif (!$identity instanceof Zend_Acl_Role_Interface) {
            throw new ZendSF_Model_Exception('Invalid identity provided');
        }

        $this->_identity = $identity;

        return $this;
    }

    /**
     * Get the identity, if no ident use 'Guest'
     *
     * @return string
     */
    public function getIdentity()
    {
        if (null === $this->_identity) {
            $auth = Zend_Auth::getInstance();

            if (!$auth->hasIdentity()) {
                return 'Guest';
            }

            $this->setIdentity($auth->getIdentity());
        }

        return $this->_identity;
    }

    /**
     * Check the acl
     *
     * @param string $action
     * @return boolean
     */
    public function checkAcl($action)
    {
        return $this->getAcl()->isAllowed(
                $this->getIdentity(),
                $this,
                $action
        );
    }

    /**
     * Injector for the acl, the acl can be injected directly
     * via this method.
     *
     * We add all the access rule for this resource here, so we
     * add $this as the resource, rules are defined by the parent class.
     *
     * @param Zend_Acl $acl
     * @return ZendSF_Model_Mapper_Abstract
     */
    public function setAcl(Zend_Acl $acl)
    {
        if (!$acl->has($this->getResourceId())) {
            $acl->add($this);
        }

        $this->_acl = $acl;

        return $this;
    }

    /**
     * Get the acl and automatically instantiate the default acl if one
     * has not been injected.
     *
     * @return Zend_Acl
     */
    public function getAcl()
    {
        if (null === $this->_acl) {
            $module = $this->_namespace[0];
            $acl = ucfirst($module) . '_Model_Acl_' . ucfirst($module);

            if (class_exists($acl)) {
                $this->setAcl(new $acl);
            }
        }

        return $this->_acl;
    }
}
