<?php
/**
 * MeterContract.php
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
 * Mapper Class for MeterContract.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model_Mapper
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Mapper_MeterContract extends BBA_Model_Mapper_Abstract
{
    /**
     * @var string the DbTable class name
     */
    protected $_dbTableClass = 'Power_Model_DbTable_MeterContract';

    /**
     * @var sting the model class name
     */
    protected $_modelClass = 'Power_Model_MeterContract';

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
            ->join('meter_contract', 'meter_idMeter = meterContract_idMeter')
            ->where($col . ' = ?', $id);

        $select = $this->getLimit($select, $count, $offset);

        $select = $this->getSort($select, $sort);

        return $this->fetchAll($select);
    }

    public function numRows($search, $child = false)
    {
        $col = key($search);
        $id = current($search);

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

    /**
     * Selects all availiable meters that are not on current contract by the contract id.
     * Query example:
     *
     * <code>
     * SELECT meter_idMeter, meter_type, meter_numberMain, meterContract_kvaNominated,
     * contract_idContract, contract_idContractPrevious, contract_type, contract_status,
     * MAX(contract_dateStart) AS contract_dateStart, contract_dateEnd
     * FROM meter
     * LEFT JOIN meter_contract ON meter_idMeter = meterContract_idMeter
     * LEFT JOIN contract ON meterContract_idContract = contract_idContract
     * WHERE meter_idSite IN (
     *     SELECT site_idSite
     *     FROM contract
     *     JOIN site ON contract_idClient = site_idClient
     *     WHERE contract_idContract = 1709)
     * AND meter_type = (
     *     SELECT SUBSTRING_INDEX(contract_type,'-',1)
     *     FROM contract
     *     WHERE contract_idContract = 1709)
     * AND (contract_dateEnd < (
     *         SELECT contract_dateStart
     *         FROM contract
     *         WHERE contract_idContract = 1709)
     *     AND contract_status IN ('current', 'signed', 'selected', 'choosing')
     *     OR contract_idContract IS NULL)
     * GROUP BY meter_numberMain
     * Order by contract_idContract, meter_numberMain;
     * </code>
     *
     * @param string $id
     * @return array
     */
    public function getAvailClientMeters($id)
    {
        $log = Zend_Registry::get('log');

        // optimised query to get all availiable meter in one query.
        $select = $this->getDbTable()->select(false)->setIntegrityCheck(false)
            ->from('meter', array(
                'meter_idMeter', 'meter_type', 'meter_numberMain'
            ))
            ->joinLeft('meter_contract', 'meter_idMeter = meterContract_idMeter', array(
                'meterContract_kvaNominated'
            ))
            ->joinLeft('contract', 'meterContract_idContract = contract_idContract', array(
                'contract_idContract',
                'contract_idContractPrevious',
                'contract_type',
                'contract_status',
                'contract_dateStart' => 'MAX(contract_dateStart)',
                'contract_dateEnd'
            ))
            ->where('meter_idSite IN (?)', new Zend_Db_Expr(
                $this->getDbTable()->select(false)->setIntegrityCheck(false)
                    ->from('contract', null)
                    ->join('site', 'contract_idClient = site_idClient', array(
                        'site_idSite'
                    ))
                    ->where('contract_idContract = ?', $id)
            ))
            ->where('meter_type = (?)', new Zend_Db_Expr(
                $this->getDbTable()->select(false)->setIntegrityCheck(false)
                    ->from('contract', array(
                        'contract_type' => 'SUBSTRING_INDEX(contract_type,\'-\',1)'
                    ))
                    ->where('contract_idContract = ?', $id)
            ))
            ->where('(contract_dateEnd < (?)', new Zend_Db_Expr(
                $this->getDbTable()->select(false)->setIntegrityCheck(false)
                    ->from('contract', array('contract_dateStart'))
                    ->where('contract_idContract = ?', $id)
            ))
            ->where('contract_status IN (?)', array(
                'current', 'signed', 'selected', 'choosing'
            ))
            ->orWhere('contract_idContract IS NULL)')
            ->group('meter_numberMain')
            ->order(array('contract_idContract', 'meter_numberMain'));

        //$log->info($select->__toString());
        $meters = $this->fetchAll($select);

        return $meters;
    }

    public function save($data)
    {
        if (!$this->checkAcl('save')) {
            throw new ZendSF_Acl_Exception('adding meters to contracts is not allowed.');
        }

        $log = Zend_Registry::get('log');
        $log->info($data);

        $modelArray = array();

        foreach($data['meters'] as $key => $value) {
            $model = new $this->_modelClass();
            $model->setCols($this->getDbTable()->info('cols'));

            $model->idMeter = $value['id'];
            $model->kvaNominated = $value['kva'];
            $model->idContract = $data['contract'];

            if ($data['type'] == 'insert') {
                $model->setDateCreate();
                $model->userCreate = Zend_Auth::getInstance()->getIdentity()->getId();
            } else {
                 $model->userModify = Zend_Auth::getInstance()->getIdentity()->getId();
                 $model->setDateModify();
            }

            $modelArray[] = $model;
        }

        $log->info($modelArray);

        foreach ($modelArray as $model) {
            $modelData = $model->toArray();

            foreach ($modelData as $key => $value) {
                if ($value === null) {
                    unset($modelData[$key]);
                }
            }

            if ($data['type'] == 'insert') {
                $saved = $this->getDbTable()->insert($modelData);
            } else {
                $saved = $this->getDbTable()->update($modelData, array(
                    'meterContract_idContract = ?' => $data['idContract']
                ));
            }
        }

        return $saved;
    }

    public function delete($id)
    {
        if (!$this->checkAcl('delete')) {
            throw new ZendSF_Acl_Exception('Deleting meter contracts is not allowed.');
        }

        $where = $this->getDbTable()
            ->getAdapter()
            ->quoteInto('meterContract_idContract = ?', $id);

        return parent::delete($where);
    }
}
