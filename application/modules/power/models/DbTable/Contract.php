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
    protected $_referenceMap = array();

    public function getContractDetails()
    {
       return $this->select(false)
            ->setIntegrityCheck(false)
            ->from('contract', array(
                'contract_idContract' => 'contract_idContract', 'contract_idClient',
                'contract_status', 'contract_dateStart',
                'contract_dateEnd', 'contract_desc'))
            ->join(
                'client',
                'client_idClient = contract_idClient ',
                array('client_name')
            )
            ->join(
                'meter_contract',
                'meterContract_idContract = contract_idContract',
                null
            )
            ->join(
                'meter',
                'meter_idMeter = meterContract_idMeter',
                array('meter_numberMain')
           )
           ->group('contract_idContract')
           ->order('client_name ASC');
    }
}
