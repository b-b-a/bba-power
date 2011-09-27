<?php
/**
 * Contract.php
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
 * Mapper Class for Contract.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Mapper_Contract extends ZendSF_Model_Mapper_Acl_Abstract
{
    /**
     * @var string the DbTable class name
     */
    protected $_dbTableClass = 'Power_Model_DbTable_Contract';

    /**
     * @var sting the model class name
     */
    protected $_modelClass = 'Power_Model_Contract';

    /**
     * Searches for a contracts by either contract, meter or both.
     *
     * @return array
     */
    protected function _getSearch($search, $select)
    {
        if ($search === null) {
            return $select;
        }

        if (!$search['contract'] == '') {
            $select->where('client_name like ? ', '%'. $search['contract'] . '%')
                ->orWhere('contract_reference like ? ', '%'. $search['contract'] . '%')
                ->orWhere('contract_desc like ? ', '%'. $search['contract'] . '%');
        }

        if (!$search['meter'] == '') {
            $select->where('meter_numberMain like ?', '%'. $search['meter'] . '%')
                ->orWhere('meter_type like ?', '%' . $search['meter'] . '%');
        }

        return $select;
    }

    public function numRows($search)
    {
        /* @var $select Zend_Db_Table_Select */
        $select = $this->getDbTable()->getContractList();

        $select = $this->_getSearch($search, $select);

        $result = $this->fetchAll($select, true);

        return $result->count();
    }

    public function getContractList($search = null, $sort = '', $count = null, $offset = null)
    {
        $select = $this->getDbTable()->getContractList();

        $log = Zend_Registry::get('log');
        $log->info($search);

        $select = $this->_getSearch($search, $select);

        $select->limit($count, $offset);

        if($sort == '') {
            $sort = 'client_name';
        }

        if(strchr($sort,'-')) {
            $sort = substr($sort, 1, strlen($sort));
            $order = 'DESC';
        } else {
            $order = 'ASC';
        }

        $select->order($sort . ' ' . $order);

        return $this->fetchAll($select);
    }

    public function getContractById($id)
    {
        $select = $this->getDbTable()
            ->select($id)
            ->where('contract_idContract = ?', $id);

        return $this->fetchRow($select);
    }

    public function delete($id)
    {
        if (!$this->checkAcl('delete')) {
            throw new ZendSF_Acl_Exception('Deleting contracts is not allowed.');
        }

        $where = $this->getDbTable()
            ->getAdapter()
            ->quoteInto('contract_idContract = ?', $id);

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
