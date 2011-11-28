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
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Database adapter class for the Tender table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Tender extends Zend_Db_Table_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'tender';

    /**
     * @var string primary key
     */
    protected $_primary = 'tender_idTender';

    /**
     * @var string row class
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_Tender';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'contract'          => array(
            'columns'       => 'tender_idContract',
            'refTableClass' => 'Power_Model_DbTable_Contract',
            'refColumns'    => 'contract_idContract'
        ),
        'supplier'          => array(
            'columns'       => 'tender_idSupplier',
            'refTableClass' => 'Power_Model_DbTable_Supplier',
            'refColumns'    => 'supplier_idSupplier'
        ),
        'supplierContact'   => array(
            'columns'       => 'tender_idSupplierContact',
            'refTableClass' => 'Power_Model_DbTable_SupplierContact',
            'refColumns'    => 'supplierCo_idSupplierContact'
        ),
        'user'              => array(
            'columns'       => array(
                'tender_userCreate',
                'tender_userModify'
            ),
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

}
