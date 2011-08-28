<?php
/**
 * ContractController.php
 *
 * Copyright (c) 2011 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of bba-power.
 *
 * bba-power is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * bba-power is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bba-power.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Controller Class ContractController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_ContractController extends BBA_Controller_Action_Abstract
{
    /**
     * @var Power_Model_Mapper_Contract
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        if ($this->_helper->acl('Guest')) {
            return $this->_forward('login', 'auth');
        }

        parent::init();

        $this->_model = new Power_Model_Mapper_Contract();

        // search form
        $this->setForm('contractSearch', array(
            'controller' => 'contract' ,
            'action' => 'index',
            'module' => 'power'
        ));

        $this->setForm('contractSave', array(
            'controller' => 'contract' ,
            'action' => 'save',
            'module' => 'power'
        ));
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $search = array(
            'contract' => $this->_request->getParam('contract'),
            'meter'    => $this->_request->getParam('meter')
        );

        $contracts = $this->_model->contractSearch($search);

        $dataStore = $this->getDataStore($contracts, 'contract_idContract');

        // gets all contracts and assigns them to the view script.
        $this->view->assign(array(
            'contracts' => $contracts,
            'search'    => $search,
            'store'     => $dataStore
        ));
    }

    public function editAction()
    {
        if ($this->_request->getParam('contractId')) {
            $contract = $this->_model->getContractById($this->_request->getParam('contractId'));
            $meterContract = new Power_Model_Mapper_MeterContract();
            $tenderContract = new Power_Model_Mapper_Tender();

            $this->getForm('contractSave')
                    ->populate($contract->toArray())
                    ->addHiddenElement('returnAction', 'edit');

            $meters = $meterContract->getMetersByContractId(
                $this->_request->getParam('contractId')
            );

            $tenders = $tenderContract->getTendersByContractId(
                $this->_request->getParam('contractId')
            );

            $meterStore = $this->getDataStore($meters, 'meter_idMeter');
            $tenderStore = $this->getDataStore($tenders, 'tender_idTender');

            $this->view->assign(array(
                'contract'      => $contract,
                'previousContract' => $this->_model->getContractById(
                    $contract->idContractPrevious
                ),
                'meterStore'    => $meterStore,
                'tenderStore'   => $tenderStore
            ));
        } else {
           return $this->_helper->redirector('index', 'contract');
        }
    }
}
