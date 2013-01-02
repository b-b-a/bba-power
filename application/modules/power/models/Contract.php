<?php
/**
 * Contract.php
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
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Contract Model.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Contract extends Power_Model_Acl_Abstract
{
    /**
     * Get contract by their id
     *
     * @param  int $id
     * @return null|Power_Model_DbTable_Row_Contract
     */
    public function getContractById($id)
    {
        $id = (int) $id;
        return $this->getDbTable('contract')->getContractById($id);
    }

    /**
     * Get a meter contract by it's compound id.
     *
     * @param int $meterId
     * @param int $contractId
     * @return null|Power_Model_DbTable_Row_Meter_Contract
     */
    public function getMeterContractById($meterId, $contractId)
    {
        $meterId = (int) $meterId;
        $contractId = (int) $contractId;
        return $this->getDbTable('meterContract')->getMeterContractById($meterId, $contractId);
    }

    /**
     * Get a meter contract by the contract id.
     *
     * @param int $id
     * @return null|Power_Model_DbTable_Row_Meter_Contract
     */
    public function getMeterContractByContractId($id)
    {
        $id = (int) $id;
        return $this->getDbTable('meterContract')->getMeterContractByContractId($id);
    }

    /**
     * Gets a tender by it's id.
     *
     * @param int $id
     * @return null|Power_Model_DbTable_Row_Tender
     */
    public function getTenderById($id)
    {
        $id = (int) $id;
        return $this->getDbTable('tender')->getTenderById($id);
    }

    /**
     * Gets the contract data store list, using search parameters.
     *
     * @param array $post
     * @return string
     */
    public function getContractDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $form = $this->getForm('contractSearch');
        $search = array();

        if ($form->isValid($post)) {
            $search = $form->getValues();
        }

        if (isset($post['contract_idClient'])) {
            $search['contract_idClient'] = (int) $post['contract_idClient'];
        }

        if (isset($post['idSite'])) {
            $search['idSite'] = (int) $post['idSite'];
        }

        $dataObj = $this->getDbTable('contract')->searchContracts($search, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'contract_idContract');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('contract')->numRows($search)
        );

        return ($store->count()) ? $store->toJson() : '{}';
    }

    /**
     * Gets the meter contract data store list, using search parameters.
     *
     * @param array $post
     * @return string
     */
    public function getMeterContractDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $dataObj = $this->getDbTable('meterContract')->searchMeterContracts($post, $sort, $count, $start);
        
        $store = $this->_getDojoData($dataObj, 'meter_idMeter');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('meterContract')->numRows($post)
        );

        return ($store->count()) ? $store->toJson() : '{}';
    }

    /**
     * Gets the tender data store list, using search parameters.
     *
     * @param array $post
     * @return string
     */
    public function getTenderDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $dataObj = $this->getDbTable('tender')->searchTenders($post, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'tender_idTender');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('tender')->numRows($post)
        );

        return ($store->count()) ? $store->toJson() : '{}';
    }
    
    /**
     * Gets the available meters data store list, using contract id.
     *
     * @param int $id
     * @return string
     */
    public function getAvailableMetersDataStore($id)
    {	
    	$contract = $this->getContractById($id);
    	$dataObj = $this->getDbTable('meterContract')->getAvailableMeters($contract);
    	
    	$store = new Zend_Dojo_Data('meter_idMeter', $dataObj->toArray());
    	
    	return $store->toJson();
    }
    
    /**
     * Checks for duplicate contracts.
     * 
     * @param array $post
     * @return NULL|Zend_Db_Table_Rowset_Abstract
     */
    public function checkDuplicateContracts(array $post)
    {
    	$ref = (string) $post['contract_reference'];
    	$type = (string) $post['contract_type'];
    	$clientId = (int) $post['contract_idClient'];
    	$ignoreContract = ($post['contract_idContract']) ? (int) $post['contract_idContract'] : null;
    	
    	if ($post['contract_dateStart'] === '') $post['contract_dateStart'] = '01-01-1970';
    	
    	try {
    		$date = new Zend_Date($post['contract_dateStart'], Zend_Date::DATE_SHORT);
    		$date = $date->toString('yyyy-MM-dd');
    	} catch (Exception $e) {
    		return null;
    	}
    	
    	
    	$contracts = $this->getDbTable('contract')->getDuplicateContracts($ref, $type, $clientId, $date, $ignoreContract);
    	
    	return ($contracts->count() > 0) ? $contracts : null;
    }
    
    public function addContract(array $post)
    {
    	if (!$this->checkAcl('addContract')) {
    		throw new Power_Model_Acl_Exception('Insufficient rights');
    	}
    	
    	$form = $this->getForm('contractAdd');
    	
    	$form->removeElement('contract_docTermination');
    	
    	return $this->_saveContract($post, $form);
    }
    
    public function editContract(array $post)
    {
    	if (!$this->checkAcl('editContract')) {
    		throw new Power_Model_Acl_Exception('Insufficient rights');
    	}
    	 
    	$form = $this->getForm('contractEdit');
    	
    	$form->removeElement('contract_docAnalysis');
    	$form->removeElement('contract_docContractSearchable');
    	$form->removeElement('contract_docContractSignedClient');
    	$form->removeElement('contract_docContractSignedBoth');
    	$form->removeElement('contract_docTermination');
    	 
    	return $this->_saveContract($post, $form);
    }

    /**
     * Save a contract.
     *
     * @param array $post
     * @return boolean
     * @throws Power_Model_Acl_Exception
     */
    protected  function _saveContract(array $post, Power_Form_Contract_Base $form)
    {
        $docForm = $this->getForm('docContract');

        // validate all forms.
        if (!$form->isValid($post) || !$docForm->isValid($post)) {
        	
        	if ($post['type'] == 'edit') {
        		$form->addElement($docForm->getElement('contract_docAnalysis'));
    			$form->addElement($docForm->getElement('contract_docContractSearchable'));
    			$form->addElement($docForm->getElement('contract_docContractSignedClient'));
    			$form->addElement($docForm->getElement('contract_docContractSignedBoth'));
    			
    			$form->getElement('contract_docAnalysis')->setOrder(91);
    			$form->getElement('contract_docContractSearchable')->setOrder(92);
    			$form->getElement('contract_docContractSignedClient')->setOrder(93);
    			$form->getElement('contract_docContractSignedBoth')->setOrder(94);
        	}
        	
    		$form->addElement($docForm->getElement('contract_docTermination'));
    		$form->getElement('contract_docTermination')->setOrder(95);
    		
            return array('id' => false);
        }
        
        // get filtered values from main form,
        // we will want to save the docForm till later.
        $data = $form->getValues();

        $dateKeys = array(
            'contract_dateDecision',
            'contract_dateStart',
            'contract_dateEnd'
        );

        // run through all values formatting them to database schema.
        foreach ($data as $key => $value) {
            if (in_array($key, $dateKeys)) {
                if ($value === '') $value = '01-01-1970';
                $date = new Zend_Date($value, Zend_Date::DATE_SHORT);
                $data[$key] = $date->toString('yyyy-MM-dd');
            }
        }

        $contract = array_key_exists('contract_idContract', $data) ?
            $this->getContractById($data['contract_idContract']) : null;
            
        //warning to user that system has changed the Contract Satus or end date
        $warning = false;
        //$log = Zend_Registry::get('log');
        //$log->info("saveContract:Cont_End: ".$contract->contract_dateEnd);
        //$log->info("saveContract:End(input): ".$data['contract_dateEnd']);

        //If tender selected being set to a value
        if ($contract && $data['contract_idTenderSelected'] > 0 && 
        		$contract->contract_idTenderSelected != $data['contract_idTenderSelected']) {
            //get new tender details
            $tender = $this->getTenderById($data['contract_idTenderSelected']);
            //set contract status to selected if new, choose or tender
            if (in_array($data['contract_status'], array('tender', 'choose', 'new'))) {
            	$data['contract_status'] = 'selected';
                //set warning to user that system changed status
                $warning = true;
            }
            //also, if date _not_ set by user set end date using start date and tender period
            if ($contract->contract_dateEnd == $data['contract_dateEnd']) {
                $date = new Zend_Date($data['contract_dateStart']);
                $date->add(round($tender->tender_periodContract, 0), ZEND_DATE::MONTH);
                $date->sub('1', ZEND_DATE::DAY);
                $data['contract_dateEnd'] = $date->toString('yyyy-MM-dd');
                //set warning to user that system set the end date
                $warning = true;
            }
        }
        
        // put back disabled values from edit form.
        if (!$this->checkAcl('currrentContractFormEdit') 
        		&& $post['type'] == 'edit' && $contract->contract_status == 'current') {
			$data['contract_status'] = $contract->contract_status;
        	$data['contract_dateStart'] = $contract->contract_dateStart;
        	$data['contract_dateEnd'] = $contract->contract_dateEnd;
        	$data['contract_reference'] = $contract->contract_reference;
        }

        $id = $this->getDbTable('contract')->saveRow($data, $contract);

        // now upload the docs if any.
        if (false === $id) {
            return array('id' => $id);
        }

        // add filters to the docForm.
        if ('add' === $post['type']) {
            Power_Model_Doc::createUploadFilter(
                $docForm->getElement('contract_docTermination'),
                $id
            );
        } else {
            Power_Model_Doc::addUploadFilter(
                Power_Model_Doc::$docContract,
                $docForm,
                $id
            );
        }

        // get filtered values, this also uploads the files.
        $data = $docForm->getValues();
        
        // add meter to contract if set.
        if ($post['meter_idMeter']) {
        	$meterId = (int) $post['meter_idMeter'];
        	$row = $this->getDbTable('meter')
        		->getMeterById($meterId)
        		->getCurrentContract();
        	
        	$meterContract = $this->saveMetersToContract(array(
				'jsonData' => Zend_Json::encode(array(
					'contract' => $id,
					'meters' => array(
						array(
							'id' => $meterId,
							'kva' => ($row->meterContract_kvaNominated) ? $row->meterContract_kvaNominated : 0,
							'eac' => ($row->meterContract_eac) ? $row->meterContract_eac : 0
						)
					)
				)
        	)));
        }
        
        $this->clearCache(array('contract', 'meterContract'));

        return array(
        	'id' => $id,
        	'warning' => $warning
        );
    }

    /**
     * Saves meters to a contract.
     *
     * @param array $post
     * @return mixed false|int
     * @throws Power_Model_Acl_Exception
     */
    public function saveMetersToContract(array $post)
    {
        if (!$this->checkAcl('saveMetersToContract')) {
            throw new Power_Model_Acl_Exception('Insufficient rights');
        }

        $post = Zend_Json::decode($post['jsonData']);

        $data = array();
        $notInStatus = array('current', 'signed', 'selected', 'choosing');
        $contract = $this->getContractById($post['contract']);

        $c = 0;
        
        //$log = Zend_Registry::get('log');
        //$log->info("saveMetersToContract:Meter count: ".count($post['meters']));
        
        foreach($post['meters'] as $value) {
            $data[$c]['meterContract_idMeter'] = $value['id'];
            $data[$c]['meterContract_kvaNominated'] = $value['kva'];
            $data[$c]['meterContract_eac'] = $value['eac'];
            $data[$c]['meterContract_idContract'] = $post['contract'];
            $c++;
        }

        // list current meter on this contract.
        // we will use this list to delete meters no longer on this contract.
        $oldMeterContracts = $this->getMeterContractByContractId($post['contract'])->toArray(true);
        $result = true;

        // update or insert rows
        foreach ($data as $row) {
            $meterContract = array_key_exists('meterContract_idContract', $row) ?
                $this->getMeterContractById($row['meterContract_idMeter'], $row['meterContract_idContract']) : null;
            $result = (is_array($this->getDbTable('meterContract')->saveRow($row, $meterContract))) ? true : false;

            // check to see if of the current meter list.
            foreach($oldMeterContracts as $key => $value) {
                if ($value['meterContract_idMeter'] == $row['meterContract_idMeter']) {
                    unset($oldMeterContracts[$key]);
                }
            }
        }
		
        // delete any rows that were deselected
        if ($oldMeterContracts && !in_array($contract->contract_status, $notInStatus)) {
            foreach ($oldMeterContracts as $row) {
                $result = $this->getDbTable('meterContract')
                    ->deleteRow($row['meterContract_idMeter'], $row['meterContract_idContract']);
            }
        }
        
        // update contract status to tender if contract is in tender process.
        if (in_array($contract->contract_status, array('tender', 'choose', 'selected', 'signed'))) {
	        $contractUpdate = $this->getDbTable('contract')->saveRow(
	        	array('contract_status' => 'tender'),
	        	$contract
	        );
    	}
        
        $this->clearCache(array('meterContract', 'contract'));

        return $result;
    }

    /**
     * Saves a contract tender.
     *
     * @param array $post
     * @return boolean
     * @throws Power_Model_Acl_Exception
     */
    public function saveTender(array $post)
    {
        if (!$this->checkAcl('saveTender')) {
            throw new Power_Model_Acl_Exception('Insufficient rights');
        }

        $form = $this->getForm('tenderSave');

        if (!$form->isValid($post)) {
            return false;
        }

        // get filtered values
        $data = $form->getValues();

        $dateKeys = array(
            'tender_dateExpiresQuote'
        );

        foreach ($data as $key => $value) {
            if (in_array($key, $dateKeys)) {
                if ($value === '') $value = '01-01-1970';
                $date = new Zend_Date($value, Zend_Date::DATE_SHORT);
                $data[$key] = $date->toString('yyyy-MM-dd');
            }
        }

        $tender = array_key_exists('tender_idTender', $data) ?
            $this->getTenderById($data['tender_idTender']) : null;
        
        $this->clearCache(array('tender'));

        return $this->getDbTable('tender')->saveRow($data, $tender);
    }

    /**
     * Injector for the acl, the acl can be injected directly
     * via this method.
     *
     * We add all the access rules for this resource here, so we first call
     * parent method to add $this as the resource then we
     * define it rules here.
     *
     * @param Zend_Acl_Resource_Interface $acl
     * @return ZendSF_Model_Abstract
     */
    public function setAcl(Zend_Acl $acl) {
        parent::setAcl($acl);

        // implement rules here.
        $this->_acl->allow('user', $this)
            ->allow('admin', $this)
        	->deny('user', $this, array('currrentContractFormEdit'));

        return $this;
    }
}