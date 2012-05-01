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
class Power_Form_Supplier_Save extends ZendSF_Dojo_Form_Abstract
{
    public function init()
    {
        $this->setName('supplier');

        $this->addElement('ValidationTextBox', 'supplier_name', array(
            'label'     => 'Supplier Name:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('ValidationTextBox', 'supplier_nameShort', array(
            'label'     => 'Supplier Short Name:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('ValidationTextBox', 'supplier_address1', array(
            'label'     => 'Address 1:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('ValidationTextBox', 'supplier_address2', array(
            'label'     => 'Address 2:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('ValidationTextBox', 'supplier_address3', array(
            'label'     => 'Town/City:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('ValidationTextBox', 'supplier_postcode', array(
            'label'         => 'Postcode:',
            'required'      => true,
            'filters'       => array('StripTags', 'StringTrim', 'StringToUpper'),
            'validators'    => array(
                array('PostCode', true, array(
                    'locale' => 'en_GB'
                ))
            )
        ));

        $this->addElement('NumberTextBox', 'supplier_commission', array(
            'label'         => 'Normal Commission:',
            'constraints'   => array(
                'min'       => 0
            ),
            'required'  => false,
            'value'     => 0,
            'filters'   => array('StripTags', 'StringTrim'),
            'dijitParams'   => array(
                'promptMessage' => 'Enter standard commission rate (Pence / Unit)',
                'style'         => 'width:50px'
            ),
            'Description'   => '(Pence / Unit)'
        ));

        $request = Zend_Controller_Front::getInstance()->getRequest();

        if ($request->getPost('supplier_idSupplier')) {

            $list = $this->getModel()->getSupplierById(
                $request->getPost('supplier_idSupplier')
            )->getSupplierContacts();

            // reset options
            $multiOptions = array(0 => ($list->count() > 0) ? 'Please Select Someone' : 'No Supplier Personnel available');
            foreach($list as $row) {
                $multiOptions[$row->supplierCo_idSupplierContact] = $row->supplierCo_name;
            }

            $this->addElement('FilteringSelect', 'supplier_idSupplierContact', array(
                'label'     => 'Main Liaison:',
                'filters'   => array('StripTags', 'StringTrim'),
                'atuocomplete' => false,
                'multiOptions'  => $multiOptions
            ));
        }

        $this->addHiddenElement('supplier_idSupplier', '');
    }
}
