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
 * @subpackage Form_Tender
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Save.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Tender
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Tender_Save extends Power_Form_Dojo_Abstract
{
    protected $_defaultDecorators = array(
		'Description',
		'FormElements',
		array(
			'HtmlTag',
			array(
				'tag'   => 'table',
				'class' => 'zend_form'
			)
		)
	);
	
	protected $_hiddenDecorators = array('ViewHelper');

    public function init()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();

        if ($request->getPost('tender_idTender')) {
            $tenderId = $request->getPost('tender_idTender');
            $row = $this->_model->getTenderById($tenderId);
            $supplier = '/supplierId/' . $row->tender_idSupplier;
        } else {
            $supplier = null;
        }
        
        $this->addHiddenElement('tender_idTender', '');
        $this->addHiddenElement('tender_idContract', '');

        $this->addElement('FilteringSelect', 'tender_idSupplier', array(
            'label' => 'Supplier:',
            'filters' => array('StripTags', 'StringTrim'),
            'autoComplete' => false,
            'hasDownArrow' => true,
            'storeId' => 'supplierStore',
            'storeType' => 'dojo.data.ItemFileReadStore',
            'storeParams' => array('url' => "supplier/data-store/type/supplierList"),
            'dijitParams' => array(
                'searchAttr' => 'supplier_name',
                'promptMessage' => 'Select a Supplier'
            ),
            'attribs' => array(
                'onChange' => 'bba.Contract.changeSupplierPersonnel(this.value);'
            ),
            'value' => '0',
            'required' => true,
            'validators' => array(
                array('GreaterThan', true, array(
                    'min' => '0',
                    'message' => 'Please select a supplier.'
                ))
            ),
            'ErrorMessages' => array('Please select a supplier.'),
        ));


        $this->addElement('FilteringSelect', 'tender_idSupplierPersonnel', array(
            'label' => 'Supplier Liaison:',
            'filters' => array('StripTags', 'StringTrim'),
            'autoComplete' => false,
            'hasDownArrow' => true,
            'storeId' => 'supplierPersonnelStore',
            'storeType' => 'dojo.data.ItemFileReadStore',
            'storeParams' => array(
                'url' => "supplier/data-store/type/supplierPersonnel" . $supplier
            ),
            'dijitParams' => array(
                'searchAttr' => 'supplierPers_name',
                'promptMessage' => 'Select a Supplier Contact'
            ),
            'required' => false,
            'value' => '0',
        ));
        
        $this->addElement('BBAPowerTextBox', 'tender_reference', array(
        	'label'     => 'Tender Ref:',
        	'required'  => false,
        	'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('NumberTextBox', 'tender_periodContract', array(
            'label' => 'Contract Period:',
            'constraints' => array(
                'min' => 0
            ),
            'required' => true,
            'filters' => array('StripTags', 'StringTrim'),
            'dijitParams' => array(
                'promptMessage' => 'Enter contract period (Months)',
                'style' => 'width:50px'
            ),
            'Description' => '(Months)'
        ));

        $this->addElement('ValidationTextBox', 'tender_dateExpiresQuote', array(
            'label' => 'Quote Expiry Date:',
            'formatLength' => 'short',
            'filters' => array('StripTags', 'StringTrim'),
            'validators' => array(
                array('Date', true, array(
                    'format' => 'dd/MM/yyyy'
                ))
            ),
            'required' => false,
            'value' => date("j/n/Y"),
            'dijitParams' => array(
                'promptMessage' => 'Enter expiry date for this tender.',
                'style' => 'width:80px'
            )
        ));

        $this->addElement('NumberTextBox', 'tender_chargeStanding', array(
            'label' => 'Standing Charge:',
            'constraints' => array(
                'min' => 0
            ),
            'required' => true,
            'value' => 0,
            'filters' => array('StripTags', 'StringTrim'),
            'dijitParams' => array(
                'promptMessage' => 'Enter Standing charge (£ / Months)',
                'style' => 'width:50px'
            ),
            'Description' => '(£ / Months)'
        ));

        $this->addElement('NumberTextBox', 'tender_priceUnitDay', array(
            'label' => 'Unit Price - Day:',
            'constraints' => array(
                'min' => 0
            ),
            'required' => true,
            'value' => 0,
            'filters' => array('StripTags', 'StringTrim'),
            'dijitParams' => array(
                'promptMessage' => 'Enter unit price for day rate (Pence / Unit)',
                'style' => 'width:50px'
            ),
            'Description' => '(Pence / Unit)'
        ));

        $this->addElement('NumberTextBox', 'tender_priceUnitNight', array(
            'label' => 'Unit Price - Night:',
            'constraints' => array(
                'min' => 0
            ),
            'required' => true,
            'value' => 0,
            'filters' => array('StripTags', 'StringTrim'),
            'dijitParams' => array(
                'promptMessage' => 'Enter unit price for night rate (Pence / Unit)',
                'style' => 'width:50px'
            ),
            'Description' => '(Pence / Unit)'
        ));

        $this->addElement('NumberTextBox', 'tender_priceUnitOther', array(
            'label' => 'Unit Price - Other & Gas:',
            'constraints' => array(
                'min' => 0
            ),
            'required' => true,
            'value' => 0,
            'filters' => array('StripTags', 'StringTrim'),
            'dijitParams' => array(
                'promptMessage' => 'Enter unit price for other rate (Pence / Unit)',
                'style' => 'width:50px'
            ),
            'Description' => '(Pence / Unit)'
        ));

        $this->addElement('NumberTextBox', 'tender_chargeCapacity', array(
            'label' => 'Capacity Charge:',
            'constraints' => array(
                'min' => 0
            ),
            'required' => true,
            'value' => 0,
            'filters' => array('StripTags', 'StringTrim'),
            'dijitParams' => array(
                'promptMessage' => 'Enter capacity charge (£ / kVA)',
                'style' => 'width:50px'
            ),
            'Description' => '(£ / kVA)'
        ));

        $this->addElement('NumberTextBox', 'tender_chargeSettlement', array(
            'label' => 'Settlement Charge:',
            'constraints' => array(
                'min' => 0
            ),
            'required' => true,
            'value' => 0,
            'filters' => array('StripTags', 'StringTrim'),
            'dijitParams' => array(
                'promptMessage' => 'Enter settlement charge (£ / Month)',
                'style' => 'width:50px'
            ),
            'Description' => '(£ / Month)'
        ));

        $this->addElement('NumberTextBox', 'tender_commission', array(
            'label' => 'Commission Rate:',
            'constraints' => array(
                'min' => 0
            ),
            'required' => true,
            'value' => 0,
            'filters' => array('StripTags', 'StringTrim'),
            'dijitParams' => array(
                'promptMessage' => 'Enter commission rate (Pence / Unit)',
                'style' => 'width:50px'
            ),
            'Description' => '(Pence / Unit)'
        ));

        $this->addElement('NumberTextBox', 'tender_fee', array(
            'label' => 'Commission Fee:',
            'constraints' => array(
                'min' => 0
            ),
            'required' => true,
            'value' => 0,
            'filters' => array('StripTags', 'StringTrim'),
            'dijitParams' => array(
                'promptMessage' => 'Enter commission fee (£ / Year)',
                'style' => 'width:50px'
            ),
            'Description' => '(£ / Year)'
        ));

        $this->addElement('BBAPowerSimpleTextarea', 'tender_desc', array(
            'label' => 'Description:',
            'required' => false,
            'filters' => array('StripTags', 'StringTrim')
        ));
        
        $this->addElement('Button', 'tenderFormSubmitButton', array(
        	'required'  => false,
        	'ignore'    => true,
        	'decorators'    => $this->_submitDecorators,
        	'label'     => 'Submit',
        	'value'     => 'Submit',
        	'dijitParams'   => array(
        		'onClick' => "return dijit.byId('tenderForm').validate();;"
        	),
        	'attribs' => array('type' => 'submit')
        ));
        
        $this->addElement('Button', 'tenderFormCancelButton', array(
        	'required'  => false,
        	'ignore'    => true,
        	'decorators'    => $this->_submitDecorators,
        	'label'     => 'Cancel',
        	'value'     => 'Cancel',
        	'dijitParams'   => array(
        		'onClick' => "return bba.closeDialog(dijit.byId('tenderForm'));"
        	)
        ));
        
        $this->addDisplayGroup(
        	array(
        		'tenderFormSubmitButton',
        		'tenderFormCancelButton',
        	),
        	'Buttons',
        	array(
        		'decorators' => array(
        			'FormElements',
        			array(
        				array('data' => 'HtmlTag'),
        				array(
        					'tag' => 'td',
        					'class' => 'submitElement',
        					'colspan' => '2'
        				)
        			),
        			array(
        				array('row' => 'HtmlTag'),
        				array(
        					'tag' => 'tr',
        					'class' => 'form_row'
        				)
        			)
        		)
        	)
        );
    }

}
