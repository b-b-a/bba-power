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
 * @subpackage Form_Client
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Save.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Client
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Contract_Save extends BBA_Dojo_Form_Abstract
{
    protected $_simpleTextareaDecorators = array(
        'DijitElement',
        'Errors',
        'Description',
        array(
            array('data' => 'HtmlTag'),
            array(
                'tag' => 'p',
                'class' => 'element'
            )
        ),
        array(
            'Label',
            array('tag' => 'p')
        ),
        array(
            array('row' => 'HtmlTag'),
            array(
                'tag' => 'div',
                'class' => 'form_row simple-textarea'
            )
        )
    );

    public function init()
    {
        $this->setName('contract');
        $this->setAttrib('enctype', 'multipart/form-data');

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $contractId = $request->getParam('contract_idContract', 0);

        if ($request->getParam('contract_idContract') || $request->getParam('contract_idClient')) {

            $row = ($contractId) ?
                $this->getModel()->getDbTable('contract')->getContractById($contractId) :
                $this->getModel()->getDbTable('client')->getClientById($request->getParam('contract_idClient'));
            $clientId = ($contractId) ? $row->contract_idClient : $row->client_idClient;
        }

        if (isset($clientId)) {
            $this->addElement('TextBox', 'client_contract', array(
                'label'     => 'Client:',
                'required'  => false,
                'attribs'   => array('readonly' => true),
                'filters'   => array('StripTags', 'StringTrim'),
                'value'     => ($contractId) ? $row->getClient('client_name') : $row->client_name
            ));
            $this->addHiddenElement('contract_idClient', $clientId);
        } else {
            $this->addElement('FilteringSelect', 'contract_idClient', array(
                'label'         => 'Client:',
                'filters'       => array('StripTags', 'StringTrim'),
                'autoComplete'  => false,
                'hasDownArrow'  => true,
                'storeId'       => 'clientStore',
                //'storeType'     => 'dojo.data.ItemFileReadStore',
                //'storeParams'   => array('url' => "site/data-store/type/clients"),
                'dijitParams'   => array(
                    'searchAttr'    => 'client_name',
                    'promptMessage' => 'Select a Client'
                ),
                'required'      => true,
                'value'         => '0',
                'validators'    => array(
                    array('GreaterThan', true, array(
                        'min'       => '0',
                        'message'   => 'Please select a client.'
                    ))
                ),
                'ErrorMessages' => array('Please select a client.'),
            ));
        }

        $multiOptions = array();

        if ($contractId > 0) {
            $list = $row->getAllTenders();

            $multiOptions = array(0 => ($list->count() > 0) ? 'Select A Tender' : 'No Tenders Available');

            foreach($list as $row) {
                $supplier = $row->getSupplier();
                $multiOptions[$row->tender_idTender]
                    = $supplier->supplier_name . ', '
                    . $row->tender_periodContract . ', '
                    . $row->tender_idTender;
            }

            $this->addElement('FilteringSelect', 'contract_idTenderSelected', array(
                'label'         => 'Tender Selected',
                'filters'       => array('StripTags', 'StringTrim'),
                'autocomplete'  => false,
                'multiOptions'  => $multiOptions,
                'required'      => false,
                'value'         => 0,
                'ErrorMessages' => array('Please select a tender.'),
            ));
        }

        /*$this->addElement('TextBox', 'contract_idSupplierPersonnelSelected', array(
            'label'     => 'Supplier Liason Selected:',
            'required'  => false,
            'value'     => 0,
            'attribs'   => array('disabled' => true),
            'filters'   => array('StripTags', 'StringTrim')
        ));*/

        $multiOptions = array();

        $table = $this->getModel()->getDbTable('tables');
        $list = $table->getSelectListByName('contract_type');
        
        if ($request->getParam('meter_type')) {
        	$type = strtolower($request->getParam('meter_type'));
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

        $this->addElement('RadioButton', 'contract_type', array(
            'label'         => 'Type:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autocomplete'  => false,
            'multiOptions'  => $multiOptions,
            'required'      => true,
            'validators'    => array(
                array('GreaterThan', true, array(
                    'min'       => '0',
                    'message'   => 'Please select a contract type.'
                ))
            ),
            'ErrorMessages' => array('Please select a contract type.')
        ));

        $decors = $this->getElement('contract_type')->getDecorators();

        $decors['Zend_Form_Decorator_Label']->setOptions(array(
            'tag' => 'p',
            'style' => 'line-height: ' . count($multiOptions) * 22 . 'px;'
        ));

        $this->getElement('contract_type')->setDecorators($decors);

        if ($request->getParam('type') == 'add') {
            $multiOptions = array();
            $list = $table->getTableItemByNameKey('contract_status', 'new');
        } else {
            $multiOptions = array(0 => 'Select a status');
            $list = $table->getSelectListByName('contract_status');
        }

        foreach($list as $row) {
            $multiOptions[$row->tables_key] = $row->tables_value;
        }

        $this->addElement('FilteringSelect', 'contract_status', array(
            'label'         => 'Status:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autocomplete'  => false,
            'multiOptions'  => $multiOptions,
            'required'      => true,
            'validators'    => array(
                array('GreaterThan', true, array(
                    'min'       => '0',
                    'message'   => 'Please select a status.'
                ))
            ),
            'ErrorMessages' => array('Please select a status.'),
        ));

        $this->addElement('TextBox', 'contract_dateStart', array(
            'label'         => 'Start Date:',
            'formatLength'  => 'short',
            'filters'       => array('StripTags', 'StringTrim'),
            'validators'    => array(
                array('Date', true, array(
                    'format' => 'dd/MM/yyyy'
                ))
            ),
            'required'      => true
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
            'required'      => false
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
            'required'      => false
        ));

        $this->addElement('ZendSFDojoTextBox', 'contract_reference', array(
            'label'     => 'Contract Ref:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('ZendSFDojoTextBox', 'contract_numberCustomer', array(
            'label'     => 'Customer No:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
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
            'value'         => 0
        ));

        $this->addElement('ZendSFDojoSimpleTextarea', 'contract_desc', array(
            'label'     => 'Description:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim'),
            'decorators'    => $this->_simpleTextareaDecorators
        ));

        /*
        $this->addElement('TextBox', 'contract_idUserAgent', array(
            'label'     => 'User Agent:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));
        */

        $this->addHiddenElement('contract_idContract', '');
        $this->addHiddenElement('meter_idMeter', '');
        $this->addHiddenElement('meter_type', '');
        $this->addHiddenElement('contract_idContractPrevious', '0');
    }

}
