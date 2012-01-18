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
class Power_Model_DbTable_Client_Address extends ZendSF_Model_DbTable_Abstract
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
        'user'      => array(
            'columns'       => array(
                'clientAd_userCreate',
                'clientAd_userModify'
            ),
            'refTableClass' => 'Power_Model_DbTable_User',
            'refColumns'    => 'user_idUser'
        )
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
            ->where('clientAd_idClient = ?', $id);

        return $this->fetchAll($select);
    }

    public function searchAddress(array $search, $sort = '', $count = null, $offset = null)
    {
        $select = $this->select()->where('clientAd_idClient = ?', $search['clientAd_idClient']);

        $select = $this->getLimit($select, $count, $offset);
        $select = $this->getSortOrder($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search)
    {
        $result = $this->searchAddress($search);
        return $result->count();
    }

    public function insert(array $data)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['clientAd_dateCreate'] = new Zend_Db_Expr('CURDATE()');
        $data['clientAd_userCreate'] = $auth->getId();

        $this->_log->info(Zend_Debug::dump($data, "\nINSERT: " . __CLASS__ . "\n", false));

        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        $auth = Zend_Auth::getInstance()->getIdentity();
        $data['clientAd_dateModify'] = new Zend_Db_Expr('CURDATE()');
        $data['clientAd_userModify'] = $auth->getId();

        $this->_log->info(Zend_Debug::dump($data, "\nUPDATE: " . __CLASS__ . "\n", false));

        return parent::update($data, $where);
    }
}
