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
 * @subpackage Form_Contract
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Edit.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Contract
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Contract_Edit extends Power_Form_Contract_Base 
{
	public function init()
	{
		parent::init();
		
		$this->getElement('contract_idClient')->setAttrib('readonly', true);
		$this->getElement('type')->setAttrib('value', 'edit');
		
		$this->addElement('FilteringSelect', 'contract_idTenderSelected', array(
				'label'         => 'Tender Selected:',
				'filters'       => array('StripTags', 'StringTrim'),
				'autocomplete'  => false,
				'multiOptions'  => $this->_getTenderOptions(),
				'required'      => false,
				'value'         => 0,
				'ErrorMessages' => array('Please select a tender.'),
				'order'			=> 15
		));
		
		// add validator to tenderselect if status equals selected.
		if ($this->_request->getParam('contract_status') == 'selected') {
		    $this->getElement('contract_idTenderSelected')
		        ->setRequired(true)
		        ->addValidator('GreaterThan', true, array(
                    'min'       => '0',
                    'message'   => 'Please select a tender.'
                ));
		}
		
		$this->addElement($this->_contractDoc->getElement('contract_docAnalysis'));
		$this->addElement($this->_contractDoc->getElement('contract_docContractSearchable'));
		$this->addElement($this->_contractDoc->getElement('contract_docContractSignedClient'));
		$this->addElement($this->_contractDoc->getElement('contract_docContractSignedBoth'));
		$this->addElement($this->_contractDoc->getElement('contract_docTermination'));
		
		$this->getElement('contract_docAnalysis')->setOrder(91);
		$this->getElement('contract_docContractSearchable')->setOrder(92);
		$this->getElement('contract_docContractSignedClient')->setOrder(93);
		$this->getElement('contract_docContractSignedBoth')->setOrder(94);
		$this->getElement('contract_docTermination')->setOrder(95);
	}
	
	protected function _getContractStatus()
    {
        $multiOptions = parent::_getContractStatus();
        
        $row = $this->getModel()->getContractById($this->_request->getParam('contract_idContract'));
        $meters = $row->getAllMetersOnContract();
        $tenders = $row->getAllTenders();
        
        $log = Zend_Registry::get('log');
        $log->info($tenders->count());
        
        if ($meters->count() == 0 || $tenders->count() == 0) {
            $multiOptions = array(
                'new' => $multiOptions['new']
            );
        }
    	
    	return $multiOptions;
    }
	
	protected function _getTenderOptions()
	{
		$row = $this->getModel()->getDbTable('contract')->getContractById($this->_request->getParam('contract_idContract'));
		
		$list = $row->getAllTenders();
		
		$multiOptions = array(0 => ($list->count() > 0) ? 'Select A Tender' : 'No Tenders Available');
		
		foreach($list as $row) {
			$supplier = $row->getSupplier();
			$multiOptions[$row->tender_idTender] = $supplier->supplier_name . ', '
					. $row->tender_periodContract . ', '
					. $row->tender_idTender;
		}
		
		return $multiOptions;
	}
}