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

    public function getAvailClientMeters($id)
    {
        $log = Zend_Registry::get('log');

        $contractMapper = new Power_Model_Mapper_Contract();

        // get all meters that belong to client.
        // get the current contract.
        $select = $contractMapper->getDbTable()->select()
                ->where('contract_idContract = ?', $id);

        $contract = $contractMapper->fetchRow($select);

        // get first part of contract status, the bit before the hyphon.
        $type = preg_replace('/-.+/', '', $contract->type);

        // get all client sites
        $siteMapper = new Power_Model_Mapper_Site();

        $select = $siteMapper->getDbTable()
                ->select()->where('site_idClient = ?', $contract->idClient);

        $sites = $siteMapper->fetchAll($select, true);

        // now get meters
        $meters = array();

        $select = $siteMapper->getDbTable()->select();

        // run through the list of sites and get their meters.
        foreach ($sites as $site) {
            $select->reset();

            $rowSet = $site->findDependentRowset(
                'Power_Model_DbTable_Meter',
                'site',
                $select->where('meter_type = ?', $type)
            );

            // run through the list of meters,
            // geting their attached contracts if any are attached to one.
            /* @var $row Zend_Db_Table_Row */
            foreach ($rowSet as $row) {
                $select->reset();

                // this should get the most recent contract attached to a meter.
                $con = $row->findManyToManyRowset(
                    'Power_Model_DbTable_Contract',
                    'Power_Model_DbTable_MeterContract',
                    'meter',
                    'contract',
                    $select->order('contract_dateStart DESC')->limit(1)
                );

                //$log->info($con->toArray());

                $meterCo = new Power_Model_MeterContract($row);
                $meterCo->setCols($this->getDbTable()->info('cols'));
                $acceptedStatus = array('current', 'signed', 'selected', 'choosing');

                if ($con->count() > 0) {
                    $meterContract = $con->current()->toArray();

                    // check accepted status
                    if (in_array($meterContract['contract_status'], $acceptedStatus)) {
                        $meterCoEndDate = new Zend_Date($meterContract['contract_dateEnd']);

                        // check if date is earlier than current contract date.
                        if ($meterCoEndDate->isEarlier($contract->dateStart)) {
                            foreach($meterContract as $key => $val) {
                                $meterCo->$key = $val;
                            }
                            // get kva data.
                            $select = $this->getDbTable()->select()
                                ->from('meter_contract', 'meterContract_kvaNominated')
                                ->where('meterContract_idContract = ?', $meterCo->contract_idContract);
                            $kva = $this->fetchRow($select);
                            $meterCo->meterContract_kvaNominated = $kva->kvaNominated;
                            $meters[] = $meterCo;
                        }
                    }
                } else {
                    $meters[] = $meterCo;
                }
            }
        }

        //$log->info($meters);
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

        $model = new $this->_modelClass();
        $model->setCols($this->getDbTable()->info('cols'));

        foreach($data['meters'] as $key => $value) {
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
