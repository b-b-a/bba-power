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
    public function init()
    {
        $this->setName('contract');

        $view = $this->getView();
        if (isset($view->request['contractId'])) {
            $siteId = $view->request['contractId'];
            $row = $this->_model->find($siteId);
        }

        $this->addElement('FilteringSelect', 'contract_idClient', array(
            'label'         => 'Client:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autoComplete'  => false,
            'hasDownArrow'  => true,
            'storeId'       => 'clientStore',
            'storeType'     => 'dojo.data.ItemFileReadStore',
            'storeParams'   => array('url' => "/site/autocomplete/param/client"),
            'dijitParams'   => array('searchAttr' => 'client_name'),
            //'attribs'       => array('readonly' => true),
            'required'      => true
        ));

        $this->addElement('TextBox', 'contract_idTenderSelected', array(
            'label'     => 'Tender Selected:',
            'required'  => true,
            'attribs'       => array('disabled' => true),
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('TextBox', 'contract_idSupplierContactSelected', array(
            'label'     => 'Supplier Contact Selected:',
            'required'  => true,
            'attribs'       => array('disabled' => true),
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

        $table = new Power_Model_Mapper_Tables();
        $list = $table->getSelectListByName('contract_type');
        foreach($list as $row) {
            $multiOptions[$row->key] = $row->value;
        }

        $this->addElement('FilteringSelect', 'contract_type', array(
            'label'         => 'Type:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autocomplete'  => false,
            'multiOptions'  => $multiOptions,
            'required'      => true,
        ));

        $multiOptions = array();

        $list = $table->getSelectListByName('contract_status');
        foreach($list as $row) {
            $multiOptions[$row->key] = $row->value;
        }

        $this->addElement('FilteringSelect', 'contract_status', array(
            'label'         => 'Status:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autocomplete'  => false,
            'multiOptions'  => $multiOptions,
            'required'      => true,
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
            )
        ));

        $this->addElement('NumberSpinner', 'contract_periodBill', array(
            'label'     => 'Billing Period:',
            'min'       => 0,
            'required'  => false,
            //'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('SimpleTextarea', 'contract_desc', array(
            'label'     => 'Description:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));

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

        $this->addElement('TextBox', 'contract_idUserAgent', array(
            'label'     => 'User Agent:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $auth = Zend_Auth::getInstance()
            ->getIdentity();

        $this->addHiddenElement('userId', $auth->getId());
        $this->addHiddenElement('contract_idContract', '');
        $this->addHiddenElement('contract_idContractPrevious', '');

        if ($auth->role == 'admin') {
            $this->addSubmit('Save');
        }

        $this->addSubmit('Cancel', 'cancel');
    }

}
