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
class Power_Model_Client extends ZendSF_Model_Acl_Abstract
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
     * @return null|Power_Model_DbTable_Row_Client_Contact
     */
    public function getClientContactById($id)
    {
        $id = (int) $id;
        return $this->getDbTable('clientContact')->getClientContactById($id);
    }

    /**
     * Get all contacts that belong to a client.
     *
     * @param int $id
     * @return null|array
     */
    public function getClientContactsByClientId($id)
    {
        $id = (int) $id;
        return $this->getDbTable('clientContact')->getClientContactsByClientId($id);
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

        return $store->toJson();
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

        return $store->toJson();
    }

    /**
     * Gets the contact data store list, using search parameters.
     *
     * @param array $post
     * @return string JSON string
     */
    public function getClientContactDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $dataObj = $this->getDbTable('clientContact')->searchContact($post, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'clientCo_idClientContact');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('clientContact')->numRows($post)
        );

        return $store->toJson();
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
            throw new ZendSF_Acl_Exception('Insufficient rights');
        }

        $log = Zend_Registry::get('log');

        $this->getDbTable('client')->getAdapter()->beginTransaction();

        try {
            /**
             * TODO: must add checking on each stage.
             */
            // save client first.
            $clientSave = $this->saveClient($post);

            // then save client address.
            $post['clientAd_idClient'] = $clientSave;
            $clientAdSave = $this->saveClientAddress($post);

            // now save client contact
            $post['clientCo_idClient'] = $clientSave;
            $post['clientCo_idAddress'] = $clientAdSave;
            $clientCoSave = $this->saveClientContact($post);

            // now update client with address and contact ids.
            $form = $this->getForm('clientSave');

            $form->addElement('hidden', 'client_idAddress', array(
                'value' => $clientAdSave
            ));

            $form->addElement('hidden', 'client_idClientContact', array(
                'value' => $clientCoSave
            ));

            $post['client_idClient'] = $clientSave;
            $post['client_idAddress'] = $clientAdSave;
            $post['client_idClientContact'] = $clientCoSave;
            $clientSave = $this->saveClient($post);

        } catch (Exception $e) {
            $log->info($e);
            $this->getDbTable('client')->getAdapter()->rollBack();
            return 0;
        }

        $this->getDbTable('client')->getAdapter()->commit();

        return $clientSave;
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
            throw new ZendSF_Acl_Exception('Insufficient rights');
        }

        $form = $this->getForm('clientSave');

        if ($post['client_dateExpiryLoa'] === '') {
            $client_dateExpiryLoaValidateRules = $form->getElement('client_dateExpiryLoa')
                ->getValidator('Date');

            $form->getElement('client_dateExpiryLoa')
                ->removeValidator('Date');
        }

        if (!$form->isValid($post)) {
            if (isset($client_dateExpiryLoaValidateRules)) {
                $form->getElement('client_dateExpiryLoa')
                    ->addValidator($client_dateExpiryLoaValidateRules);
            }

            return false;
        }

        // get filtered values
        $data = $form->getValues();

        // format date for database.
        $dateValue =  ($data['client_dateExpiryLoa'] === '') ? '01-01-1970' : $data['client_dateExpiryLoa'];
        $date = new Zend_Date($dateValue);
        $date->set($date->toString('yy'), Zend_Date::YEAR_SHORT);
        $data['client_dateExpiryLoa'] = $date->toString('yyyy-MM-dd');


        $client = array_key_exists('client_idClient', $data) ?
            $this->getClientById($data['client_idClient']) : null;

        return $this->getDbTable('client')->saveRow($data, $client);
    }

    /**
     * Updates a client address.
     *
     * @param array $post
     * @return false|int
     */
    public function saveClientAddress($post)
    {
        if (!$this->checkAcl('saveClientAddress')) {
            throw new ZendSF_Acl_Exception('Insufficient rights');
        }

        $form = $this->getForm('clientAddressSave');

        if (!$form->isValid($post)) {
            return false;
        }

        // get filtered values
        $data = $form->getValues();

        $clientAd = array_key_exists('clientAd_idAddress', $data) ?
            $this->getClientAddressById($data['clientAd_idAddress']) : null;

        return $this->getDbTable('clientAddress')->saveRow($data, $clientAd);
    }

    /**
     * Updates a client contact.
     *
     * @param array $post
     * @return false|int
     */
    public function saveClientContact($post)
    {
        if (!$this->checkAcl('saveClientContact')) {
            throw new ZendSF_Acl_Exception('Insufficient rights');
        }

        $form = $this->getForm('clientContactSave');

        if ($post['type'] == 'edit') {
            $form->excludeEmailFromValidation('clientCo_email', array(
                'field' => 'clientCo_email',
                'value' => $this->getClientContactById($post['clientCo_idClientContact'])
                    ->clientCo_email
            ));
        }

        if (!$form->isValid($post)) {
            return false;
        }

        // get filtered values
        $data = $form->getValues();

        $clientCo = array_key_exists('clientCo_idClientContact', $data) ?
            $this->getClientContactById($data['clientCo_idClientContact']) : null;

        return $this->getDbTable('clientContact')->saveRow($data, $clientCo);
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
