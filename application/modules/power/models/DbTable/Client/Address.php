<?php
/**
 * ClientAddress.php
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
 * Database adapter class for the ClientAddress table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Client_Address extends BBA_Model_DbTable_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'client_address';

    /**
     * @var string primary key
     */
    protected $_primary = 'clientAd_idAddress';

    /**
     * @var string row class
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_Client_Address';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'client'    => array(
            'columns'       => 'clientAd_idClient',
            'refTableClass' => 'Power_Model_DbTable_Client',
            'refColumns'    => 'client_idClient'
        ),
        'userCreate'    => array(
            'columns'       => 'clientAd_userCreate',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        ),
        'userModify'    => array(
            'columns'       => 'clientAd_userModify',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    protected $_nullAllowed = array(
        'clientAd_userModify'
    );

    public function getClientAddressById($id)
    {
        return $this->find($id)->current();
    }

    public function getClientAddressesByClientId($id)
    {
        $select = $this->select()
            ->where('clientAd_idClient = ?', $id)
            ->order(array('clientAd_postcode', 'clientAd_address1'));

        return $this->fetchAll($select);
    }

    public function getAvailableSiteAddressesByClientId($id)
    {
        $select = $this->select()
            ->from('client_address')
            ->where('clientAd_idAddress NOT IN (?)', new Zend_Db_Expr(
                $this->select(false)->setIntegrityCheck(false)
                ->from('site', array('site_idAddress'))
                ->where('site_idClient = ?', $id))
            )
            ->where('clientAd_idClient = ?', $id)
            ->order(array('clientAd_postcode', 'clientAd_address1'));

        return $this->fetchAll($select);
    }
    
    public function getDuplicateAddresses($postcode, $ignore=null)
    {
    	$select = $this->select();
    	 
    	if ($ignore) {
    		$select->where('clientAd_idAddress != ?', $ignore);
    	}
    	
    	if (substr($postcode, -4, 1) != ' ') {
    		$replace = substr($postcode, -3, 3);
    		$postcode = str_replace($replace, ' ' . $replace, $postcode);
    	}
    	 
    	$select->where('clientAd_postcode = ?', $postcode)
    		->orWhere('clientAd_postcode = ?', str_replace(' ', '', $postcode));
    	
    	return $this->fetchAll($select);
    }

    protected function _getSearchAddressSelect(array $search)
    {
        $select = $this->select(false)
            ->from('client_address')
            ->where('clientAd_idClient = ?', $search['clientAd_idClient']);

        return $select;
    }

    public function searchAddress(array $search, $sort = '', $count = null, $offset = null)
    {
        $select = $this->_getSearchAddressSelect($search);
        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $select = $this->_getSearchAddressSelect($search);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(array('numRows' => 'COUNT(clientAd_idClient)'));
        $result = $this->fetchRow($select);

        return $result->numRows;
    }
}
