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

    );

    /**
     * Aggregates the site, client and client_address tables
     * into the meter list.
     *
     * @return Zend_Db_Table_Select
     */
    public function getMeterDetails()
    {
       return $this->select(false)
            ->setIntegrityCheck(false)
            ->from('meter', array('meter_idMeter', 'meter_numberSerial', 'meter_type', 'meter_mpan13'))
            ->join('site', 'site_idSite = meter_idSite', null)
            ->join(
                'client_address',
                'clientAd_idAddress = site_idAddress',
                array('site' => 'CONCAT(clientAd_address1,"/n",clientAd_address2,"/n",clientAd_address3,"/n",clientAd_postcode)')
            )
            ->join(
                'client',
                'client_idClient = site_idClient ',
                array('client' => 'client_name')
            )
            ->order('client_name ASC');
    }
}
