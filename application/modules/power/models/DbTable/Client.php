<?php
/**
 * Client.php
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
 * Database adapter class for the Client table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Client extends ZendSF_Model_DbTable_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'client';

    /**
     * @var string primary key
     */
    protected $_primary = 'client_idClient';

    /**
     * @var string row class
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_Client';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'clientAd'  => array(
            'columns'       => 'client_idAddress',
            'refTableClass' => 'Power_Model_DbTable_Client_Address',
            'refColumns'    => 'clientAd_idAddress'
		),
        'clientCo'  => array(
            'columns'       => 'client_idClientContact',
            'refTableClass' => 'Power_Model_DbTable_Client_Contact',
            'refColumns'    => 'clientCo_idClientContact'
        ),
        'user'      => array(
            'columns'       => array(
                'client_userCreate',
                'client_userModify'
            ),
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    public function getClientById($id)
    {
        return $this->find($id)->current();
    }

    public function searchClients(array $search, $sort = '', $count = null, $offset = null)
    {
        $select = $this->select(false)->setIntegrityCheck(false)
            ->from('client', array(
                'client_idClient',
                'client_name',
                'client_desc' => 'SUBSTR(client_desc, 1, 15)'
            ))
            ->joinLeft('client_address', 'client_idAddress = clientAd_idAddress', array(
                'clientAd_addressName',
                'clientAd_address1',
                'clientAd_postcode'
            ));

        if (!$search['client'] == '') {
            if (substr($search['client'], 0, 1) == '=') {
                $id = (int) substr($search['client'], 1);
                $select->where('client_idClient = ?', $id);
            } else {
                $select->orWhere('client_name like ?', '%' . $search['client'] . '%')
                    ->orWhere('client_desc like ?', '%' . $search['client'] . '%');
            }
        }

        if (!$search['address'] == '') {
            $select->orWhere('clientAd_addressName like ?', '%' . $search['address'] . '%')
                ->orWhere('clientAd_address1 like ?', '%' . $search['address'] . '%')
                ->orWhere('clientAd_address2 like ?', '%' . $search['address'] . '%')
                ->orWhere('clientAd_address3 like ?', '%' . $search['address'] . '%')
                ->orWhere('clientAd_postcode like ?', '%' . $search['address'] . '%');
        }

        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $result = $this->searchClients($search);
        return $result->count();
    }

    public function insert(array $data)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['client_dateCreate'] = new Zend_Db_Expr('CURDATE()');
        $data['client_userCreate'] = $auth->getId();

        $this->_log->info(Zend_Debug::dump($data, "\nINSERT: " . __CLASS__ . "\n", false));

        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['client_dateModify'] = new Zend_Db_Expr('CURDATE()');
        $data['client_userModify'] = $auth->getId();

        $this->_log->info(Zend_Debug::dump($data, "\nUPDATE: " . __CLASS__ . "\n", false));

        return parent::update($data, $where);
    }
}
