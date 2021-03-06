<?php
/**
 * AddressSave.php
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
 * Form Class AddressSave.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Client
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Client_Address_Save extends Power_Form_Dojo_Abstract
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
    	$this->addElementPrefixPath(
    		'Power_Filter',
    		APPLICATION_PATH . '/modules/power/models/Filter/',
    		'filter'
    	);
    	
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->setName('client-address');

        $this->addHiddenElement('clientAd_idAddress', '');
        $this->addHiddenElement('clientAd_idClient', '');
        
        if($request->getParam('site_idSite')) {
        	$this->addHiddenElement('site_idSite', '');
        }

        $this->addElement('TextBox', 'clientAd_addressName', array(
            'label'         => 'Address Name:',
            'filters'       => array('StripTags', 'StringTrim'),
            'dijitParams'   => array(
                'promptMessage' => 'Enter the clients address name.'
            ),
            'value'         => ($request->getParam('type') == 'add' && $request->getParam('clientAd_idClient')) ?
                $this->getModel()
                    ->getDbTable('client')
                    ->getClientById(
                    		$request->getParam('clientAd_idClient')
                    )
                    ->client_name : ''
        ));

        $this->addElement('ValidationTextBox', 'clientAd_address1', array(
            'label'     => 'Address 1:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim'),
            'validators'    => array('NotEmpty'),
            'dijitParams'   => array(
                'promptMessage' => 'Enter the first line of clients address.'
            )
        ));

        $this->addElement('ValidationTextBox', 'clientAd_address2', array(
            'label'     => 'Address 2:',
            'filters'   => array('StripTags', 'StringTrim'),
            'dijitParams'   => array(
                'promptMessage' => 'Enter the second line of clients address.'
            )
        ));

        $this->addElement('ValidationTextBox', 'clientAd_address3', array(
            'label'     => 'Town/City:',
            'filters'   => array('StripTags', 'StringTrim'),
            'dijitParams'   => array(
                'promptMessage' => 'Enter the third line of clients address.'
            )
        ));

        $this->addElement('ValidationTextBox', 'clientAd_postcode', array(
            'label'         => 'Postcode:',
            'required'      => true,
            'filters'       => array('StripTags', 'StringTrim', 'Postcode'),
            'validators'    => array(
                array('PostCode', true, array(
                    'locale' => 'en_GB'
                ))
            ),
            'dijitParams'   => array(
                'promptMessage' => 'Enter the clients postcode.'
            )
        ));
        
        $this->addElement('Button', 'clientAdFormSubmitButton', array(
        		'required'  => false,
        		'ignore'    => true,
        		'decorators'    => $this->_submitDecorators,
        		'label'     => 'Submit',
        		'value'     => 'Submit',
        		'dijitParams'   => array(
        			'onClick' => "return bba.Client.validateClientAdForm();"
        		),
        		'attribs' => array('type' => 'submit')
        ));
        
        $this->addElement('Button', 'clientAdFormCancelButton', array(
        		'required'  => false,
        		'ignore'    => true,
        		'decorators'    => $this->_submitDecorators,
        		'label'     => 'Cancel',
        		'value'     => 'Cancel',
        		'dijitParams'   => array(
        			'onClick' => "return bba.closeDialog(dijit.byId('clientAdForm'));"
        		)
        ));
        
        $this->addDisplayGroup(
        	array(
        		'clientAdFormSubmitButton',
        		'clientAdFormCancelButton',
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
