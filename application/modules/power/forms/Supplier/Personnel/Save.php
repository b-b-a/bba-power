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
class Power_Form_Supplier_Personnel_Save extends BBA_Dojo_Form_Abstract
{
    public function init()
    {
    	$this->addElementPrefixPath(
    		'Power_Filter',
    		APPLICATION_PATH . '/modules/power/models/Filter/',
    		'filter'
    	);
    	
        $this->setName('supplier-personnel');

        // supplier contact to do.

        $table = $this->getModel()->getDbTable('tables');
        $list = $table->getSelectListByName('SupplierPers_type');
        $multiOptions[0] = 'Select a type';
        foreach($list as $row) {
            $multiOptions[$row->tables_key] = $row->tables_value;
        }

        $this->addElement('FilteringSelect', 'supplierPers_type', array(
            'label'     => 'Type:',
            'filters'   => array('StripTags', 'StringTrim'),
            'autocomplete' => false,
            'multiOptions'  => $multiOptions,
            'required'  => true,
        ));

        $this->addElement('ValidationTextBox', 'supplierPers_name', array(
            'label'     => 'Name:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('ValidationTextBox', 'supplierPers_position', array(
            'label'     => 'Position:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('TextBox', 'supplierPers_phone', array(
            'label'     => 'Phone:',
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('TextBox', 'supplierPers_email', array(
            'label'         => 'Email:',
            'filters'       => array('StripTags', 'StringTrim', 'StringToLower'),
            'validators'    => array(
                array('EmailAddress', true),
                array('Db_NoRecordExists', false, array(
                    'table' => 'supplier_personnel',
                    'field' => 'supplierPers_email'
                ))
            )
        ));

        $this->addElement('TextBox', 'supplierPers_address1', array(
            'label'     => 'Address 1:',
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('TextBox', 'supplierPers_address2', array(
            'label'     => 'Address 2:',
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('TextBox', 'supplierPers_address3', array(
            'label'     => 'Town/City:',
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('TextBox', 'supplierPers_postcode', array(
            'label'         => 'Postcode:',
            'filters'       => array('StripTags', 'StringTrim', 'Postcode'),
            'validators'    => array(
                array('PostCode', true, array(
                    'locale' => 'en_GB'
                ))
            )
        ));

        $this->addHiddenElement('supplierPers_idSupplierPersonnel', '');
        $this->addHiddenElement('supplierPers_idSupplier', '');
    }
}
