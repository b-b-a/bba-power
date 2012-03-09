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
class Power_Model_DbTable_Meter_Usage extends ZendSF_Model_DbTable_Abstract
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
        'user'  => array(
            'columns'       => array(
                'usage_userCreate',
                'usage_userModify'
            ),
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    public function getUsageById($id)
    {
        return $this->find($id)->current();
    }

    public function searchUsage(array $search, $sort = '', $count = null, $offset = null)
    {
        $select = $this->select()
                ->from('pusage')
                ->columns(array(
                    'usage_usageTotal' => '(usage_usageDay + usage_usageNight + usage_usageOther)'
                ))
                ->where('usage_idMeter = ?', $search['usage_idMeter']);

        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $result = $this->searchUsage($search);
        return $result->count();
    }

    public function insert(array $data)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['usage_dateCreate'] = new Zend_Db_Expr('CURDATE()');
        $data['usage_userCreate'] = $auth->getId();

        $this->_log->info(Zend_Debug::dump($data, "INSERT: " . __CLASS__, false));

        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['usage_dateModify'] = new Zend_Db_Expr('CURDATE()');
        $data['usage_userModify'] = $auth->getId();

        $this->_log->info(Zend_Debug::dump($data, "\nUPDATE: " . __CLASS__ . "\n", false));

        return parent::update($data, $where);
    }
}
