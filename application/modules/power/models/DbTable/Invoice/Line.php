<?php
/**
 * Line.php
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
 * Database adapter class for the Line table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Invoice_Line extends ZendSF_Model_DbTable_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'invoice_line';

    /**
     * @var string primary key
     */
    protected $_primary = 'invoiceLine_idInvoiceLine';

    /**
     * @var string row class.
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_Invoice_Line';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'invoice'   => array(
            'columns'       => 'invoiceLine_idInvoice',
            'refTableClass' => 'Power_Model_DbTable_Invoice',
            'refColumns'    => 'invoice_idInvoice'
        ),
        'meter'     => array(
            'columns'       => 'invoiceLine_idMeter',
            'refTableClass' => 'Power_Model_DbTable_Meter',
            'refColumns'    => 'meter_idMeter'
        ),
        'contract'  => array(
            'columns'       => 'invoiceLine_idContract',
            'refTableClass' => 'Power_Model_DbTable_Contract',
            'refColumns'    => 'contract_idContract'
        )
    );

    public function getInvoiceLineById($id)
    {
        return $this->find($id)->current();
    }

    protected function _getSearchInvoiceLinesSelect(array $search)
    {
        $select = $this->select()->setIntegrityCheck(false)
            ->from('invoice_line', array(
                '*',
                'invoiceLine_consumption' => new Zend_Db_Expr('(
                    SELECT SUM(usage_usageNight) + SUM(usage_usageDay) + SUM(usage_usageOther)
                    FROM invoice_usage, pusage
                    WHERE invoiceUsage_idInvoiceLine = invoiceLine_idInvoiceLine
                    AND invoiceUsage_idUsage = usage_idUsage
                 )')
            ))
            ->join('invoice', 'invoiceLine_idInvoice = invoice_idInvoice', array(
                'invoice_numberInvoice'
            ))
            ->join('meter', 'invoiceLine_idMeter = meter_idMeter', array(
                'meter_idMeter',
                'meter_numberMain'
            ))
            ->join('contract', 'invoiceLine_idContract = contract_idContract', array(
                'contract_idContract'
            ));

        if (isset($search['invoiceLine_idInvoice'])) {
            $select->where('invoiceLine_idInvoice = ?', $search['invoiceLine_idInvoice']);
        }

        if (isset($search['invoiceLine_idMeter'])) {
            $select->where('invoiceLine_idMeter = ?', $search['invoiceLine_idMeter']);
        }

        if (isset($search['invoiceLine_idContract'])) {
            $select->where('invoiceLine_idContract = ?', $search['invoiceLine_idContract']);
        }

        return $select;
    }

    public function searchInvoiceLines(array $search, $sort = '', $count = null, $offset = null)
    {
        $select = $this->_getSearchInvoiceLinesSelect($search);
        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $select = $this->_getSearchInvoiceLinesSelect($search);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(array('numRows' => 'COUNT(invoiceLine_idInvoiceLine)'));
        $result = $this->fetchRow($select);

        return $result->numRows;
    }
}
