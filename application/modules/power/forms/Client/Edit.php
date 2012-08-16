<?php
/**
 * Edit.php
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
 * Form Class Edit.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Client
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Client_Edit extends Power_Form_Client_Base
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

	public function init()
	{	
		parent::init();
		
		$this->setName('clientEditForm');
		
		$this->setDescription('Fill in the form to edit this client.');
		
		$this->addHiddenElement('client_idClient', '');
		$this->addHiddenElement('type', 'edit');
		
		$request = Zend_Controller_Front::getInstance()->getRequest();
		
		
		$list = $this->getModel()->getClientAddressesByClientId(
			$request->getPost('client_idClient')
		);
	
		// reset options
		$multiOptions = array(0 => ($list->count() > 0) ? 'Please Select An Address' : 'No Addresses Available');
	
		foreach($list as $row) {
			$multiOptions[$row->clientAd_idAddress] = $row->getAddress1AndPostcode();
		}
	
		$this->addElement('FilteringSelect', 'client_idAddress', array(
			'label'         => 'Main Address:',
			'filters'       => array('StripTags', 'StringTrim'),
			'atuocomplete'  => false,
			'multiOptions'  => $multiOptions,
			'required'      => true,
			'validators'    => array(
				array('GreaterThan', true, array(
					'min'       => '0',
					'message'   => 'Please select an address.'
				))
			),
			'ErrorMessages' => array('Please select an address.'),
			'dijitParams'   => array(
				'promptMessage' => 'Choose a client address.'
			),
			'order'			=> 1
		));
		
		$this->addElement('FilteringSelect', 'client_idRegAddress', array(
				'label'         => 'Registered Address:',
				'filters'       => array('StripTags', 'StringTrim'),
				'atuocomplete'  => false,
				'multiOptions'  => $multiOptions,
				'required'      => true,
				'validators'    => array(
						array('GreaterThan', true, array(
								'min'       => '0',
								'message'   => 'Please select an address.'
						))
				),
				'ErrorMessages' => array('Please select an address.'),
				'dijitParams'   => array(
						'promptMessage' => 'Choose a client address.'
				),
				'order'			=> 2
		));
	
		$list = $this->getModel()->getClientPersonnelByClientId(
			$request->getPost('client_idClient')
		);
	
		// reset options
		$multiOptions = array(0 => ($list->count() > 0) ? 'Please Select Someone' : 'No Client Personnel Available');
		foreach($list as $row) {
			$multiOptions[$row->clientPers_idClientPersonnel] = $row->clientPers_name;
		}
	
		$this->addElement('FilteringSelect', 'client_idClientPersonnel', array(
			'label'         => 'Main Liaison:',
			'filters'       => array('StripTags', 'StringTrim'),
			'atuocomplete'  => false,
			'multiOptions'  => $multiOptions,
			'required'      => false,
			'dijitParams'   => array(
				'promptMessage' => 'Choose a client contact.'
			),
			'order'			=> 3
		));
		
		$this->addElement('Button', 'clientFormSubmitButton', array(
				'required'  => false,
				'ignore'    => true,
				'decorators'    => $this->_submitDecorators,
				'label'     => 'Submit',
				'value'     => 'Submit',
				'dijitParams'   => array(
						'onClick' => "return bba.Client.clientFormValidate()"
				),
				'attribs' => array('type' => 'submit')
		));
		
		$this->addElement('Button', 'clientFormCancelButton', array(
				'required'  => false,
				'ignore'    => true,
				'decorators'    => $this->_submitDecorators,
				'label'     => 'Cancel',
				'value'     => 'Cancel',
				'dijitParams'   => array(
						'onClick' => "return bba.closeDialog(dijit.byId('clientForm'))"
				)
		));
		
		$this->addDisplayGroup(
			array(
				'clientFormSubmitButton',
				'clientFormCancelButton',
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
