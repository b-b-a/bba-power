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
class Power_Model_DbTable_Contract extends Power_Model_DbTable_Abstract
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
     * @var string row class
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_Contract';

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
        'supplierPersonnelSelected'   => array(
            'columns'       => 'contract_idSupplierPersonnelSelected',
            'refTableClass' => 'Power_Model_DbTable_Supplier_Personnel',
            'refColumns'    => 'supplierPers_idSupplierPersonnel'
        ),
        'contractStatus' => array(
            'columns'       => 'contract_status',
            'refTableClass' => 'Power_Model_DbTable_Tables',
            'refColumns'    => 'tables_key'
        ),
        'contractType' => array(
            'columns'       => 'contract_type',
            'refTableClass' => 'Power_Model_DbTable_Tables',
            'refColumns'    => 'tables_key'
        ),
        'userCreate'    => array(
            'columns'       => 'contract_userCreate',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        ),
        'userModify'    => array(
            'columns'       => 'contract_userModify',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    protected $_nullAllowed = array(
        'contract_idTenderSelected',
        'contract_idSupplierPersonnelSelected',
        'contract_idUserAgent',
        'contract_userModify'
    );

    public function getContractById($id)
    {
        return $this->find($id)->current();
    }
    
    public function getDuplicateContracts($ref, $type, $clientId, $dateStart, $ignore=null)
    {
    	$select = $this->select();
    	
    	if ($ignore) {
    		$select->where('contract_idContract != ?', $ignore);
    	}
    	
    	$select->where('contract_type = ?', $type)
            ->where('contract_idClient = ?', $clientId)
    		->where('contract_dateStart = ?', $dateStart);
    	
    	if ($ref && $ref != '') {
    		$select->orWhere('contract_reference = ?', $ref);
    	}
    	
    	return $this->fetchAll($select);
    }

    protected function _getSearchContractsSelect(array $search)
    {
        $select = $this->select(false)->setIntegrityCheck(false)
            ->from('contract', array(
                'contract_idContract' => 'contract_idContract',
                'contract_reference',
                'contract_type' => $this->_getTablesValue('contract_type'),
                'contract_status' => $this->_getTablesValue('contract_status'),
                'contract_dateStart',
                'contract_dateEnd',
                'contract_desc' => 'SUBSTR(contract_desc, 1, 40)'
            ))->join(
                'client',
                'client_idClient = contract_idClient ',
                array('client_name')
            )->joinLeft(
                'meter_contract',
                'meterContract_idContract = contract_idContract',
                array('meter_count' => '(
                    SELECT COUNT(meterContract_idMeter)
                    FROM meter_contract
                    WHERE meterContract_idContract = contract_idContract
                )')
            )->joinLeft(
                'meter',
                'meter_idMeter = meterContract_idMeter',
                null
            ) ->joinLeft(
                'tender',
                'tender_idTender = contract_idTenderSelected',
                null
            )->joinLeft(
                'supplier',
                'supplier_idSupplier = tender_idSupplier',
                array('supplier_nameShort')
           )->group('contract_idContract');

        if (!$search['contract'] == '') {
            if (substr($search['contract'], 0, 1) == '=') {
                $id = (int) substr($search['contract'], 1);
                $select->where('contract_idContract = ?', $id);
            } else {
                $select->orWhere('client_name LIKE ? ', '%'. $search['contract'] . '%')
                    ->orWhere('contract_reference LIKE ? ', '%'. $search['contract'] . '%')
                    ->orWhere('contract_desc LIKE ? ', '%'. $search['contract'] . '%');
            }
        }

        if (!$search['meter'] == '') {
            $select->orWhere('meter_numberMain LIKE ?', '%'. $search['meter'] . '%')
                ->orWhere('meter_type LIKE ?', '%' . $search['meter'] . '%');
        }

        if (isset($search['contract_idClient'])) {
            $select->where('contract_idClient = ?', $search['contract_idClient']);
        }

        if (isset($search['idSite'])) {
            $select->where('meter_idSite = ?', $search['idSite']);
        }

        return $select;
    }

    public function searchContracts(array $search, $sort = '', $count = null, $offset = null)
    {
        $select = $this->_getSearchContractsSelect($search);
        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $select = $this->_getSearchContractsSelect($search);
        //$select->reset(Zend_Db_Select::COLUMNS);
        //$select->columns(array('numRows' => 'COUNT(contract_idContract)'));
        $result = $this->fetchAll($select);

        return $result->count();
    }
}
