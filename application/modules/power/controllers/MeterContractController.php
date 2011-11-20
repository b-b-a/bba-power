<?php
/**
 * MeterContractController.php
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
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Controller Class MeterContractController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_MeterContractController extends BBA_Controller_Action_Abstract
{
    /**
     * @var Power_Model_Mappper_MeterContract
     */
    protected $_model;
    /**
     * Initialization code.
     */
    public function init()
    {
        parent::init();

        if (!$this->_helper->acl('Guest')) {
            $this->_model = new Power_Model_Mapper_MeterContract();

            $this->_setSearch(array('meterContract_idContract'));
        }
    }

    public function meterContractStoreAction()
    {
        return $this->_getAjaxDataStore('getMetersByContractId' ,'meterContract_idMeter', true);
    }

    public function storeAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        $meters = $this->_model->getAvailClientMeters(
            $this->_request->getParam('meterContract_idContract')
        );

        $store = $this->getDataStore($meters, 'meter_idMeter');

        /*$store->setMetadata(
            'numRows',
            count($store)
        );*/

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody($store->toJson());
    }

    public function addAction()
    {
        $this->view->assign(array(
            'idContract' => $this->_request->getParam('meterContract_idContract')
        ));
    }

    public function saveAction()
    {

    }
}
