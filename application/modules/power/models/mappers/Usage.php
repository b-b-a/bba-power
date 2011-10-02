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
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Mapper Class for Usage.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Mapper_Usage extends BBA_Model_Mapper_Abstract
{
    /**
     * @var string the DbTable class name
     */
    protected $_dbTableClass = 'Power_Model_DbTable_Usage';

    /**
     * @var sting the model class name
     */
    protected $_modelClass = 'Power_Model_Usage';

    public function getUsageByMeterId($search, $sort = '', $count = null, $offset = null)
    {
        $col = key($search);
        $id = current($search);

        $select = $this->getDbTable()
            ->select()
            ->where($col . ' = ?', $id);

        $select = $this->getLimit($select, $count, $offset);

        $select = $this->getSort($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $col = key($search);
        $id = current($search);

        return parent::numRows(array(
            'col' => $col,
            'id'  => $id
        ), true);
    }

    public function save()
    {
        if (!$this->checkAcl('save')) {
            throw new ZendSF_Acl_Exception('saving meters usage is not allowed.');
        }

        return parent::save('usageSave');
    }

    public function delete($id)
    {
        if (!$this->checkAcl('delete')) {
            throw new ZendSF_Acl_Exception('Deleting meter usage is not allowed.');
        }

        $where = $this->getDbTable()
            ->getAdapter()
            ->quoteInto('usage_idUsage = ?', $id);

        return parent::delete($where);
    }
}
