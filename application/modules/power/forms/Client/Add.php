<?php
/**
 * Add.php
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
 * Form Class Add.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Client
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Client_Add extends Power_Form_Client_Base
{
	protected $_defaultDecorators = array(
		'Description',
		'FormElements',
		array(
			'HtmlTag',
			array(
				'tag'   => 'div',
				'class' => 'form_wrap wizard'
			)
		)
	);
	
    public function init()
    {
    	parent::init();
    	
    	$this->addHiddenElement('type', 'add');
    	
        $clientAdForm = new Power_Form_Client_Address_Save(array('model' => $this->_model));
        $clientPersForm = new Power_Form_Client_Personnel_Save(array('model' => $this->_model));

        /**
         * Add Client Address form Elements
         */
        $this->addElement($clientAdForm->getElement('clientAd_addressName'));
        $this->addElement($clientAdForm->getElement('clientAd_address1'));
        $this->addElement($clientAdForm->getElement('clientAd_address2'));
        $this->addElement($clientAdForm->getElement('clientAd_address3'));
        $this->addElement($clientAdForm->getElement('clientAd_postcode'));

        /**
         * Add Client Contact form Elements
         */
        $this->addElement($clientPersForm->getElement('clientPers_type')->setValue('liaison'));
        $this->addElement($clientPersForm->getElement('clientPers_name'));
        $this->addElement($clientPersForm->getElement('clientPers_position'));
        $this->addElement($clientPersForm->getElement('clientPers_phone'));
        $this->addElement($clientPersForm->getElement('clientPers_email'));
        
    	/**
    	 * Add groups.
    	 */
    	
    	$this->addDisplayGroup(
    		array(
    			'client_name',
    			'client_docLoa',
    			'client_dateExpiryLoa',
    			'client_desc',
    			'type'
    		),
    		'clientGroup',
    		array('decorators' => array(
    			'FormElements',
		    	array(
		    		array('fieldset' => 'HtmlTag'),
		    		array(
		    			'tag' => 'table',
		    			'class' => 'zend_form'
		    		)
		    	),
		    	array(
		    		'HtmlTag',
		    		array(
		    			'tag' => 'div',
		    			'data-dojo-type' => 'dojox.widget.WizardPane',
		    			'data-dojo-props' => 'onShow: bba.Client.wizardClientPane',
		    			'id'	 => 'clientPane',
		    			'class' => 'client_edit'
		    		)
		    	)
    		)
    	));
    	
    	$this->addDisplayGroup(
    		array(
    			'clientAd_addressName',
    			'clientAd_address1',
    			'clientAd_address2',
    			'clientAd_address3',
    			'clientAd_postcode'
    		),
    		'clientAdGroup',
    		array('decorators' => array(
    			'FormElements',
		    	array(
		    		array('fieldset' => 'HtmlTag'),
		    		array(
		    			'tag' => 'table',
		    			'class' => 'zend_form'
		    		)
		    	),
		    	array(
		    		'HtmlTag',
		    		array(
		    			'tag' => 'div',
		    			'data-dojo-type' => 'dojox.widget.WizardPane',
		    			'data-dojo-props' => 'onShow: bba.Client.wizardClientAdPane',
		    			'id'	 => 'clientAdPane',
		    			'class' => 'client_edit'
		    		)
		    	)
    		))
    	);
    	
    	$this->addDisplayGroup(
    		array(
    			'clientPers_type',
    			'clientPers_name',
    			'clientPers_position',
    			'clientPers_phone',
    			'clientPers_email'
    		),
    		'clientPersGroup',
    		array('decorators' => array(
    			'FormElements',
		    	array(
		    		array('fieldset' => 'HtmlTag'),
		    		array(
		    			'tag' => 'table',
		    			'class' => 'zend_form'
		    		)
		    	),
		    	array(
		    		'HtmlTag',
		    		array(
		    			'tag' => 'div',
		    			'data-dojo-type' => 'dojox.widget.WizardPane',
		    			'data-dojo-props' => 'onShow: bba.Client.wizardClientPersPane, doneFunction: bba.Client.wizardDoneFunction',
		    			'id'	 => 'clientPersPane',
		    			'class' => 'client_edit'
		    		)
		    	)
    		))
    	);
    	
    }
}
