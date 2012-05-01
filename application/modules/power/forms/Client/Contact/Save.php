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
 * @subpackage Form_Client
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Save.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Client
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Client_Contact_Save extends ZendSF_Dojo_Form_Abstract
{
    public function init()
    {
        $this->setName('client-contact');

        $table = $this->getModel()->getDbTable('tables');

        $list = $table->getSelectListByName('ClientCo_type');
        $multiOptions[0] = 'Select a type';
        foreach($list as $row) {
            $multiOptions[$row->tables_key] = $row->tables_value;
        }

        $this->addElement('FilteringSelect', 'clientCo_type', array(
            'label'         => 'Type:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autocomplete'  => false,
            'multiOptions'  => $multiOptions,
            'required'      => true,
            'validators'    => array(
                array('GreaterThan', true, array(
                    'min'       => '0',
                    'message'   => 'Please select an contact type.'
                ))
            ),
            'ErrorMessages' => array('Please select an contact type.'),
            'dijitParams'   => array(
                'promptMessage' => 'Choose a client contact type.'
            )
        ));

        $this->addElement('ValidationTextBox', 'clientCo_name', array(
            'label'         => 'Name:',
            'required'      => true,
            'filters'       => array('StripTags', 'StringTrim'),
            'validators'    => array(
                //array('Alpha', true, array('allowWhiteSpace' => true))
            ),
            'dijitParams'   => array(
                'promptMessage' => 'Choose a client contact.'
            )
        ));

        $this->addElement('TextBox', 'clientCo_position', array(
            'label'         => 'Position:',
            'filters'       => array('StripTags', 'StringTrim'),
            'validators'    => array(
                //array('Alpha', true, array('allowWhiteSpace' => true))
            ),
            'dijitParams'   => array(
                'promptMessage' => 'Enter a clients position.'
            )
        ));

        $this->addElement('TextBox', 'clientCo_phone', array(
            'label'         => 'Phone:',
            'filters'       => array('StripTags', 'StringTrim'),
            'dijitParams'   => array(
                'promptMessage' => 'Enter clients phone No.'
            )
        ));

        $this->addElement('TextBox', 'clientCo_email', array(
            'label'         => 'email:',
            'filters'       => array('StripTags', 'StringTrim', 'StringToLower'),
            'validators'    => array(
                array('EmailAddress', true),
                array('Db_NoRecordExists', false, array(
                    'table' => 'client_contact',
                    'field' => 'clientCo_email'
                ))
            ),
            'dijitParams'   => array(
                'promptMessage' => 'Enter clients Email.'
            )
        ));

        $this->addHiddenElement('clientCo_idAddress', '');

        $request = Zend_Controller_Front::getInstance()->getRequest();

        if (!$request->getPost('clientCo_idAddress')) {

            $list = $this->getModel()->getClientAddressesByClientId(
                $request->getPost('clientCo_idClient')
            );

            // reset options
            $multiOptions = array(0 => ($list->count() > 0) ? 'Please Select An Address' : 'No Addresses Available');

            foreach($list as $row) {
                $multiOptions[$row->clientAd_idAddress] = $row->getAddress1AndPostcode();
            }

            $this->addElement('FilteringSelect', 'clientCo_idAddress', array(
                'label'         => 'Address:',
                'filters'       => array('StripTags', 'StringTrim'),
                'atuocomplete'  => false,
                'multiOptions'  => $multiOptions,
                'required'      => true,
                'ErrorMessages' => array('Please select a client address.'),
                'dijitParams'   => array(
                    'promptMessage' => 'Choose a address.'
                ),
                'validators'    => array(
                    /*array('GreaterThan', true, array(
                        'min'       => '0',
                        'message'   => 'Please select an contact address.'
                    ))*/
                ),
                'ErrorMessages' => array('Please select an contact address.'),
            ));
        } else {
            $this->addHiddenElement('clientCo_idAddress', '');
        }

        $this->addHiddenElement('clientCo_idClientContact', '');
        $this->addHiddenElement('clientCo_idClient', '');
    }
}
