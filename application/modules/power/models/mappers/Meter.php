<?php
/**
 * Meter.php
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
 * Mapper Class for Meter.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Mapper_Meter extends BBA_Model_Mapper_Abstract
{
    /**
     * @var string the DbTable class name
     */
    protected $_dbTableClass = 'Power_Model_DbTable_Meter';

    /**
     * @var sting the model class name
     */
    protected $_modelClass = 'Power_Model_Meter';

    public function getMetersBySiteId($search, $sort = '', $count = null, $offset = null)
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

    public function getMetersByContractId($search, $sort = '', $count = null, $offset = null)
    {
        $col = key($search);
        $id = current($search);

        $select = $this->getDbTable()
            ->select(false)
            ->setIntegrityCheck(false)
            ->from('meter')
            ->join('site', 'site_idSite = meter_idSite', null)
            ->join('client_address', 'clientAd_idAddress = site_idAddress', array(
                'clientAd_addressName','clientAd_postcode'
            ))
            ->join('client', 'client_idClient = site_idClient', null)
            ->joinLeft('client_contact', 'client_idClientContact = clientCo_idClientContact', array(
                'clientCo_name'
            ))
            ->join('meter_contract', 'meter_idMeter = meterContract_idMeter', null)
            ->where($col . ' = ?', $id);

        $select = $this->getLimit($select, $count, $offset);

        $select = $this->getSort($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search, $child = false)
    {
        if ($child) {
            $col = key($search);
            $id = current($search);

            if ($col == 'meterContract_idContract') {
                $select = $this->getDbTable()
                    ->select(false)
                    ->setIntegrityCheck(false)
                    ->from('meter')
                    ->join('site', 'site_idSite = meter_idSite', null)
                    ->join('client_address', 'clientAd_idAddress = site_idAddress')
                    ->join('client', 'client_idClient = site_idClient')
                    ->joinLeft('client_contact', 'client_idClientContact = clientCo_idClientContact')
                    ->join('meter_contract', 'meter_idMeter = meterContract_idMeter')
                    ->where($col . ' = ?', $id);

                $result = $this->fetchAll($select, true);

                return $result->count();
            }

            return parent::numRows(array(
                'col' => $col,
                'id'  => $id
            ), true);
        } else {
            return parent::numRows($search);
        }
    }

    public function getMeterDetails($id)
    {
        /* @var $select Zend_Db_Table_Select */
        $select = $this->getDbTable()->getMeterDetails();
        $select->where('meter_idMeter = ?', $id);

        $row = $this->fetchRow($select, true);

        $model = new Power_Model_Meter($row);
        $model->setCols($this->getDbTable()->info('cols'));

        return $model;
    }

    public function save()
    {
        if (!$this->checkAcl('save')) {
            throw new ZendSF_Acl_Exception('saving meters is not allowed.');
        }

        return parent::save('meterSave');
    }

    public function delete($id)
    {
        if (!$this->checkAcl('delete')) {
            throw new ZendSF_Acl_Exception('Deleting meters is not allowed.');
        }

        $where = $this->getDbTable()
            ->getAdapter()
            ->quoteInto('meter_idMeter = ?', $id);

        return parent::delete($where);
    }
}
