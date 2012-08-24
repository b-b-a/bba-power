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
 * @subpackage Form_Meter
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Base.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Meter
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Meter_Base extends ZendSF_Dojo_Form_Abstract
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
	
    public function init()
    {
        // add path to custom validators.
        $this->addElementPrefixPath(
            'Power_Validate',
            APPLICATION_PATH . '/modules/power/models/Validate/',
            'validate'
        );

        $this->setName('meter');

        $multiOptions = array();

        $table = $this->getModel()->getDbTable('tables');
        $list = $table->getSelectListByName('meter_type');

        foreach($list as $row) {
            $multiOptions[$row->tables_key] = $row->tables_value;
        }

        $this->addElement('RadioButton', 'meter_type', array(
            'label'         => 'Type:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autocomplete'  => false,
            'multiOptions'  => $multiOptions,
            'required'      => true,
            'ErrorMessages' => array('Please select a meter type.'),
        ));

        $multiOptions = array();

        $list = $table->getSelectListByName('meter_status');
        $multiOptions[0] = 'Select a status';
        foreach($list as $row) {
            $multiOptions[$row->tables_key] = $row->tables_value;
        }

        $this->addElement('FilteringSelect', 'meter_status', array(
            'label'         => 'Status:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autocomplete'  => false,
            'multiOptions'  => $multiOptions,
            'required'      => true,
            'dijitParams'   => array(
                'promptMessage' => 'Select a Status'
            ),
            'validators'    => array(
                array('GreaterThan', true, array(
                    'min'       => '0',
                    'message'   => 'Please select a status.'
                ))
            ),
            'ErrorMessages' => array('Please select a status.'),
        ));

        $this->addElement('ValidationTextBox', 'meter_numberTop', array(
            'label'     => 'Top No:',
            'required'  => false,
            'filters'   => array(
            	'StripTags',
            	'StringTrim',
            	array('PregReplace', array('match' => '/\s+|-+/', 'replace' => ''))
            ),
            'dijitParams'   => array(
                'promptMessage' => 'Enter the meter top number.'
            ),
            'validators'    => array(
            	array('Digits', false),
            	array('NumberTop', false)
            ),
            'attribs'       => array('style' => 'width: 150px;')
        ));

        $this->addElement('ValidationTextBox', 'meter_numberMain', array(
            'label'     => 'Main No:',
            'filters'   => array(
                'StripTags',
                'StringTrim',
                array('PregReplace', array('match' => '/\s+|-+/', 'replace' => ''))
            ),
            'dijitParams'   => array(
                'promptMessage' => 'Enter the meter main number.'
            ),
            'validators'    => array(
                array('Digits', false),
                array('NumberMain', false, array($this->getModel()))
            ),
            'attribs'       => array('style' => 'width: 150px;')
        ));
        
        $this->addElement('ValidationTextBox', 'meter_numberSerial', array(
        	'label'         => 'Serial No:',
        	'required'      => false,
        	'filters'       => array('StripTags', 'StringTrim'),
        	'dijitParams'   => array(
        		'promptMessage' => 'Enter the meter serial number.'
        	),
        	'validators'    => array(
        		array('StringLength', true, array('max' => 16)),
        		array('NumberSerial', false, array($this->getModel()))
        	),
        	'attribs'       => array('style' => 'width: 150px;')
        ));

        $this->addElement('ValidationTextBox', 'meter_capacity', array(
            'label'     => 'Supply Capacity:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim'),
            'dijitParams'   => array(
                'promptMessage' => 'Enter the meter capcity (kVA).'
            ),
            'validators'    => array(
                array('StringLength', true, array('max' => 11))
            ),
            'Description'   => '(kVA)',
            'attribs'       => array('style' => 'width: 100px;')
        ));

        $this->addElement('ZendSFDojoSimpleTextarea', 'meter_desc', array(
            'label'         => 'Description:',
            'required'      => false,
            'filters'       => array('StripTags', 'StringTrim')
        ));

        $this->addHiddenElement('meter_idMeter', '');
        $this->addHiddenElement('meter_idSite', '');
        
        $this->addElement('Button', 'meterFormSubmitButton', array(
        	'required'  => false,
        	'ignore'    => true,
        	'decorators'    => $this->_submitDecorators,
        	'label'     => 'Submit',
        	'value'     => 'Submit',
        	'dijitParams'   => array(
        		'onClick' => "return bba.Meter.meterFormValidate()"
        	),
        	'attribs' => array('type' => 'submit')
        ));
        
        $this->addElement('Button', 'meterFormCancelButton', array(
        	'required'  => false,
        	'ignore'    => true,
        	'decorators'    => $this->_submitDecorators,
        	'label'     => 'Cancel',
        	'value'     => 'Cancel',
        	'dijitParams'   => array(
        		'onClick' => "return bba.closeDialog(dijit.byId('meterForm'))"
        	)
        ));
        
        $this->addDisplayGroup(
        	array(
        		'meterFormSubmitButton',
        		'meterFormCancelButton',
        	),
        	'Buttons',
        	array('decorators' => $this->_submitGroupDecorators)
        );
    }
}
