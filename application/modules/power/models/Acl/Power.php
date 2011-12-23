<?php
/**
 * Power.php
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
 * @subpackage Model_Acl
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Description of BBA
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Acl
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Acl_Power extends ZendSF_Acl_Abstract
{
    /**
     * Set up role and resouces for power module.
     */
    public function init()
    {
        $this->removeRole('admin');
        $this->removeRole('registered');

        $this->addRole(new Zend_Acl_Role('decline'), 'guest');
        $this->addRole(new Zend_Acl_Role('agent'), 'decline');
        $this->addRole(new Zend_Acl_Role('read'));
        $this->addRole(new Zend_Acl_Role('meterReading'), 'read');
        $this->addRole(new Zend_Acl_Role('user'), 'meterReading');
        $this->addRole(new Zend_Acl_Role('admin'), 'user');

        $this->addResource(new Zend_Acl_Resource('Decline'));
        $this->addResource(new Zend_Acl_Resource('Agent'));
        $this->addResource(new Zend_Acl_Resource('Read'));
        $this->addResource(new Zend_Acl_Resource('MeterReading'));

        $this->allow('decline', 'Decline');
        $this->allow('agent', 'Agent');
        $this->allow('read', 'Read');
        $this->allow('meterReading', 'MeterReading');
        $this->allow('user', 'User');
        $this->allow('admin', 'Admin');
    }
}
