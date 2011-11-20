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

        $this->getDbTable()->getAdapter()->beginTransaction();

        // get all meters that belong to client.
        // get the current contract.
        $select = $contractMapper->getDbTable()->select()
                ->where('contract_idContract = ?', $id);

        $contract = $contractMapper->fetchRow($select);

        $type = preg_replace('/-.+/', '', $contract->type);

        // get all client sites
        $siteMapper = new Power_Model_Mapper_Site();

        $select = $siteMapper->getDbTable()
                ->select()->where('site_idClient = ?', $contract->idClient);

        $sites = $siteMapper->fetchAll($select, true);

        // now get meters
        $meters = array();

        $select = $siteMapper->getDbTable()->select();

        foreach ($sites as $site) {
            $select->reset();

            $rowSet = $site->findDependentRowset(
                'Power_Model_DbTable_Meter',
                'site',
                $select->where('meter_type = ?', $type)
            );

            /* @var $row Zend_Db_Table_Row */
            foreach ($rowSet as $row) {
                $select->reset();

                // this should get the most recent contract attach to a meter.
                $con = $row->findManyToManyRowset(
                    'Power_Model_DbTable_Contract',
                    'Power_Model_DbTable_MeterContract',
                    'meter',
                    'contract'//,
                    //$select->from('contract')->columns(array(
                        //'contract_dateStart' => 'MAX(contract.contract_dateStart)'
                    //))
                );

                //$log->info($con);

                $meterCo = new Power_Model_MeterContract($row);
                $meterCo->setCols($this->getDbTable()->info('cols'));

                foreach ($con as $value) {
                    foreach($value as $key => $val) {
                        $meterCo->$key = $val;
                    }
                }

                // if there is a contract, compare the end date
                // if end date is earlier than new contract start date add to list
                // if there is no contract add meter to list.
                if ($meterCo->contract_dateEnd) {
                    $curCo = new Zend_Date($contract->dateStart);
                    $meterCoEndDate = new Zend_Date($meterCo->contract_dateEnd);

                    if ($meterCoEndDate->isEarlier($curCo)) {
                        $meters[] = $meterCo;
                    }
                } else {
                    $meters[] = $meterCo;
                }
            }
        }

        $this->getDbTable()->getAdapter()->commit();

        //$log->info($meters);
        return $meters;
    }
/*
    public function getAvailClientMeters($id)
    {
        $log = Zend_Registry::get('log');

        $select = $this->getDbTable()
            ->select(false)
            ->setIntegrityCheck(false)
            ->from('contract')
            ->where('contract_idContract = ?', $id);

        $contract = $this->fetchRow($select, true)->toArray();
        $select->reset();

        $contractType = explode('-', $contract['contract_type']);

        $log->info($contract);

        $subQuery2 = $this->getDbTable()
            ->select(false)
            ->setIntegrityCheck(false)
            ->from('meter', array('meter_idMeter'))
            ->joinLeft('site', 'meter_idSite = site_idSite', null)
            ->joinLeft('meter_contract', 'meter_idMeter = meterContract_idMeter', null)
            ->joinLeft('contract', 'meterContract_idContract = contract_idContract', null)
            ->where('site_idClient = ?', $contract['contract_idClient'])
            ->where('meter_type = ?', $contractType[0])
            ->where('contract_status IN (?)', array('current', 'signed', 'selected', 'choose'))
            ->where('CAST(? AS DATE) BETWEEN contract_dateStart AND contract_dateEnd', $contract['contract_dateStart']);

        $subQuery1 = $this->getDbTable()
            ->select(false)
            ->setIntegrityCheck(false)
            ->where('contract_status IN (?)', array('current', 'signed', 'selected', 'choose'))
            ->where('contract_dateEnd < ?', $contract['contract_dateStart'])
            ->where('meter_idMeter NOT IN ?', new Zend_Db_Expr('(' . $subQuery2 .')'))
            ;

        $select = $this->getDbTable()
            ->select(false)
            ->setIntegrityCheck(false)
            ->from('meter', array(
                'meter_idMeter',
                'meter_idSite',
                'meter_type',
                'meter_numberMain'
            ))
            ->joinLeft('site', 'meter_idSite = site_idSite', array('site_idClient'))
            ->joinLeft('meter_contract', 'meter_idMeter = meterContract_idMeter', array(
                'meterContract_kvaNominated'
            ))
            ->joinLeft('contract', 'meterContract_idContract = contract_idContract', array(
                'contract_idContract',
                'contract_idContractPrevious',
                'contract_type',
                'contract_status',
                'MAX(contract_dateStart)',
                'contract_dateEnd'
            ))
            ->where('site_idClient = ?', $contract['contract_idClient'])
            ->where('meter_type = ?', $contractType[0])
            //->where(new Zend_Db_Expr(implode(' ', $subQuery1->getPart('where'))))
            ->orWhere('contract_idContract IS NULL AND site_idClient = ?', $contract['contract_idClient'])
            ->group('meter_numberMain')
            ->order(array('contract_idContract', 'meter_numberMain'))
            ;

        $sql = "
            SELECT meter_idMeter, meter_type, meter_numberMain, meterContract_kvaNominated,
            contract_idContract, contract_idContractPrevious, contract_type, contract_status,
            MAX(contract_dateStart), contract_dateEnd
            FROM meter
            LEFT JOIN site ON meter_idSite = site_idSite
            LEFT JOIN meter_contract ON meter_idMeter = meterContract_idMeter
            LEFT JOIN contract ON meterContract_idContract = contract_idContract
            WHERE site_idClient = '".$contract['contract_idClient']."'
            AND meter_type = '".$contractType[0]."'
            AND (contract_dateEnd < '".$contract['contract_dateStart']."')
            OR (contract_idContract IS NULL
                AND site_idClient = '".$contract['contract_idClient']."'
                AND meter_type = '".$contractType[0]."'
            )
            GROUP BY meter_numberMain
            ORDER BY contract_idContract, meter_numberMain;
        ";

        $result = $this->getDbTable()
            ->getAdapter()
            ->fetchAssoc($sql);

        $log->info($result);
        $log->info($sql);
        //return $this->fetchAll($select);
    }
*/
    public function save($form)
    {
        if (!$this->checkAcl('save')) {
            throw new ZendSF_Acl_Exception('adding meters is not allowed.');
        }

        return parent::save($form);
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
