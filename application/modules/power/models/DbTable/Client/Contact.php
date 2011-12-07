<?php
/**
 * ClientContact.php
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
 * Database adapter class for the ClientContact table.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_DbTable_Client_Contact extends ZendSF_Model_DbTable_Abstract
{
    /**
     * @var string database table
     */
    protected $_name = 'client_contact';

    /**
     * @var string primary key
     */
    protected $_primary = 'clientCo_idClientContact';

    /**
     * @var string row class
     */
    protected $_rowClass = 'Power_Model_DbTable_Row_Client_Contact';

    /**
     * @var array Reference map for parent tables
     */
    protected $_referenceMap = array(
        'client'        => array(
            'columns'       => 'clientCo_idClient',
            'refTableClass' => 'Power_Model_DbTable_Client',
            'refColumns'    => 'client_idClient'
        ),
        'clientAddress' => array(
            'columns'       => 'clientCo_idAddress',
            'refTableClass' => 'Power_Model_DbTable_ClientAddress',
            'refColumns'    => 'clientAd_idAddress'
        ),
        'user'          => array(
            'columns'       => array(
                'clientCo_userCreate',
                'clientCo_userModify'
            ),
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
    );

    public function getClientContactById($id)
    {
        return $this->find($id)->current();
    }

    public function getClientContactsByClientId($id)
    {
        $select = $this->select()->where('clientCo_idClient = ?', $id);
        return $this->fetchAll($select);
    }

    public function searchContact(array $search, $sort = '', $count = null, $offset = null)
    {
        $select = $this->select(false)->setIntegrityCheck(false)
            ->from('client_contact')
            ->join('client_address', 'clientCo_idAddress = clientAd_idAddress')
            ->where('clientCo_idClient = ?', $search['clientCo_idClient']);

        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $result = $this->searchContact($search);
        return $result->count();
    }

    public function insert(array $data)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['clientCo_dateCreate'] = new Zend_Db_Expr('CURDATE()');
        $data['clientCo_userCreate'] = $auth->getId();
        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['clientCo_dateModify'] = new Zend_Db_Expr('CURDATE()');
        $data['clientCo_userModify'] = $auth->getId();
        return parent::update($data, $where);
    }
}
