<?php

/**
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BBA
 *
 * @category   BBA
 * @package    Power
 * @subpackage
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

        $this->addRole(new Zend_Acl_Role('agent'), 'guest');
        $this->addRole(new Zend_Acl_Role('read'));
        $this->addRole(new Zend_Acl_Role('meterReading'), 'read');
        $this->addRole(new Zend_Acl_Role('user'), 'meterReading');
        $this->addRole(new Zend_Acl_Role('admin'), 'user');

        $this->addResource(new Zend_Acl_Resource('Agent'));
        $this->addResource(new Zend_Acl_Resource('Read'));
        $this->addResource(new Zend_Acl_Resource('MeterReading'));

        $this->allow('agent', 'Agent');
        $this->allow('read', 'Read');
        $this->allow('meterReading', 'MeterReading');
        $this->allow('user', 'User');
        $this->allow('admin', 'Admin');
    }
}
