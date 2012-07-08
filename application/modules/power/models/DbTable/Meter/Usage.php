<?php
/**
 * Usage.php
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
 * Database adapter class for the Usage table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Meter_Usage extends BBA_Model_DbTable_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'pusage';

    /**
     * @var string primary key
     */
    protected $_primary = 'usage_idUsage';

    /**
     * @var string row class
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_Meter_Usage';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'meter' => array(
            'columns'       => 'usage_idMeter',
            'refTableClass' => 'Power_Model_DbTable_Meter',
            'refColumns'    => 'meter_idMeter'
        ),
        'usageType' => array(
            'columns'       => 'usage_type',
            'refTableClass' => 'Power_Model_DbTable_Tables',
            'refColumns'    => 'tables_key'
        ),
        'userCreate'    => array(
            'columns'       => 'usage_userCreate',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        ),
        'userModify'    => array(
            'columns'       => 'usage_userModify',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    protected $_nullAllowed = array(
        'usage_userModify'
    );

    public function getUsageById($id)
    {
        return $this->find($id)->current();
    }

    protected function _getSearchUsageSelect(array $search)
    {
        $select = $this->select()
            ->from('pusage')
            ->columns(array(
                'usage_usageTotal' => '(usage_usageDay + usage_usageNight + usage_usageOther)'
            ))
            ->where('usage_idMeter = ?', $search['usage_idMeter']);

        return $select;
    }

    public function searchUsage(array $search, $sort = '', $count = null, $offset = null)
    {
        $select = $this->_getSearchUsageSelect($search);
        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $select = $this->_getSearchUsageSelect($search);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(array('numRows' => 'COUNT(usage_idUsage)'));
        $result = $this->fetchRow($select);

        return $result->numRows;
    }
}
