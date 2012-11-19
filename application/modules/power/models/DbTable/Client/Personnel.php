<?php
/**
 * ClientPersonnel.php
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
 * Database adapter class for the ClientPersonnel table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Client_Personnel extends BBA_Model_DbTable_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'client_personnel';

    /**
     * @var string primary key
     */
    protected $_primary = 'clientPers_idClientPersonnel';

    /**
     * @var string row class
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_Client_Personnel';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'client'        => array(
            'columns'       => 'clientPers_idClient',
            'refTableClass' => 'Power_Model_DbTable_Client',
            'refColumns'    => 'client_idClient'
        ),
        'clientAddress' => array(
            'columns'       => 'clientPers_idAddress',
            'refTableClass' => 'Power_Model_DbTable_ClientAddress',
            'refColumns'    => 'clientAd_idAddress'
        ),
        'userCreate'    => array(
            'columns'       => 'clientPers_userCreate',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        ),
        'userModify'    => array(
            'columns'       => 'clientPers_userModify',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    protected $_nullAllowed = array(
        'clientPers_userModify'
    );

    public function getClientPersonnelById($id)
    {
        return $this->find($id)->current();
    }

    public function getClientPersonnelByClientId($id)
    {
        $select = $this->select()->where('clientPers_idClient = ?', $id);
        return $this->fetchAll($select);
    }
    
    public function getDuplicateEmails($data)
    {
    	$select = $this->select();
    
    	if ($data['clientPers_idClientPersonnel']) {
    		$select->where('clientPers_idClientPersonnel != ?', $data['clientPers_idClientPersonnel']);
    	}
    	 
    	if ($data['clientPers_idClient']) {
    		$select->where('clientPers_idClient = ?', $data['clientPers_idClient']);
    	}
    
    	$select->where('clientPers_email = ?', $data['clientPers_email']);
    	 
    	return $this->fetchAll($select);
    }

    protected function _getSearchPersonnelSelect(array $search)
    {
        $select = $this->select(false)->setIntegrityCheck(false)
            ->from('client_personnel')
            ->join('tables', 'tables_name = "clientPers_type" AND tables_key = clientPers_type')
            ->join('client_address', 'clientPers_idAddress = clientAd_idAddress')
            ->where('clientPers_idClient = ?', $search['clientPers_idClient']);

        if (isset($search['clientPers_idAddress'])) {
            $select->where('clientPers_idAddress = ? ', $search['clientPers_idAddress']);
        }

        return $select;
    }

    public function searchContact(array $search, $sort = '', $count = null, $offset = null)
    {
        $select = $this->_getSearchPersonnelSelect($search);
        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $select = $this->_getSearchPersonnelSelect($search);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(array('numRows' => 'COUNT(clientPers_idAddress)'));
        $result = $this->fetchRow($select);

        return $result->numRows;
    }
}
