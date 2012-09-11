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
class Power_Model_Contract extends ZendSF_Model_Acl_Abstract
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
    	$dataObj = $this->getDbTable('meter')->getAvailableMeters($contract);
    	
    	$meters = array();
    	
    	// get all meters on this contract.
    	$metersContract = $contract->getAllMetersOnContract();
    	
    	// Add meters to list from current contract
    	foreach ($metersContract as $row) {
    		$meter = $row->getMeter();
    		$meters[] = array_merge($row->toArray(), $meter->toArray(), $contract->toArray());
    	}
    	
    	$store = new Zend_Dojo_Data('meter_idMeter', array_merge($meters, $dataObj->toArray()));
    	
    	return $store->toJson();
    }

    /**
     * Gets the available meters data store list, using contract id.
     *
     * @param int $id
     * @return string
     */
    /*public function getAvailableMetersDataStore($id)
    {
        $id = (int) $id;
        $log = Zend_Registry::get('log');

        // get contract
        $contract = $this->getContractById($id);
        $notInStatus = array('current', 'signed', 'selected', 'choosing');
        $notInNewStatus = array('new', 'tender');
        $notIn = array();
        $meters = array();

        $curCoStartDate = new Zend_Date($contract->contract_dateStart);

        $type = preg_replace('/-.+/', '', $contract->contract_type);

        // get all meters on this contract.
        $metersContract = $contract->getAllMetersOnContract();

        //
         // Add meters to list from current contract
         // and add them to a filter so as to exclude them later if needed.
         //
        foreach ($metersContract as $row) {
            $meter = $row->getMeter();
            $meters[] = array_merge($row->toArray(), $meter->toArray(), $contract->toArray());
            $notIn[] = $meter->meter_idMeter;
        }

        //
         // If current contract can have meters added to it
         // get all availible meters and add them to list
         // excluding all current contract meters.
         //
        if (!in_array($contract->contract_status, $notInStatus)) {

            // get client sites
            $sites = $this->getDbTable('site')
                ->fetchAll('site_idClient = ' . $contract->contract_idClient);

            // get meters on sites.
            foreach ($sites as $site) {

                $rowSet = $site->getMetersByType($type);

                // run through each meter attaching contracts if any.
                foreach ($rowSet as $row) {

                    // skip current contract meters.
                    if (in_array($row->meter_idMeter, $notIn)) {
                        continue;
                    }

                    $meter = $row->toArray();

                    // get this meter contract.
                    $meterCo = $row->getCurrentContract();

                    //
                     // If this meter has an contract then run some checks
                     // else just add it to the list.
                     //
                    if ($meterCo) {
                        $meterCoEndDate = new Zend_Date($meterCo->contract_dateEnd);

                        //
                         // If this contract is not earlier than the current contract start date
                         // then skip this meter.
                         //
                        if (!$meterCoEndDate->isEarlier($curCoStartDate)) {
                            continue;
                        }

                        //
                         // If the contract is new|tender and the contract end date
                         // is not earlier than the current contract start date then skip this meter.
                         //
                        if (in_array($meterCo->contract_status, $notInNewStatus) &&
                                !$meterCoEndDate->isEarlier($curCoStartDate)) {
                            continue;
                        }

                        //
                         // If contract end date is earlier then the current contract start date
                         // then add meter to list.
                         //
                        if ($meterCoEndDate->isEarlier($curCoStartDate)) {
                            $mc = $this->getDbTable('meterContract')
                                ->getMeterContractById($row->meter_idMeter, $meterCo->contract_idContract);
                            $meterCo = $meterCo->toArray();
                            $meter = array_merge($meter, $meterCo, $mc->toArray());
                            $meters[] = $meter;
                        }

                    } else {
                        $meters[] = $meter;
                    }
                }
            }
        }

        $store = new Zend_Dojo_Data('meter_idMeter', $meters);

        return ($store->count()) ? $store->toJson() : '{}';
    }*/

    /**
     * Save a contract.
     *
     * @param array $post
     * @return boolean
     * @throws ZendSF_Acl_Exception
     */
    public function saveContract(array $post)
    {
        if (!$this->checkAcl('saveContract')) {
            throw new ZendSF_Acl_Exception('Insufficient rights');
        }

        $form = $this->getForm('contractSave');
        $docForm = $this->getForm('docContract');

        // validate all forms.
        if (!$form->isValid($post) || !$docForm->isValid($post)) {
            return false;
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

        $id = $this->getDbTable('contract')->saveRow($data, $contract);

        // now upload the docs if any.
        if (false === $id) {
            return $id;
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
        
        $log = Zend_Registry::get('log');
        $log->info($id);
        
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
        
        $this->clearCache(array('contract'));

        return $id;
    }

    /**
     * Saves meters to a contract.
     *
     * @param array $post
     * @return mixed false|int
     * @throws ZendSF_Acl_Exception
     */
    public function saveMetersToContract(array $post)
    {
        if (!$this->checkAcl('saveMetersToContract')) {
            throw new ZendSF_Acl_Exception('Insufficient rights');
        }

        $post = Zend_Json::decode($post['jsonData']);

        $data = array();
        $notInStatus = array('current', 'signed', 'selected', 'choosing');
        $contract = $this->getContractById($post['contract']);

        $c = 0;
        foreach($post['meters'] as $value) {
            $data[$c]['meterContract_idMeter'] = $value['id'];
            $data[$c]['meterContract_kvaNominated'] = $value['kva'];
            $data[$c]['meterContract_eac'] = $value['eac'];
            $data[$c]['meterContract_idContract'] = $post['contract'];
            $c++;
        }

        // list current meter on this contract.
        // we will use this list to delete meters no longer on this contract.
        $oldMeterContracts = $this->getMeterContractByContractId($post['contract'])->toArray();
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
        
        $this->clearCache(array('meterContract'));

        return $result;
    }

    /**
     * Saves a contract tender.
     *
     * @param array $post
     * @return boolean
     * @throws ZendSF_Acl_Exception
     */
    public function saveTender(array $post)
    {
        if (!$this->checkAcl('saveTender')) {
            throw new ZendSF_Acl_Exception('Insufficient rights');
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
            ->allow('admin', $this);

        return $this;
    }
}