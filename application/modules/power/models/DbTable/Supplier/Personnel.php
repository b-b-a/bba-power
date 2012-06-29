<?php
/**
 * SupplierPersonnel.php
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
 * Database adapter class for the SupplierPersonnel table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Supplier_Personnel extends ZendSF_Model_DbTable_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'supplier_personnel';

    /**
     * @var string primary key
     */
    protected $_primary = 'supplierPers_idSupplierPersonnel';

    /**
     * @var string row class
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_Supplier_Personnel';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'supplier'  => array(
            'columns'       => 'supplierPers_idSupplier',
            'refTableClass' => 'Power_Model_DbTable_Supplier',
            'refColumns'    => 'supplier_idSupplier'
        ),
        'userCreate'    => array(
            'columns'       => 'supplierPers_userCreate',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        ),
        'userModify'    => array(
            'columns'       => 'supplierPers_userModify',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    public function getSupplierPersonnelById($id)
    {
        return $this->find($id)->current();
    }

    protected function _getSearchPersonnelSelect($search)
    {
         $select = $this->select(false)->setIntegrityCheck(false)
            ->from('supplier_personnel')
            ->where('supplierPers_idSupplier = ?', $search);

         return $select;
    }

    public function searchPersonnel($id, $sort = '', $count = null, $offset = null)
    {
        $select = $this->_getSearchPersonnelSelect($id)
            ->join('tables',
                'tables_name = "supplierPers_type" AND tables_key = supplierPers_type',
                array('supplierPers_type_tables' => 'tables_value')
            );
        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $select = $this->_getSearchPersonnelSelect($search);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(array('numRows' => 'COUNT(supplierPers_idSupplierPersonnel)'));
        $result = $this->fetchRow($select);

        return $result->numRows;
    }

    public function insert(array $data)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['supplierPers_dateCreate'] = new Zend_Db_Expr('CURDATE()');
        $data['supplierPers_userCreate'] = $auth->getId();

        $this->_log->info(Zend_Debug::dump($data, "\nINSERT: " . __CLASS__ . "\n", false));

        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['supplierPers_dateModify'] = new Zend_Db_Expr('CURDATE()');
        $data['supplierPers_userModify'] = $auth->getId();

        $this->_log->info(Zend_Debug::dump($data, "\nUPDATE: " . __CLASS__ . "\n", false));

        return parent::update($data, $where);
    }
}
