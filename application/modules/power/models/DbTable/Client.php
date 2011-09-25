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
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Database adapter class for the Client table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Client extends Zend_Db_Table_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'client';

    /**
     * @var string primary key
     */
    protected $_primary = 'client_idClient';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'clientAd'  => array(
            'columns'       => 'client_idAddress',
            'refTableClass' => 'Power_Model_DbTable_ClientAddress',
            'refColumns'    => 'clientAd_idAddress'
		),
        'clientCo'  => array(
            'columns'       => 'client_idClientContact',
            'refTableClass' => 'Power_Model_DbTable_ClientContacts',
            'refColumns'    => 'clientCo_idClientContact'
        ),
        'user'      => array(
            'columns'       => array(
                'client_userCreate',
                'client_userModify'
            ),
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    public function getClientList()
    {
        return $this->select(false)
            ->setIntegrityCheck(false)
            ->from('client', array(
                'client_idClient',
                'client_name',
                'client_desc' => 'SUBSTR(client_desc, 1, 15)'
            ))
            ->join('client_address', 'client_idAddress = clientAd_idAddress', array(
                'clientAd_addressName',
                'clientAd_address1',
                'clientAd_postcode'
            ));
    }
}
