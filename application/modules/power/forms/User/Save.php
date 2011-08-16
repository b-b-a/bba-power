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

        $this->addElement('TextBox', 'user_name', array(
            'label' => 'Username:',
            'required'      => true
        ));

        $this->addElement('PasswordTextBox', 'user_password', array(
            'label' => 'Password:',
            'required' => true
        ));

        $this->addElement('TextBox', 'user_fullName', array(
            'label' => 'Full Name:',
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

        $this->addElement('FilteringSelect', 'user_role', array(
            'label'         => 'Role:',
            'validators'    => array(
                array('Alpha', true)
            ),
            'atuocomplete' => false,
            'errorMessages'  => array('Please select a role for this user.'),
            'multiOptions'  => $multiOptions,
            'required'      => true
        ));

        $this->addHiddenElement('user_idUser', '');

        $this->addSubmit('Save');
    }

}
