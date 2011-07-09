<?php
/**
 * Save.php
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
 * @subpackage Form
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Save.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_User_Save extends ZendSF_Form_Abstract
{
    public function init()
    {
        $this->setName('userSave');

        $this->addElement('text', 'us_username', array(
            'label' => 'Username:',
            'required'      => true
        ));

        $this->addElement('password', 'us_password', array(
            'label' => 'Password:',
            'required' => true
        ));

        $this->addElement('text', 'us_real_name', array(
            'label' => 'Real Name:',
            'required'      => true
        ));

        $multiOptions = array(
            0               => 'Select Role',
            'agent'         => 'Agent',
            'read'          => 'Read',
            'meterReading'  => 'Meter Reading',
            'user'          => 'User',
            'admin'         => 'Admin'
        );

        $this->addElement('select', 'us_role', array(
            'label'         => 'Role:',
            'validators'    => array(
                array('Alpha', true)
            ),
            'errorMessages'  => array('Please select a role for this user.'),
            'MultiOptions'  => $multiOptions,
            'required'      => true
        ));

        $this->addHiddenElement('us_id', '');

        $this->addSubmit('Save');
    }

}
