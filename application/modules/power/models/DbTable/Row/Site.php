<?php

/**
 * Site.php
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
 * @subpackage Model_DbTable_Row
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Database class for the Site table row.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable_Row
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Row_Site extends ZendSF_Model_DbTable_Row_Abstract
{
    /**
     * @var Power_Model_DbTable_Row_Client
     */
    protected $_client;

    /**
     * @var Power_Model_DbTable_Row_Client_Address
     */
    protected $_siteAddress;

    /**
     * @var Power_Model_DbTable_Row_Client_Address
     */
    protected $_billingAddress;

    /**
     * @var Power_Model_DbTable_Row_Client_Contact
     */
    protected $_clientContact;

    public function getClient($row=null)
    {
        if (!$this->_client instanceof Power_Model_DbTable_Row_Client) {
            $this->_client = $this->getRow()
                ->findParentRow( 'Power_Model_DbTable_Client', 'siteClient');
        }

        return (null === $row) ? $this->_client : $this->_client->$row;
    }

    public function getSiteAddress($row=null)
    {
         if (!$this->_siteAddress instanceof Power_Model_DbTable_Row_Client_Address) {
            $this->_siteAddress = $this->getRow()
                ->findParentRow( 'Power_Model_DbTable_Client_Address', 'siteAddress');
        }

        return (null === $row) ? $this->_siteAddress : $this->_siteAddress->$row;
    }

    public function getBillingAddress($row=null)
    {
        if (!$this->_billingAddress instanceof Power_Model_DbTable_Row_Client_Address) {
            $this->_billingAddress = $this->getRow()
                ->findParentRow( 'Power_Model_DbTable_Client_Address', 'siteAddressBill');
        }

        return (null === $row) ? $this->_billingAddress : $this->_billingAddress->$row;
    }

    public function getClientContact($row=null)
    {
        if (!$this->_clientContact instanceof Power_Model_DbTable_Row_Client_Contact) {
            $this->_clientContact = $this->getRow()
                ->findParentRow( 'Power_Model_DbTable_Client_Contact', 'siteClientContact');
        }

        return (null === $row) ? $this->_clientContact : $this->_clientContact->$row;
    }

    public function getMeters($sort = null, $count = null, $offset = null)
    {
        $select = $this->getRow()->select();

        $select = $this->getRow()->getTable()->getLimit($select, $count, $offset);
        $select = $this->getRow()->getTable()->getSortOrder($select, $sort);

        return $this->getRow()->findDependentRowset(
            'Power_Model_DbTable_Meter',
            'site',
            $select);
    }

    public function getMetersByType($type)
    {
        $select = $this->getRow()->select()->where('meter_type = ?', $type);

        return $this->getRow()->findDependentRowset(
            'Power_Model_DbTable_Meter',
            'site',
            $select);
    }
}
