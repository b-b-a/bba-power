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
class Power_Form_User_Save extends BBA_Dojo_Form_Abstract
{
    public function init()
    {
        $this->setName('userSave');

        $this->addElement('ValidationTextBox', 'user_name', array(
            'label'     => 'Username:',
            'filters'   => array('StripTags', 'StringTrim'),
            'required'  => true
        ));

        $this->addElement('PasswordTextBox', 'user_password', array(
            'label'     => 'Password:',
            'filters'   => array('StripTags', 'StringTrim'),
            'required'  => true
        ));

        $this->addElement('ValidationTextBox', 'user_fullName', array(
            'label'     => 'Full Name:',
            'filters'   => array('StripTags', 'StringTrim'),
            'required'  => true
        ));

        $multiOptions = array(
            0 => 'Select Role'
        );
        
        foreach (Power_Model_Acl_Power::$bbaRoles as $key => $value) {
            $multiOptions[$key] = $value['label'];
        }

        $this->addElement('FilteringSelect', 'user_role', array(
            'label'         => 'Role:',
            'validators'    => array(
                array('Alpha', true)
            ),
            'atuocomplete'  => false,
            'errorMessages' => array('Please select a role for this user.'),
            'multiOptions'  => $multiOptions,
            'required'      => true
        ));
        
        $multiOptions = array(
            0 => 'All'
        );
        
        $list = $this->getModel()->getDbTable('client')
            ->fetchAll(null, 'client_name ASC');
        
        foreach ($list as $row) {
            $multiOptions[$row->client_idClient] = $row->client_name;
        }
        
        $this->addElement('FilteringSelect', 'user_accessClient', array(
            'label'         => 'Access Client:',
            'validators'    => array(
                array('Digits', true)
            ),
            'atuocomplete'  => false,
            'multiOptions'  => $multiOptions,
            'required'      => false,
        ));

        $this->addHiddenElement('user_idUser', '');
    }
}
