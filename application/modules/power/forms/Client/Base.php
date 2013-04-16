<?php
/**
 * Base.php
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
 * Form Class Base.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Client
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Client_Base extends Power_Form_Dojo_Abstract
{	
	protected $_hiddenDecorators = array('ViewHelper');
	
	protected $_companyDecorators =  array(
		'DijitElement',
		'Errors',
		'Description',
		array(
			array('data' => 'HtmlTag'),
			array(
				'tag' => 'td',
				'class' => 'element',
				'openOnly' => true
			)
		),
		array(
			'Label',
			array(
				'tag' => 'th',
				'class' => 'label'
			)
		)
	);
	
	protected $_checkboxDecorators = array(
        'DijitElement',
        array(
			'Label',
		    array(
		    	'tag' => 'span',
		    	'class' => 'label'
			)
		),
		array(
			'HtmlTag',
			array(
				'tag' => 'td',
				'closeOnly' => true
        					
        	)
        )
	);
	
	protected $_companyGroupDecorators = array(
        'FormElements',
        array(
        	array('row' => 'HtmlTag'),
        	array(
        		'tag' => 'tr',
        		'class' => 'form_row'
        	)
        )
	);
	
    public function init()
    {
        $this->setAttrib('enctype', 'multipart/form-data');

        $this->addElement('ValidationTextBox', 'client_name', array(
            'label'         => 'Client Name:',
            'required'      => true,
            'filters'       => array('StripTags', 'StringTrim'),
            'validators'    => array(
                //array('Alnum', true, array('allowWhiteSpace' => true)),
                array('StringLength', true, array('max' => 64))
            ),
            'dijitParams'   => array(
                'promptMessage' => 'Enter the clients name.'
            ),

        ));
        
        $this->addElement('ValidationTextBox', 'client_numberCompany', array(
        	'label'         => 'Company Number:',
        	'required'      => true,
        	'filters'       => array('StripTags', 'StringTrim'),
        	'validators'    => array(
        		array('Alnum', true, array('allowWhiteSpace' => true)),
        		array('StringLength', true, array('max' => 32))
        	),
        	'dijitParams'   => array(
        		'promptMessage' => 'Enter the client company number.'
        	),
        	'decorators'	=> $this->_companyDecorators
        ));
        
        $this->addElement('BBAPowerCheckBox', 'client_registeredCompany', array(
        	'label'         => 'Not a Registered Company:',
        	'required'		=> false,
        	'decorators' 	=> $this->_checkboxDecorators,
        	'checkedValue'   => 1,
        	'uncheckedValue' => 0,
        ));
        
        $this->addDisplayGroup(
        	array(
        		'client_numberCompany',
        		'client_registeredCompany',
        	),
        	'company',
        	array(
        		'decorators' => $this->_companyGroupDecorators
        	)
        );
        
        $this->addElement('ValidationTextBox', 'client_numberVAT', array(
        	'label'         => 'VAT Number:',
        	'required'      => true,
        	'filters'       => array('StripTags', 'StringTrim'),
        	'validators'    => array(
        		array('Alnum', true, array('allowWhiteSpace' => true)),
        		array('StringLength', true, array('max' => 32))
        	),
        	'dijitParams'   => array(
        		'promptMessage' => 'Enter the client VAT number.'
        	),
        	'decorators' 	=> $this->_companyDecorators
        ));
        
        $this->addElement('BBAPowerCheckBox', 'client_registeredVAT', array(
        	'label'         => 'Not VAT Registered:',
        	'required'		=> false,
        	'checkedValue'   => 1,
        	'uncheckedValue' => 0,
        	'decorators' 	=> $this->_checkboxDecorators,
        ));
        
        $this->addDisplayGroup(
        	array(
        		'client_numberVAT',
        		'client_registeredVAT',
        	),
        	'vat',
        	array(
        		'decorators' => $this->_companyGroupDecorators
        	)	
        );
        
        $multiOptions = array();
        
        $table = $this->getModel()->getDbTable('tables');
        $list = $table->getSelectListByName('client_methodPay');
        
        foreach($list as $row) {
        	$multiOptions[$row->tables_key] = $row->tables_value;
        }
        
        $this->addElement('RadioButton', 'client_methodPay', array(
        	'label'         => 'Payment Method:',
        	'filters'       => array('StripTags', 'StringTrim'),
        	'autocomplete'  => false,
        	'multiOptions'  => $multiOptions,
        	'required'      => true,
        	'validators'    => array(
        		array('GreaterThan', true, array(
        			'min'       => '0',
        			'message'   => 'Please select a payment method.'
        		))
        	),
        	'ErrorMessages' => array('Please select a payment method.'),
        	'dijitParams'   => array(
        		'promptMessage' => 'Please select a payment method.'
        	)
        ));
        
        $clientdoc = new Power_Form_Doc_Client(array('model' => $this->_model));
        $this->addElement($clientdoc->getElement('client_docLoa'));

        $this->addElement('ValidationTextBox', 'client_dateExpiryLoa', array(
            'label'         => 'LoA Expiry Date:',
            'formatLength'  => 'short',
            'filters'       => array('StripTags', 'StringTrim'),
            'validators'    => array(
                array('Date', true, array(
                    'format' => 'dd/MM/yyyy'
                ))
            ),
            'required'      => false,
            'dijitParams'   => array(
                'promptMessage' => 'Enter the date that the letter of authority expires.'
            ),
            'attribs'       => array('style' => 'width: 80px;')
        ));

        $this->addElement('BBAPowerSimpleTextarea', 'client_desc', array(
            'label'         => 'Description:',
            'required'      => false,
            'filters'       => array('StripTags', 'StringTrim')
        ));
        
    }
}
