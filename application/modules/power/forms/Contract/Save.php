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
class Power_Form_Contract_Save extends ZendSF_Form_Abstract
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

        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($request->getParam('idContract')) {
            $contractId = $request->getParam('idContract');
            $row = $this->getModel()->getDbTable('contract')->getContractById($contractId);
        }

        if ($request->getParam('type') == 'edit') {

            $this->addElement('TextBox', 'client', array(
                'label'     => 'Client:',
                'required'  => false,
                'attribs'   => array('disabled' => true),
                'filters'   => array('StripTags', 'StringTrim'),
                'value'     => $row->getClient('client_name')
            ));
            $this->addHiddenElement('contract_idClient', '');
        } else {
            $this->addElement('FilteringSelect', 'contract_idClient', array(
                'label'         => 'Client:',
                'filters'       => array('StripTags', 'StringTrim'),
                'autoComplete'  => false,
                'hasDownArrow'  => true,
                'storeId'       => 'clientStore',
                'storeType'     => 'dojo.data.ItemFileReadStore',
                'storeParams'   => array('url' => "/site/data-store/type/clients"),
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

        if (isset($row)) {
            $list = $row->getAllTenders();

            $multiOptions = array(0 => 'Select Tender');

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
                'validators'    => array(
                    array('GreaterThan', true, array(
                        'min'       => '0',
                        'message'   => 'Please select a tender.'
                    ))
                ),
                'ErrorMessages' => array('Please select a tender.'),
            ));
        }

        $this->addElement('TextBox', 'contract_idSupplierContactSelected', array(
            'label'     => 'Supplier Contact Selected:',
            'required'  => false,
            'attribs'   => array('disabled' => true),
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('TextBox', 'contract_reference', array(
            'label'     => 'Contract Ref:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('TextBox', 'contract_numberCustomer', array(
            'label'     => 'Customer No:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $multiOptions = array();

        $table = $this->getModel()->getDbTable('tables');
        $list = $table->getSelectListByName('contract_type');

        foreach($list as $row) {
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
            'ErrorMessages' => array('Please select a contract type.'),
        ));

        $multiOptions = array(0 => 'Select a status');

        $list = $table->getSelectListByName('contract_status');
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

        $this->addElement('TextBox', 'contract_dateEnd', array(
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

        $this->addElement('NumberTextBox', 'contract_periodBill', array(
            'label'     => 'Billing Period:',
            'constraints'   => array(
                'min'       => 0
            ),
            'required'  => false,
            'dijitParams'   => array(
                'promptMessage' => 'Enter contract billing period (Months)',
                'style'         => 'width:50px'
            ),
            'Description'   => '(Months)',
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('SimpleTextarea', 'contract_desc', array(
            'label'     => 'Description:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim'),
            'decorators'    => $this->_simpleTextareaDecorators
        ));
        /*
        $this->addElement('SimpleTextarea', 'contract_txtTenderRequest', array(
            'label'     => 'Temder Request:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('TextBox', 'contract_docAnalysis', array(
            'label'     => 'Analysis Doc:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('TextBox', 'contract_docTermination', array(
            'label'     => 'Termination Doc:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));
        */

        /*$this->addElement('TextBox', 'contract_idUserAgent', array(
            'label'     => 'User Agent:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));*/

        $this->addHiddenElement('contract_idContract', '');
        $this->addHiddenElement('contract_idContractPrevious', '');
    }

}
