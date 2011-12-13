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
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * DAO to represent a single Contract.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Contract extends ZendSF_Model_Abstract
{
    /**
     * Get contract by their id
     *
     * @param  int $id
     * @return null|Power_Model_DbTable_Row_Contract
     */
    public function getContractById($id)
    {
        $id = (int) $id;
        return $this->getDbTable('contract')->getContractById($id);
    }

    /**
     * Gets the contract data store list, using search parameters.
     *
     * @param array $post
     * @return string
     */
    public function getContractDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $form = $this->getForm('contractSearch');
        $search = array();

        if ($form->isValid($post)) {
            $search = $form->getValues();
        }

        $dataObj = $this->getDbTable('contract')->searchContracts($search, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'contract_idContract');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('contract')->numRows($search)
        );

        return $store->toJson();
    }

    public function getMeterContractDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $dataObj = $this->getDbTable('meterContract')->searchMeterContracts($post, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'meter_idMeter');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('meterContract')->numRows($post)
        );

        return $store->toJson();
    }

    public function getTenderDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $dataObj = $this->getDbTable('tender')->searchTenders($post, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'tender_idTender');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('tender')->numRows($post)
        );

        return $store->toJson();
    }
}