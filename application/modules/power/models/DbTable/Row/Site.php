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
    protected $_siteAd;

    /**
     * @var Power_Model_DbTable_Row_Client_Address
     */
    protected $_billingAd;

    /**
     * @var Power_Model_DbTable_Row_Client_Personnel
     */
    protected $_clientPers;
    
    protected $_dateKeys = array(
        'site_dateCreate',
        'site_dateModify'
    );

    protected $_dateFormat = 'dd/MM/yyyy';
    
    public function getShortDesc()
    {
        return substr($this->getRow()->site_desc, 0, 200);
    }

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
         if (!$this->_siteAd instanceof Power_Model_DbTable_Row_Client_Address) {
            $this->_siteAd = $this->getRow()
                ->findParentRow( 'Power_Model_DbTable_Client_Address', 'siteAd');
        }

        return (null === $row) ? $this->_siteAd : $this->_siteAd->$row;
    }

    public function getBillingAddress($row=null)
    {
        if (!$this->_billingAd instanceof Power_Model_DbTable_Row_Client_Address) {
            $this->_billingAd = $this->getRow()
                ->findParentRow( 'Power_Model_DbTable_Client_Address', 'siteAddressBill');
        }

        return (null === $row) ? $this->_billingAd : $this->_billingAd->$row;
    }

    public function getClientPersonnel($row=null)
    {
        if (!$this->_clientPers instanceof Power_Model_DbTable_Row_Client_Personnel) {
            $this->_clientPers = $this->getRow()
                ->findParentRow( 'Power_Model_DbTable_Client_Personnel', 'siteClientPers');
        }

        return (null === $row) ? $this->_clientPers : $this->_clientPers->$row;
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
    
    /**
     * Returns row as an array, with optional date formating.
     *
     * @param string $dateFormat
     * @return array
     */
    public function toArray($dateFormat=null, $raw=false)
    {
        $array = array();

        foreach ($this->getRow() as $key => $value) {
            
            if (in_array($key, $this->_dateKeys)) {
                $date = new Zend_Date($value);
                $value = $date->toString((null === $dateFormat) ? $this->_dateFormat : $dateFormat);
            }

            if (true === $raw) {
                $array[$key] = $value;
            } else {
                switch ($key) {
                    case 'site_desc':
                        $array[$key] = $this->getShortDesc();
                        break;
                    default:
                        $array[$key] = $value;
                        break;
                }
            }
        }

        return $array;
    }
}
