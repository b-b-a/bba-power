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
 * @subpackage Form_Contract
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Base.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Contract
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Contract_Base extends ZendSF_Dojo_Form_Abstract
{	
	protected $_hiddenDecorators = array('ViewHelper');
	
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
	
	protected $_request;
	
	protected $_contractDoc;
	
    public function init()
    {
        $this->setName('contract');
        $this->setAttrib('enctype', 'multipart/form-data');
        
        $this->_request = Zend_Controller_Front::getInstance()->getRequest();
        $this->_contractDoc = new Power_Form_Doc_Contract(array('model' => $this->_model));
        
        $this->addHiddenElement('contract_idContract', '');
        $this->addHiddenElement('meter_idMeter', '');
        $this->addHiddenElement('meter_type', '');
        $this->addHiddenElement('contract_idContractPrevious', '0');
        $this->addHiddenElement('type', '');
        
        $this->addElement('FilteringSelect', 'contract_idClient', array(
    		'label'         => 'Client:',
    		'filters'       => array('StripTags', 'StringTrim'),
    		'autoComplete'  => false,
    		'hasDownArrow'  => true,
    		'multiOptions'  => $this->_getClientMultiOptions(),
    		'required'      => true,
    		'value'         => '0',
    		'validators'    => array(
    			array('GreaterThan', true, array(
    				'min'       => '0',
    				'message'   => 'Please select a client.'
    			))
    		),
    		'ErrorMessages' => array('Please select a client.'),
    		'order'			=> 10
        ));

        $this->addElement('RadioButton', 'contract_type', array(
            'label'         => 'Type:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autocomplete'  => false,
            'multiOptions'  => $this->_getContractType(),
            'required'      => true,
            'validators'    => array(
                array('GreaterThan', true, array(
                    'min'       => '0',
                    'message'   => 'Please select a contract type.'
                ))
            ),
            'ErrorMessages' => array('Please select a contract type.'),
        	'order'			=> 20,
        	'value'         => '0'
        ));

        $this->addElement('FilteringSelect', 'contract_status', array(
            'label'         => 'Status:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autocomplete'  => false,
            'multiOptions'  => $this->_getContractStatus(),
            'required'      => true,
            'validators'    => array(
                array('GreaterThan', true, array(
                    'min'       => '0',
                    'message'   => 'Please select a status.'
                ))
            ),
            'ErrorMessages' => array('Please select a status.'),
        	'order'			=> 30
        ));

        $this->addElement('ValidationTextBox', 'contract_dateStart', array(
            'label'         => 'Start Date:',
            'formatLength'  => 'short',
            'filters'       => array('StripTags', 'StringTrim'),
            'validators'    => array(
                array('Date', true, array(
                    'format' => 'dd/MM/yyyy'
                ))
            ),
            'required'      => true,
        	'order'			=> 40,
        	'value'         => ''
        ));

        $this->addElement('ZendSFDojoTextBox', 'contract_dateEnd', array(
            'label'         => 'End Date:',
            'formatLength'  => 'short',
            'filters'       => array('StripTags', 'StringTrim'),
            'validators'    => array(
                array('Date', true, array(
                    'format' => 'dd/MM/yyyy'
                ))
            ),
            'required'      => false,
        	'order'			=> 50,
        	'value'         => ''
        ));

        $this->addElement('ZendSFDojoTextBox', 'contract_dateDecision', array(
            'label'         => 'Tender Decision Date:',
            'formatLength'  => 'short',
            'filters'       => array('StripTags', 'StringTrim'),
            'validators'    => array(
                array('Date', true, array(
                    'format' => 'dd/MM/yyyy'
                ))
            ),
            'required'      => false,
        	'order'			=> 60
        ));

        $this->addElement('ZendSFDojoTextBox', 'contract_reference', array(
            'label'     => 'Contract Ref:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim'),
        	'order'			=> 70
        ));

        $this->addElement('ZendSFDojoTextBox', 'contract_numberCustomer', array(
            'label'     => 'Customer No:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim'),
        	'order'			=> 80
        ));

        $this->addElement('NumberTextBox', 'contract_periodBill', array(
            'label'         => 'Billing Period:',
            'constraints'   => array(
                'min'   => 0
            ),
            'required'      => false,
            'dijitParams'   => array(
                'promptMessage' => 'Enter contract billing period (Months)',
                'style'         => 'width:50px'
            ),
            'Description'   => '(Months)',
            'filters'       => array('StripTags', 'StringTrim'),
            'value'         => 0,
        	'order'			=> 90
        ));

        $this->addElement('ZendSFDojoSimpleTextarea', 'contract_desc', array(
            'label'     => 'Description:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim'),
            'order'			=> 100
        ));
        
        $this->addElement('Button', 'contractFormSubmitButton', array(
        		'required'  => false,
        		'ignore'    => true,
        		'decorators'    => $this->_submitDecorators,
        		'label'     => 'Submit',
        		'value'     => 'Submit',
        		'dijitParams'   => array(
        			'onClick' => "return bba.Contract.validateContractForm()"
        		),
        		'attribs' => array('type' => 'submit')
        ));
        
        $this->addElement('Button', 'contractFormCancelButton', array(
        		'required'  => false,
        		'ignore'    => true,
        		'decorators'    => $this->_submitDecorators,
        		'label'     => 'Cancel',
        		'value'     => 'Cancel',
        		'dijitParams'   => array(
        			'onClick' => "return bba.closeDialog(dijit.byId('contractForm'))"
        		)
        ));
        
        $this->addDisplayGroup(
        		array(
        				'contractFormSubmitButton',
        				'contractFormCancelButton',
        		),
        		'Buttons',
        		array(
        				'decorators' => $this->_submitGroupDecorators,
        				'order'		 => 110
        		)
        );
        
    }
    
    protected function _getClientMultiOptions()
    {
    	$list = $this->_model->getDbTable('client')->fetchAll(null, 'client_name ASC');
    	
    	$multiOptions = array('0' => ($list->count() > 0) ? 'Please Select A Client' : 'No Clients Available');
    	foreach($list as $row) {
    		$multiOptions[$row->client_idClient] = $row->client_name;
    	}
    	
    	return $multiOptions;
    }
    
    protected function _getContractType()
    {
    	$multiOptions = array();
    	
    	$table = $this->getModel()->getDbTable('tables');
    	$list = $table->getSelectListByName('contract_type');
    	
    	if ($this->_request->getParam('meter_type')) {
    		$type = strtolower($this->_request->getParam('meter_type'));
    	} else {
    		$type = null;
    	}
    	
    	foreach($list as $row) {
    		if ($type) {
    			$conType = explode('-', $row->tables_key);
    			if ($conType[0] != $type) continue;
    		}
    		 
    		$multiOptions[$row->tables_key] = $row->tables_value;
    	}
    	
    	return $multiOptions;
    }
    
    protected function _getContractStatus()
    {
    	$table = $this->getModel()->getDbTable('tables');
    	
    	if ($this->_request->getParam('type') == 'add') {
    		$multiOptions = array();
    		$list = $table->getTableItemByNameKey('contract_status', 'new');
    	} else {
    		$multiOptions = array(0 => 'Select a status');
    		$list = $table->getSelectListByName('contract_status');
    	}
    	
    	foreach($list as $row) {
    		$multiOptions[$row->tables_key] = $row->tables_value;
    	}
    	
    	return $multiOptions;
    }
}
