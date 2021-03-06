<?php
/**
 * Tables.php
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
 * Database adapter class for the Tables table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Tables extends Power_Model_DbTable_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'tables';

    /**
     * @var string primary key
     */
    protected $_primary = 'tables_idTables';

    /**
     * @var string row class
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_Tables';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array();

    public function getTableById($id)
    {
        return $this->find($id)->current();
    }

    public function getSelectListByName($name)
    {
        $select = $this->select(true)
            ->columns(array('tables_key', 'tables_value'))
            ->where('tables_name = ?', $name)
            ->order('tables_sort ASC');

        return $this->fetchAll($select);
    }
    
    public function getTableItemByNameKey($name, $key)
    {
        $select = $this->select(true)
            ->columns(array('tables_key', 'tables_value'))
            ->where('tables_name = ?', $name)
            ->where('tables_key = ?', $key)
            ->order('tables_sort ASC');

        return $this->fetchAll($select);
    }
}
