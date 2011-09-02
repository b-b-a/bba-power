<?php
/**
 * UsageController.php
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
 * Controller Class UsageController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_UsageController extends BBA_Controller_Action_Abstract
{
    /**
     * @var Power_Model_mapper_Usage
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        parent::init();

        $this->_model = new Power_Model_Mapper_Usage();

        $this->setForm('usageSave', array(
            'controller' => 'usage' ,
            'action' => 'save',
            'module' => 'power'
        ));
    }

    public function addAction()
    {
        $meterModel = new Power_Model_Mapper_Meter();
        $meter = $meterModel->getMeterDetails($this->_request->getParam('meterId'));

        $usage = $this->_model->getUsageByMeterId($this->_request->getParam('meterId'));

        $usageStore = $this->getDataStore($usage, 'usage_idUsage');

        $this->getForm('usageSave')
                ->populate(array(
                    'usage_idMeter' => $meter->id
                ))
                ->addHiddenElement('returnAction', 'add');

        $this->view->assign(array(
            'usageStore'    => $usageStore,
            'meter'         => $meter
        ));

        $this->render('save');
    }

    public function editAction()
    {
        $meterUsage = $this->_getUsageDetails();
        
        $this->getForm('usageSave')
            ->populate($meterUsage->toArray())
            ->addHiddenElement('returnAction', 'edit');

        $this->render('save');
    }

    public function saveAction()
    {
        if (!$this->_request->isPost()) {
            return $this->_helper->redirector('index', 'meter');
        }

        /* @var $meterId Power_Form_Usage_Save */
        $meterId = $this->_request->getPost('usage_idMeter');


        if ($this->_request->getParam('cancel')) {
            return $this->_helper->redirector('index', 'meter', 'power');
        }

        $action = $this->_request->getParam('returnAction');

        $this->getForm('usageSave')->addHiddenElement('returnAction', $action);


        if (!$this->getForm('usageSave')->isValid($this->_request->getPost())) {
            $this->_getUsageDetails();
            return $this->render('save'); // re-render the edit form
        } else {
            $saved = $this->_model->save();

            if ($saved > 0) {
                $this->_helper->FlashMessenger(array(
                    'pass' => 'Meter usage saved to database'
                ));

                return $this->_helper->redirector('edit', 'meter', 'power', array(
                    'meterId'   => $meterId
                ));
            } elseif ($saved == 0) {
                $this->_helper->FlashMessenger(array(
                    'fail' => 'Nothing new to save'
                ));

                return $this->_forward($action);
            }
        }
    }
    
    protected function _getUsageDetails()
    {
        $meterModel = new Power_Model_Mapper_Meter();
        $meterUsage = $this->_model->find($this->_request->getParam('usageId'));
        $usage = $this->_model->getUsageByMeterId($meterUsage->idMeter);

        $meter = $meterModel->getMeterDetails($meterUsage->idMeter);

        $usageStore = $this->getDataStore($usage, 'usage_idUsage');

        $this->view->assign(array(
            'usageStore'    => $usageStore,
            'usage'         => $meterUsage,
            'meter'         => $meter
        ));
        
        return $meterUsage;
    }

}
