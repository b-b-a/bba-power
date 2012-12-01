<?php
/**
 * Usage.php
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
 * Database adapter class for the Usage table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Invoice_Usage extends Power_Model_DbTable_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'invoice_usage';

    /**
     * @var array compound primary key
     */
    protected $_primary = array('invoiceUsage_idInvoiceLine', 'invoiceUsage_idUsage');

    /**
     * @var string row class.
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_Invoice_Usage';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'invoiceLine'   => array(
            'columns'       => 'invoiceUsage_idInvoiceLine',
            'refTableClass' => 'Power_Model_DbTable_Invoice_Line',
            'refColumns'    => 'invoiceLine_idInvoiceLine'
        ),
        'usage'         => array(
            'columns'       => 'invoiceUsage_idUsage',
            'refTableClass' => 'Power_Model_DbTable_Meter_Usage',
            'refColumns'    => 'usage_idUsage'
        )
    );

    protected function _getSearchInvoiceUsageSelect(array $search)
    {
        $select = $this->select()->setIntegrityCheck(false)
            ->from('invoice_usage', array(
                '*',
                'invoiceUsage_idInvoiceUsage' => new Zend_Db_Expr("
                    CONCAT(invoiceUsage_idInvoiceLine, '-', invoiceUsage_idUsage)
                ")
            ))
            ->join('pusage', 'invoiceUsage_idUsage = usage_idUsage', array(
                '*',
                'usage_totalUsage' => new Zend_Db_Expr(
                    'usage_usageDay + usage_usageNight + usage_usageOther'
                )
            ))
            ->join('meter', 'usage_idMeter = meter_idMeter', 'meter_numberMain')
            ->where('invoiceUsage_idInvoiceLine = ?', $search['invoiceUsage_idInvoiceLine']);

        return $select;
    }

    public function searchInvoiceUsage(array $search, $sort = '', $count = null, $offset = null)
    {
        $select = $this->_getSearchInvoiceUsageSelect($search);
        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $select = $this->_getSearchInvoiceUsageSelect($search);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(array('numRows' => 'COUNT(invoiceUsage_idInvoiceLine)'));
        $result = $this->fetchRow($select);

        return $result->numRows;
    }
}
