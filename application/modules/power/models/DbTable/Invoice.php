<?php
/**
 * Invoice.php
 *
 * Copyright (c) 2012 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of BBA Power.
 *
 * BBA Power is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BBA Power is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BBA Power.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Database adapter class for the Invoice table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Invoice extends ZendSF_Model_DbTable_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'invoice';

    /**
     * @var string primary key
     */
    protected $_primary = 'invoice_idInvoice';

    /**
     * @var string row class.
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_Invoice';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'supplier'  => array(
            'columns'       => 'invoice_idSupplier',
            'refTableClass' => 'Power_Model_DbTable_Supplier',
            'refColumns'    => 'supplier_idSupplier'
        )
    );

    public function getInvoiceById($id)
    {
        return $this->find($id)->current();
    }

    protected function _getSearchInvoiceSelect(array $search)
    {
        $select = $this->select()->setIntegrityCheck(false)
            ->from('invoice', '*')
            ->joinLeft('supplier', 'invoice_idSupplier = supplier_idSupplier');

        if (!$search['invoice'] == '') {
            if (substr($search['invoice'], 0, 1) == '=') {
                $id = (int) substr($search['invoice'], 1);
                $select->where('invoice_idInvoice = ?', $id);
            } else {
                $select->orWhere('invoice_numberInvoice like ?', '%' . $search['invoice'] . '%');
            }
        }

        if (!$search['supplier'] == '') {
            $select->orWhere('supplier_name like ?', '%' . $search['supplier'] . '%');
        }

        return $select;
    }

    public function searchInvoice(array $search, $sort='', $count=null, $offset=null)
    {
        $select = $this->_getSearchInvoiceSelect($search);
        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $select = $this->_getSearchInvoiceSelect($search);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(array('numRows' => 'COUNT(invoice_idInvoice)'));
        $result = $this->fetchRow($select);

        return $result->numRows;
    }
}
