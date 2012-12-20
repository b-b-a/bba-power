<?php
/**
 * Client.php
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
 * Client model.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Client extends Power_Model_Acl_Abstract
{
    /**
     * Get Client by their id
     *
     * @param  int $id
     * @return null|Power_Model_DbTable_Row_Client
     */
    public function getClientById($id)
    {
        $id = (int) $id;
        return $this->getDbTable('client')->getClientById($id);
    }

    /**
     * Get Client Address by their id
     *
     * @param  int $id
     * @return null|Power_Model_DbTable_Row_Client_Address
     */
    public function getClientAddressById($id)
    {
        $id = (int) $id;
        return $this->getDbTable('clientAddress')->getClientAddressById($id);
    }

    /**
     * Get all addresses belonging to a client.
     *
     * @param int $id
     * @return null|array
     */
    public function getClientAddressesByClientId($id)
    {
        $id = (int) $id;
        return $this->getDbTable('clientAddress')->getClientAddressesByClientId($id);
    }

    /**
     * Get Client Contact by their id
     *
     * @param  int $id
     * @return null|Power_Model_DbTable_Row_Client_Personnel
     */
    public function getClientPersonnelById($id)
    {
        $id = (int) $id;
        return $this->getDbTable('clientPersonnel')->getClientPersonnelById($id);
    }

    /**
     * Get all contacts that belong to a client.
     *
     * @param int $id
     * @return null|array
     */
    public function getClientPersonnelByClientId($id)
    {
        $id = (int) $id;
        return $this->getDbTable('clientPersonnel')->getClientPersonnelByClientId($id);
    }

    /**
     * Gets the client data store list, using search parameters.
     *
     * @param array $post
     * @return string JSON string
     */
    public function getClientDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $form = $this->getForm('clientSearch');
        $search = array();

        if ($form->isValid($post)) {
            $search = $form->getValues();
        }

        $dataObj = $this->getDbTable('client')->searchClients($search, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'client_idClient');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('client')->numRows($search)
        );

        return ($store->count()) ? $store->toJson() : '{}';
    }

    /**
     * Gets the address data store list, using search parameters.
     *
     * @param array $post
     * @return string JSON string
     */
    public function getClientAddressDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $dataObj = $this->getDbTable('clientAddress')->searchAddress($post, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'clientAd_idAddress');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('clientAddress')->numRows($post)
        );

        return ($store->count()) ? $store->toJson() : '{}';
    }

    /**
     * Gets the contact data store list, using search parameters.
     *
     * @param array $post
     * @return string JSON string
     */
    public function getClientPersonnelDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $dataObj = $this->getDbTable('clientPersonnel')->searchContact($post, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'clientPers_idClientPersonnel');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('clientPersonnel')->numRows($post)
        );

        return ($store->count()) ? $store->toJson() : '{}';
    }
    
    /**
     * Checks for duplicate client addresses.
     * 
     * @param array $post
     * @return NULL|Zend_Db_Table_Rowset_Abstract
     */
    public function checkDuplicateAddresses(array $post)
    {
    	$form = $this->getForm('clientAddressSave');
    	$form->populate($post);
    	$data = $form->getValues();
    	
    	$addresses = $this->getDbTable('clientAddress')
    	    ->getDuplicateAddresses($data);
    	 
    	return ($addresses->count() > 0) ? $addresses : null;
    }
    
    /**
     * Checks for duplicate client email addesses.
     *
     * @param array $post
     * @return NULL|Zend_Db_Table_Rowset_Abstract
     */
    public function checkDuplicateEmails(array $post)
    {
    	$form = $this->getForm('clientPersonnelSave');
    	$form->populate($post);
    	$data = $form->getValues();
    	 
    	$emails = $this->getDbTable('clientPersonnel')
    	->getDuplicateEmails($data);
    
    	return ($emails->count() > 0 && $data['clientPers_email'] != '') ? $emails : null;
    }

    /**
     * Add new Client.
     *
     * @param array $post
     * @return false|int
     */
    public function saveNewClient($post)
    {
        if (!$this->checkAcl('saveNewClient')) {
            throw new Power_Model_Acl_Exception('Insufficient rights');
        }

        // validate form.
        $form = $this->getForm('clientAdd');
        
        /* @var $form Power_Form_Doc_Client */
        $docForm = $this->getForm('docClient');

        if ($post['client_dateExpiryLoa'] === '') {
            $client_dateExpiryLoaValidateRules = $form->getElement('client_dateExpiryLoa')
                ->getValidator('Date');

            $form->getElement('client_dateExpiryLoa')
                ->removeValidator('Date');
        }

        if (!$form->isValid($post) || !$docForm->isValid($post)) {
            if (isset($client_dateExpiryLoaValidateRules)) {
                $form->getElement('client_dateExpiryLoa')
                    ->addValidator($client_dateExpiryLoaValidateRules);
            }
            return false;
        }

        // get filtered values.
        $form->removeElement('client_docLoa');
        $post = $form->getValues();

        $this->getDbTable('client')->getAdapter()->beginTransaction();

        try {
            /**
             * TODO: must add checking on each stage.
             */
            // save client first.
            $clientSave = $this->_saveClient($post);

            // then save client address.
            $post['clientAd_idClient'] = $clientSave;

            if ($post['clientAd_addressName'] == '') {
                $post['clientAd_addressName'] = $post['client_name'];
            }

            $clientAdSave = $this->_saveClientAddress($post);

            // now save client contact
            $post['clientPers_idClient'] = $clientSave;
            $post['clientPers_idAddress'] = $clientAdSave;
            $clientPersSave = $this->_saveClientPersonnel($post);

            // now update client with address and contact ids.
            // we will have to update docLoa here too.
            // now we have to rename the docLoa.
            // add filters to the docForm.
            // add filters to the docForm.
            Power_Model_Doc::addUploadFilter(Power_Model_Doc::$docClient, $docForm, $clientSave);

            // upload file.
            $upload = $docForm->getValues();

            $post['client_idClient'] = $clientSave;
            $post['client_idAddress'] = $clientAdSave;
            $post['client_idClientPersonnel'] = $clientPersSave;
            $post['client_idRegAddress'] = $clientAdSave;
            $clientSave = $this->_saveClient($post);

            $newSite = array(
                'site_idClient'         => $clientSave,
                'site_idAddress'        => $clientAdSave,
                'site_idAddressBill'    => $clientAdSave,
                'site_idClientContact'  => $clientPersSave
            );

            // now save the new client as a new site.
            $siteSave = $this->getDbTable('site')->saveRow($newSite, null);

        } catch (Exception $e) {
        	$log = Zend_Registry::get('log');
        	$log->err($e);
            $this->getDbTable('client')->getAdapter()->rollBack();
            return false;
        }

        $this->getDbTable('client')->getAdapter()->commit();

        return array(
            $clientSave,
            $siteSave
        );
    }

    /**
     * Updates a client.
     *
     * @param array $post
     * @return false|int
     */
    public function saveClient($post)
    {
        if (!$this->checkAcl('saveClient')) {
            throw new Power_Model_Acl_Exception('Insufficient rights');
        }

        /* @var $form Power_Form_Client_Save */
        $form = $this->getForm('clientEdit');
        
        /* @var $form Power_Form_Doc_Client */
        $docForm = $this->getForm('docClient');

        if ($post['client_dateExpiryLoa'] === '') {
            $client_dateExpiryLoaValidateRules = $form->getElement('client_dateExpiryLoa')
                ->getValidator('Date');

            $form->getElement('client_dateExpiryLoa')
                ->removeValidator('Date');
        }

        // Check if form is valid.
        if (!$form->isValid($post) || !$docForm->isValid($post)) {

            if (isset($client_dateExpiryLoaValidateRules)) {
                $form->getElement('client_dateExpiryLoa')
                    ->addValidator($client_dateExpiryLoaValidateRules);
            }
            return false;
        }

        $form->removeElement('client_docLoa');
        $data = $form->getValues();

        // add filters to the docForm.
        // add filters to the docForm.
        Power_Model_Doc::addUploadFilter(
            Power_Model_Doc::$docClient, $docForm, $data['client_idClient']
        );

        // upload file.
        $upload = $docForm->getValues();

        // get filtered values and return results.
        return $this->_saveClient($data);
    }

    /**
     * saves client row.
     *
     * @param array $data
     * @return false|int
     */
    protected function _saveClient($data)
    {
        // format date for database.
        $dateValue =  ($data['client_dateExpiryLoa'] === '') ? '01-01-1970' : $data['client_dateExpiryLoa'];
        $date = new Zend_Date($dateValue, Zend_Date::DATE_SHORT);
        //$date->set($date, Zend_Date::DATE_SHORT);
        $data['client_dateExpiryLoa'] = $date->toString('yyyy-MM-dd');

        $client = array_key_exists('client_idClient', $data) ?
            $this->getClientById($data['client_idClient']) : null;
        
        $this->clearCache(array('client', 'site'));

        return $this->getDbTable('client')->saveRow($data, $client);
    }

    public function checkClientAddress($post)
    {
        $form = $this->getForm('clientAddressSave');

        if (!$form->isValid($post)) {
            return false;
        }

        $post = $form->getValues();

        $client = $this->getClientById($post['clientAd_idClient']);
        // search clients address for possible duplicates.
        $select = $client->getRow()->select()
            ->where('clientAd_address1 like ?', '%' . $post['clientAd_address1'] . '%')
            ->where('clientAd_postcode = ?', $post['clientAd_postcode']);
        $addresses = $client->getAllClientAddresses($select);

        //$log = Zend_Registry::get('log');
        //$log->info("checkClientAddress:addresses: ".$addresses);
    }

    /**
     * Updates a client address.
     *
     * @param array $post
     * @return false|int
     */
    public function saveClientAddress($post, $doCheck = true)
    {
        if (!$this->checkAcl('saveClientAddress')) {
            throw new Power_Model_Acl_Exception('Insufficient rights');
        }

        $form = $this->getForm('clientAddressSave');

        if (!$form->isValid($post)) {
            return false;
        }

        if ('add' === $post['type'] && true === $doCheck) {
            $dupilcates = $this->checkClientAddress($form->getValues());
        }

        // get filtered values and return results.
        return $this->_saveClientAddress($form->getValues());
    }

    /**
     * saves client address row.
     *
     * @param array $data
     * @return false|int
     */
    protected function _saveClientAddress($data)
    {
        $clientAd = array_key_exists('clientAd_idAddress', $data) ?
            $this->getClientAddressById($data['clientAd_idAddress']) : null;
        
        $this->clearCache(array('clientAddress'));

        return $this->getDbTable('clientAddress')->saveRow($data, $clientAd);
    }

    /**
     * Updates a client contact.
     *
     * @param array $post
     * @return false|int
     */
    public function saveClientPersonnel($post)
    {
        if (!$this->checkAcl('saveClientPersonnel')) {
            throw new Power_Model_Acl_Exception('Insufficient rights');
        }

        $form = $this->getForm('clientPersonnelSave');

        if ($post['type'] == 'edit') {
            $form->excludeEmailFromValidation('clientPers_email', array(
                'field' => 'clientPers_email',
                'value' => $this->getClientPersonnelById($post['clientPers_idClientPersonnel'])
                    ->clientPers_email
            ));
        }

        if (!$form->isValid($post)) {
            return false;
        }

        // get filtered values and return results.
        return $this->_saveClientPersonnel($form->getValues());
    }

    /**
     * saves client contact row.
     *
     * @param array $data
     * @return false|int
     */
    protected function _saveClientPersonnel($data)
    {
        $clientPers = array_key_exists('clientPers_idClientPersonnel', $data) ?
            $this->getClientPersonnelById($data['clientPers_idClientPersonnel']) : null;
        
        $this->clearCache(array('clientPersonnel'));

        return $this->getDbTable('clientPersonnel')->saveRow($data, $clientPers);
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
    public function setAcl(Zend_Acl $acl)
    {
        parent::setAcl($acl);

        // implement rules here.
        $this->_acl->allow('user', $this)
            ->allow('admin', $this);

        return $this;
    }
}
