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
     * Gets the data store list, using search parameters.
     *
     * @param array $post
     * @return string JSON string
     */
    public function getClientDataStore(array $post)
    {
        return $this->_getDojoDataStore(
            $post,
            'clientSearch',
            'client',
            'searchClients',
            'client_idClient'
        );
    }

    public function saveClient()
    {
        /*
        if ($this->_request->getParam('client_dateExpiryLoa') === '') {
            $client_dateExpiryLoaValidateRules = $this->getForm($form)
                ->getElement('client_dateExpiryLoa')
                ->getValidator('Date');

            $this->getForm($form)->getElement('client_dateExpiryLoa')
                ->removeValidator('Date');
        }

        if (!$this->getForm($form)->isValid($this->_request->getPost())) {

            if (isset($client_dateExpiryLoaValidateRules)) {
                $this->getForm($form)
                    ->getElement('client_dateExpiryLoa')
                    ->addValidator($client_dateExpiryLoaValidateRules);
            }
        }
        */
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
        $this->_acl->allow('admin', $this)
            ->deny('admin', $this, array('delete'));

        return $this;
    }
}
