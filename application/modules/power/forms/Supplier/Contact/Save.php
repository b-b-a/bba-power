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
 * @subpackage Form_Supplier
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Save.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Supplier
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Supplier_Contact_Save extends ZendSF_Form_Abstract
{
    public function init()
    {
        $this->setName('supplier-contact');

        // supplier contact to do.

        $table = new Power_Model_Mapper_Tables();
        $list = $table->getSelectListByName('SupplierCo_type');
        $multiOptions[0] = 'Select a type';
        foreach($list as $row) {
            $multiOptions[$row->key] = $row->value;
        }

        $this->addElement('FilteringSelect', 'supplierCo_type', array(
            'label'     => 'Type:',
            'filters'   => array('StripTags', 'StringTrim'),
            'autocomplete' => false,
            'multiOptions'  => $multiOptions,
            'required'  => true,
        ));

        $this->addElement('ValidationTextBox', 'supplierCo_name', array(
            'label'     => 'Name:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('ValidationTextBox', 'supplierCo_position', array(
            'label'     => 'Position:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('ValidationTextBox', 'supplierCo_phone', array(
            'label'     => 'Phone:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('ValidationTextBox', 'supplierCo_email', array(
            'label'         => 'Email:',
            'required'      => true,
            'filters'       => array('StripTags', 'StringTrim', 'StringToLower'),
            'validators'    => array(
                array('EmailAddress', true),
                array('Db_NoRecordExists', false, array(
                    'table' => 'supplier_contact',
                    'field' => 'supplierCo_email'
                ))
            )
        ));

        $this->addElement('ValidationTextBox', 'supplierCo_address1', array(
            'label'     => 'Address 1:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('TextBox', 'supplierCo_address2', array(
            'label'     => 'Address 2:',
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('TextBox', 'supplierCo_address3', array(
            'label'     => 'Address 3:',
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('ValidationTextBox', 'supplierCo_postcode', array(
            'label'         => 'Postcode:',
            'required'      => true,
            'filters'       => array('StripTags', 'StringTrim', 'StringToUpper'),
            'validators'    => array(
                array('PostCode', true, array(
                    'locale' => 'en_GB'
                ))
            )
        ));

        $auth = Zend_Auth::getInstance()->getIdentity();

        $this->addHiddenElement('userId', $auth->getId());
        $this->addHiddenElement('supplierCo_idSupplierContact', '');
        $this->addHiddenElement('supplierCo_idSupplier', '');
    }
}
