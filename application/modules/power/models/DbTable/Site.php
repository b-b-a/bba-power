<?php
/**
 * Site.php
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
 * Database adapter class for the Site table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Site extends Power_Model_DbTable_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'site';

    /**
     * @var string primary key
     */
    protected $_primary = 'site_idSite';

    /**
     * @var string row class
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_Site';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'siteMeters' => array(
            'columns'           => 'site_idSite',
            'refTableClass'     => 'Power_Model_DbTable_Meter',
            'refColumns'        => 'meter_idSite'
		),
        'siteClient' => array(
            'columns'           => 'site_idClient',
            'refTableClass'     => 'Power_Model_DbTable_Client',
            'refColumns'        => 'client_idClient'
		),
        'siteAd' => array(
            'columns'           => 'site_idAddress',
            'refTableClass'     => 'Power_Model_DbTable_Client_Address',
            'refColumns'        => 'clientAd_idAddress'
		),
        'siteAddressBill' => array(
            'columns'           => 'site_idAddressBill',
            'refTableClass'     => 'Power_Model_DbTable_Client_Address',
            'refColumns'        => 'clientAd_idAddress'
		),
        'siteClientPers' => array(
            'columns'           => 'site_idClientPersonnel',
            'refTableClass'     => 'Power_Model_DbTable_Client_Personnel',
            'refColumns'        => 'clientPers_idClientPersonnel'
		),
        'userCreate'    => array(
            'columns'       => 'site_userCreate',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        ),
        'userModify'    => array(
            'columns'       => 'site_userModify',
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    protected $_nullAllowed = array(
        'site_idAddressBill',
        'site_idClientPersonnel',
        'site_userModify'
    );

    public function getSiteById($id)
    {
        return $this->find($id)->current();
    }

    protected function _getSearchSitesSelect(array $search)
    {
        $select = $this->select(false)->setIntegrityCheck(false)
            ->from('site', array('site_idSite'))
            ->join('client_address', 'clientAd_idAddress = site_idAddress', array(
                'clientAd_addressName',
                'clientAd_address1',
                'clientAd_address2',
                'clientAd_address3',
                'clientAd_postcode'
            ))
            ->join('client', 'client_idClient = site_idClient', array('client_name'))
            ->joinLeft(
                'client_personnel', 'clientPers_idClientPersonnel = site_idClientPersonnel', array(
				'clientPers_name'
            ));

        if (!$search['site'] == '') {
            if (substr($search['site'], 0, 1) == '=') {
                $id = (int) substr($search['site'], 1);
                $select->where('(site_idSite = ?)', $id);
            } else {
                $select->orWhere('(clientAd_addressName like ?', '%' . $search['site'] . '%')
                    ->orWhere('clientAd_address1 like ?', '%' . $search['site'] . '%')
                    ->orWhere('clientAd_address2 like ?', '%' . $search['site'] . '%')
                    ->orWhere('clientAd_address3 like ?', '%' . $search['site'] . '%')
                    ->orWhere('clientAd_postcode like ?)', '%' . $search['site'] . '%');
            }
        }

        else if (!$search['client'] == '') {
            $select->orWhere('(client_name like ?', '%' . $search['client'] . '%')
                ->orWhere('client_desc like ?)', '%' . $search['client'] . '%');
        }

        if (isset($search['idClient'])) {
            $select->where('site_idClient = ?', $search['idClient']);
        }
        
        $select = $this->_getAccessClient($select, 'site');

        return $select;
    }

    public function searchSites(array $search, $sort = '', $count = null, $offset = null)
    {
        $select = $this->_getSearchSitesSelect($search);
        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $select = $this->_getSearchSitesSelect($search);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->columns(array('numRows' => 'COUNT(site_idSite)'));
        $result = $this->fetchRow($select);

        return $result->numRows;
    }
}
