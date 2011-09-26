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
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Database adapter class for the Contract table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Contract extends Zend_Db_Table_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'contract';

    /**
     * @var string primary key
     */
    protected $_primary = 'contract_idContract';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'contractPrevious'          => array(
            'columns'       => 'contract_idContractPrevious',
            'refTableClass' => 'Power_Model_DbTable_Contract',
            'refColumns'    => 'contract_idContract'
        ),
        'client'                    => array(
            'columns'       => 'contract_idClient',
            'refTableClass' => 'Power_Model_DbTable_Client',
            'refColumns'    => 'client_idClient'
        ),
        'tenderSelected'            => array(
            'columns'       => 'contract_idTenderSelected',
            'refTableClass' => 'Power_Model_DbTable_Tender',
            'refColumns'    => 'tender_idTender'
        ),
        'supplierContactSelected'   => array(
            'columns'       => 'contract_idSupplierContactSelected',
            'refTableClass' => 'Power_Model_DbTable_SupplierContact',
            'refColumns'    => 'supplierCo_idSupplierContact'
        ),
        'user'                      => array(
            'columns'       => array(
                'contract_userCreate',
                'contract_userModify'
            ),
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    public function getContractList()
    {
        return $this->select(false)
            ->setIntegrityCheck(false)
            ->from('contract', array(
                'contract_idContract' => 'contract_idContract',
                'contract_reference',
                'contract_dateStart',
                'contract_dateEnd',
                'contract_desc'
            ))
            ->join(
                'client',
                'client_idClient = contract_idClient ',
                array('client_name')
            )
            ->joinLeft(
                'meter_contract',
                'meterContract_idContract = contract_idContract',
                null
            )
            ->joinLeft(
                'meter',
                'meter_idMeter = meterContract_idMeter',
                array('meter_numberMain', 'meter_type')
           )
           ->group('contract_idContract');
    }
}
