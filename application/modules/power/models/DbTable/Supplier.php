<?php
/**
 * Supplier.php
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
 * Database adapter class for the Supplier table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Supplier extends Power_Model_DbTable_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'supplier';
    /**
     * @var string primary key
     */
    protected $_primary = 'supplier_idSupplier';

    /**
     * @var string row class
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_Supplier';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'supplierPers'   => array(
            'columns'       => 'supplier_idSupplierPersonnel',
            'refTableClass' => 'Power_Model_DbTable_Supplier_Personnel',
            'refColumns'    => 'supplierPers_idSupplierPersonnel'
        ),
        'userCreate'    => array(
            'columns'       => 'supplier_userCreate',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        ),
        'userModify'    => array(
            'columns'       => 'supplier_userModify',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    protected $_nullAllowed = array(
        'supplier_idSupplierPersonnel',
        'supplier_userModify'
    );

    public function getSupplierById($id)
    {
        return $this->find($id)->current();
    }

    public function getSupplierContractsBySupplierId($id, $sort = '', $count = null, $offset = null)
    {
        $select = $this->select(false)->setIntegrityCheck(false)
            ->from('supplier', null)
            ->join('tender', 'tender_idSupplier = supplier_idSupplier', null)
            ->join('contract', 'contract_idContract = tender_idContract', array(
                'contract_idContract',
                'contract_type' => '(SELECT tables_value FROM tables WHERE tables_key = contract_type AND tables_name = "contract_type")',
                'contract_status' => '(SELECT tables_value FROM tables WHERE tables_key = contract_status AND tables_name = "contract_status")',
                'contract_dateStart',
                'contract_dateEnd',
                'contract_reference'
            ))
            ->join('client', 'client_idClient = contract_idClient', array(
                'client_name'
            ))
            ->group('contract_idContract')
            ->where('supplier_idSupplier = ?', $id)
            ->where('tender_idTender = contract_idTenderSelected');

        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        $select = $this->_getAccessClient($select, 'client');

        return $this->fetchAll($select);
    }

    protected function _getSearchSuppliersSelect(array $search)
    {
        $select = $this->select(false)->setIntegrityCheck(false)
            ->from('supplier', array(
                'supplier_idSupplier',
                'supplier_name',
                'supplier_nameShort',
                'supplier_address1',
                'supplier_postcode'
            ))
            ->joinLeft(
                'supplier_personnel',
                'supplier_idSupplierPersonnel = supplierPers_idSupplierPersonnel',
                null
            );

        if (!$search['supplier'] == '') {
            if (substr($search['supplier'], 0, 1) == '=') {
                $id = (int) substr($search['supplier'], 1);
                $select->where('supplier_idSupplier = ?', $id);
            } else {
                $select->orWhere('supplier_name like ?', '%' . $search['supplier'] . '%')
                    ->orWhere('supplier_address1 like ?', '%' . $search['supplier'] . '%')
                    ->orWhere('supplier_address2 like ?', '%' . $search['supplier'] . '%')
                    ->orWhere('supplier_address3 like ?', '%' . $search['supplier'] . '%')
                    ->orWhere('supplier_postcode like ?', '%' . $search['supplier'] . '%');
            }
        }

        if (!$search['personnel'] == '') {
            $select->orWhere('supplierPers_name like ?', '%' . $search['personnel'] . '%')
                ->orWhere('supplierPers_email like ?', '%' . $search['personnel'] . '%')
                ->orWhere('supplierPers_address1 like ?', '%' . $search['personnel'] . '%')
                ->orWhere('supplierPers_address2 like ?', '%' . $search['personnel'] . '%')
                ->orWhere('supplierPers_address3 like ?', '%' . $search['personnel'] . '%')
                ->orWhere('supplierPers_postcode like ?', '%' . $search['personnel'] . '%');
        }

        return $select;
    }

    public function searchSuppliers(array $search, $sort = '', $count = null, $offset = null)
    {
        $select = $this->_getSearchSuppliersSelect($search);
        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $select = $this->_getSearchSuppliersSelect($search);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(array('numRows' => 'COUNT(supplier_idSupplier)'));
        $result = $this->fetchRow($select);

        return $result->numRows;
    }
}
