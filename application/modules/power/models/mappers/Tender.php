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
class Power_Model_Mapper_Tender extends ZendSF_Model_Mapper_Acl_Abstract
{
    /**
     * @var string the DbTable class name
     */
    protected $_dbTableClass = 'Power_Model_DbTable_Tender';

    /**
     * @var sting the model class name
     */
    protected $_modelClass = 'Power_Model_Tender';

    public function getTendersByContractId($id)
    {
        $select = $this->getDbTable()
            ->select(false)
            ->setIntegrityCheck(false)
            ->from('tender')
            ->join('contract', 'contract_idContract = tender_idContract')
            ->join('client', 'client_idClient = contract_idClient')
            ->join('supplier', 'tender_idSupplier = supplier_idSupplier')
            ->joinLeft('supplier_contact', 'tender_idSupplierContact = SupplierCo_idSuppliercontact')
            ->where('tender_idContract = ?', $id);

        return $this->fetchAll($select);
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

    /**
     * Injector for the acl, the acl can be injected directly
     * via this method.
     *
     * We add all the access rules for this resource here, so we first call
     * parent method to add $this as the resource then we
     * define it rules here.
     *
     * @param Zend_Acl_Resource_Interface $acl
     * @return ZendSF_Model_Mapper_Abstract
     */
    public function setAcl(Zend_Acl $acl)
    {
        parent::setAcl($acl);

        // implement rules here.
        $this->_acl->allow('admin', $this)
            ->deny('admin', $this, array('delete'));

        return $this;
    }

}
