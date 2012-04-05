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
class Power_Model_DbTable_Supplier extends ZendSF_Model_DbTable_Abstract
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
        'supplierContact'   => array(
            'columns'       => 'supplier_idSupplierContact',
            'refTableClass' => 'Power_Model_DbTable_Supplier_Contact',
            'refColumns'    => 'supplierCo_idSupplierContact'
        ),
        'user'              => array(
            'columns'       => array(
                'supplier_userCreate',
                'supplier_userModify'
            ),
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
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
            ->where('supplier_idSupplier = ?', $id);

        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function searchSuppliers(array $search, $sort = '', $count = null, $offset = null)
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
                'supplier_contact',
                'supplier_idSupplierContact = supplierCo_idSupplierContact',
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

        if (!$search['contact'] == '') {
            $select->orWhere('supplierCo_name like ?', '%' . $search['contact'] . '%')
                ->orWhere('supplierCo_email like ?', '%' . $search['contact'] . '%')
                ->orWhere('supplierCo_address1 like ?', '%' . $search['contact'] . '%')
                ->orWhere('supplierCo_address2 like ?', '%' . $search['contact'] . '%')
                ->orWhere('supplierCo_address3 like ?', '%' . $search['contact'] . '%')
                ->orWhere('supplierCo_postcode like ?', '%' . $search['contact'] . '%');
        }

        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $result = $this->searchSuppliers($search);
        return $result->count();
    }

    public function insert(array $data)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['supplier_dateCreate'] = new Zend_Db_Expr('CURDATE()');
        $data['supplier_userCreate'] = $auth->getId();

        $this->_log->info(Zend_Debug::dump($data, "\nINSERT: " . __CLASS__ . "\n", false));

        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['supplier_dateModify'] = new Zend_Db_Expr('CURDATE()');
        $data['supplier_userModify'] = $auth->getId();

        $this->_log->info(Zend_Debug::dump($data, "\nUPDATE: " . __CLASS__ . "\n", false));

        return parent::update($data, $where);
    }
}
