<?php
/**
 * Meter.php
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
 * Meter model.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Meter extends Power_Model_Acl_Abstract
{
    /**
     * Get meter by their id
     *
     * @param  int $id
     * @return null|Power_Model_DbTable_Row_Meter
     */
    public function getMeterById($id)
    {
        $id = (int) $id;
        return $this->getDbTable('meter')->getMeterById($id);
    }

    /**
     * Get Meter by meter_numberMain
     *
     * @param  string $mpan The meter number to search for
     * @param  Power_Model_DbTable_Row_Meter $ignoreMeter Meter to ignore from the search
     * @return null|Power_Model_DbTable_Row_Meter
     */
    public function getMeterByNumberMain($numberMain, $ignoreMeter=null)
    {
        return $this->getDbTable('meter')->getMeterByNumberMain($numberMain, $ignoreMeter);
    }
    
    public function getMeterByNumberSerial($numberSerial, $ignoreMeter=null)
    {
    	return $this->getDbTable('meter')->getMeterByNumberSerial($numberSerial, $ignoreMeter);
    }

    /**
     * Get meter usage by their id
     *
     * @param  int $id
     * @return null|Power_Model_DbTable_Row_Meter_Usage
     */
    public function getUsageById($id)
    {
        $id = (int) $id;
        return $this->getDbTable('meterUsage')->getUsageById($id);
    }

    /**
     * Gets the meter data store list, using search parameters.
     *
     * @param array $post
     * @return string
     */
    public function getMeterDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $form = $this->getForm('meterSearch');
        $search = array();

        if ($form->isValid($post)) {
            $search = $form->getValues();
        }

        if (isset($post['idClient'])) {
            $search['idClient'] = (int) $post['idClient'];
        }

        $dataObj = $this->getDbTable('meter')->searchMeters($search, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'meter_idMeter');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('meter')->numRows($search)
        );

        return ($store->count()) ? $store->toJson() : '{}';
    }

    /**
     * Gets the meter data store list, using search parameters.
     *
     * @param array $post
     * @return string
     */
    public function getUsageDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $dataObj = $this->getDbTable('meterUsage')->searchUsage($post, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'usage_idUsage');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('meterUsage')->numRows($post)
        );

        return ($store->count()) ? $store->toJson() : '{}';
    }

    /**
     * Gets all contracts on a meter data store.
     *
     * @param array $post
     * @return string
     */
    public function getMeterContractDataStore(array $post)
    {
        $dataObj = $this->getDbTable('meterContract')->getAllContractsByMeterId($post);

        $store = $this->_getDojoData($dataObj, 'contract_idContract');

        $store->setMetadata(
            'numRows',
            $dataObj->count()
        );

        return ($store->count()) ? $store->toJson() : '{}';
    }
    
    /**
     * Adds a meter
     * 
     * @param array $post
     * @throws Power_Model_Acl_Exception
     * @return boolean|Ambigous <false, number>
     */
    public function addMeter(array $post)
    {
    	if (!$this->checkAcl('addMeter')) {
    		throw new Power_Model_Acl_Exception('Insufficient rights');
    	}
    	
    	$form = $this->getForm('meterAdd');
    	
    	if (!$form->isValid($post)) {
    		return false;
    	}
    	
    	return $this->_save($form->getValues());
    	
    }
    
    /**
     * Edits a meter
     * 
     * @param array $post
     * @throws Power_Model_Acl_Exception
     * @return boolean|Ambigous <false, number>
     */
    public function editMeter(array $post)
    {
    	if (!$this->checkAcl('editMeter')) {
    		throw new ZendSF_Acl_Exception('Insufficient rights');
    	}
    	
    	$form = $this->getForm('meterEdit');
    	 
    	if (!$form->isValid($post)) {
    		return false;
    	}
    	 
    	return $this->_save($form->getValues());
    }

    /**
     * Updates a meter.
     *
     * @param array $post
     * @return false|int
     */
    private function _save(array $data)
    {
        $meter = array_key_exists('meter_idMeter', $data) ?
            $this->getMeterById($data['meter_idMeter']) : null;
            
        $this->clearCache(array('meter'));

        return $this->getDbTable('meter')->saveRow($data, $meter);
    }

    /**
     * Updates meter usage.
     *
     * @param array $post
     * @return false|int
     */
    public function saveUsage($post)
    {
        if (!$this->checkAcl('saveUsage')) {
            throw new Power_Model_Acl_Exception('Insufficient rights');
        }

        $form = $this->getForm('meterUsageSave');

        if (!$form->isValid($post)) {
            return false;
        }

        // get filtered values
        $data = $form->getValues();

//        // check usage values, if all zero then return false with error message.
//        $totalUsage = $data['usage_usageDay'] + $data['usage_usageNight'] + $data['usage_usageOther'];
//
//        if ($totalUsage == 0) {
//            $form->addErrorMessage('Total Consumption for meters cannot be zero.');
//            return false;
//        }
//  EP - Apparently they need to be able to have zeros in all values

        $dateKeys = array(
            'usage_dateBill',
            'usage_dateReading',
        );

        foreach ($data as $key => $value) {
            if (in_array($key, $dateKeys)) {
                if ($value === '') $value = '01-01-1970';
                $date = new Zend_Date($value, Zend_Date::DATE_SHORT);
                $data[$key] = $date->toString('yyyy-MM-dd');
            }
        }

        $meter = array_key_exists('usage_idUsage', $data) ?
            $this->getUsageById($data['usage_idUsage']) : null;
        
        $this->clearCache(array('usage'));

        return $this->getDbTable('meterUsage')->saveRow($data, $meter);
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
        $this->_acl->allow('meterUsage', $this, array('saveUsage'))
        	->allow('client', $this, array('editMeter', 'saveUsage'))
            ->allow('user', $this)
            ->allow('admin', $this);

        return $this;
    }
}
