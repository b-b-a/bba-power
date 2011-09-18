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
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Database adapter class for the Meter table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Meter extends Zend_Db_Table_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'meter';

    /**
     * @var string primary key
     */
    protected $_primary = 'meter_idMeter';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'site'      => array(
            'columns'       => 'meter_idSite',
            'refTableClass' => 'Power_Model_DbTable_Site',
            'refColumns'    => 'site_idSite'
        ),
        'user'      => array(
            'columns'       => array(
                'meter_userCreate',
                'meter_userModify'
            ),
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    /**
     * Aggregates the site, client and client_address tables
     * into the meter details.
     *
     * @return Zend_Db_Table_Select
     */
    public function getMeterDetails()
    {
       return $this->select(false)
            ->setIntegrityCheck(false)
            ->from('meter')
            ->join('site', 'site_idSite = meter_idSite', null)
            ->join('client_address', 'clientAd_idAddress = site_idAddress')
            ->join('client', 'client_idClient = site_idClient')
            ->joinLeft('client_contact', 'client_idClientContact = clientCo_idClientContact')
            ->join('meter_contract', 'meter_idMeter = meterContract_idMeter')
            ->join('contract', 'meterContract_idContract = contract_idContract')
            ->order('client_name ASC');
    }
    
    /**
     * Cherry pick which columns to return for speed.
     * 
     * @return Zend_Db_Table_Select
     */
    public function getMeterList()
    {
        return $this->select(false)
            ->setIntegrityCheck(false)
            ->from('meter', array(
                'meter_idMeter',
                'meter_type',
                'meter_numberMain'
            ))
            ->join('site', 'site_idSite = meter_idSite', null)
            ->join('client_address', 'clientAd_idAddress = site_idAddress', array(
                'clientAd_addressName',
                'clientAd_postcode'
            ))
            ->join('client', 'client_idClient = site_idClient', array(
                'client_name'
            ))
            ->join('meter_contract', 'meter_idMeter = meterContract_idMeter', null)
            ->join('contract', 'meterContract_idContract = contract_idContract', array(
                'contract_status',
                'contract_dateEnd'
            ))
            ->order('client_name ASC');
    }
}
