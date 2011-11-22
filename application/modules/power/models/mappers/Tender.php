<?php
/**
 * Tender.php
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
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Mapper Class for Tender.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Mapper_Tender extends BBA_Model_Mapper_Abstract
{
    /**
     * @var string the DbTable class name
     */
    protected $_dbTableClass = 'Power_Model_DbTable_Tender';

    /**
     * @var sting the model class name
     */
    protected $_modelClass = 'Power_Model_Tender';

    public function getTendersByContractId($search, $sort = '', $count = null, $offset = null)
    {
        foreach ($search as $key => $value) {
            if ($value) {
                $col = $key;
                $id = $value;
            }
        }

        $select = $this->getDbTable()
            ->select(false)
            ->setIntegrityCheck(false)
            ->from('tender')
            ->join('contract', 'contract_idContract = tender_idContract')
            ->join('client', 'client_idClient = contract_idClient')
            ->join('supplier', 'tender_idSupplier = supplier_idSupplier')
            ->joinLeft('supplier_contact', 'tender_idSupplierContact = SupplierCo_idSuppliercontact')
            ->where($col . ' = ?', $id);

        $select = $this->getLimit($select, $count, $offset);

        $select = $this->getSort($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search, $child = false)
    {
        foreach ($search as $key => $value) {
            if ($value) {
                $col = $key;
                $id = $value;
            }
        }

        $select = $this->getDbTable()
            ->select(false)
            ->setIntegrityCheck(false)
            ->from('tender')
            ->join('contract', 'contract_idContract = tender_idContract')
            ->join('client', 'client_idClient = contract_idClient')
            ->join('supplier', 'tender_idSupplier = supplier_idSupplier')
            ->joinLeft('supplier_contact', 'tender_idSupplierContact = SupplierCo_idSuppliercontact')
            ->where($col . ' = ?', $id);

         $result = $this->fetchAll($select, true);

        return $result->count();
    }

    public function getTenderDetails($id)
    {
        $select = $this->getDbTable()
            ->select(false)
            ->setIntegrityCheck(false)
            ->from('tender')
            ->join('contract', 'contract_idContract = tender_idContract')
            ->join('client', 'client_idClient = contract_idClient')
            ->join('supplier', 'tender_idSupplier = supplier_idSupplier')
            ->joinLeft('supplier_contact', 'tender_idSupplierContact = SupplierCo_idSuppliercontact')
            ->where('tender_idTender = ?', $id);

        $row = $this->fetchAll($select);

        return $row[0];
    }

    public function save($form)
    {
        if (!$this->checkAcl('save')) {
            throw new ZendSF_Acl_Exception('saving tenders is not allowed.');
        }

        return parent::save($form);
    }

    public function delete($id)
    {
        if (!$this->checkAcl('delete')) {
            throw new ZendSF_Acl_Exception('Deleting tenders is not allowed.');
        }

        $where = $this->getDbTable()
            ->getAdapter()
            ->quoteInto('tender_idTender = ?', $id);

        return parent::delete($where);
    }
}
