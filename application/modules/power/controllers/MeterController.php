<?php
/**
 * MeterController.php
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
 * Controller Class MeterController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_MeterController extends BBA_Controller_Action_Abstract
{
    /**
     * @var Power_Model_Mapper_Meter
     */
    protected $_model;

    /**
     * @var int
     */
    protected $_page;

    /**
     * Initialization code.
     */
    public function init()
    {
        if ($this->_helper->acl('Guest')) {
            return $this->_forward('login', 'auth');
        }

        parent::init();

        $this->_model = new Power_Model_Mapper_Meter();

        // search form
        $this->setForm('meterSearch', array(
            'controller' => 'meter' ,
            'action' => 'index',
            'module' => 'power'
        ), true);

        $this->setForm('meterSave', array(
            'controller' => 'meter' ,
            'action' => 'save',
            'module' => 'power'
        ));

        $this->_setSearch(array(
            'meter', 'site'
        ));
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $this->getForm('meterSearch')
            ->populate($this->_getSearch());

        $this->view->assign(array(
            'search'    => $this->_getSearchString()
        ));
    }

    public function meterStoreAction()
    {
        return $this->_getAjaxDataStore('getList' ,'meter_idMeter');
    }

    public function addAction()
    {
        if ($this->_request->getParam('siteId')) {
            $this->getForm('meterSave')
                ->populate(array(
                    'meter_idSite' => $this->_request->getParam('siteId')
                ))
                ->addHiddenElement('returnAction', 'add');
        } else {
            return $this->_helper->redirector('index', 'meter', 'power');
        }

    }

    public function editAction()
    {
        if ($this->_request->getParam('idMeter')) {
            $usageModel = new Power_Model_Mapper_Usage();
            $meter = $this->_model->getMeterDetails($this->_request->getParam('idMeter'));
            $usage = $usageModel->getUsageByMeterId($this->_request->getParam('idMeter'));

            $usageStore = $this->getDataStore($usage, 'usage_idUsage');

            $this->getForm('meterSave')
                ->populate($meter->toArray('dd/MM/yyyy'))
                ->addHiddenElement('returnAction', 'edit');

            $this->view->assign(array(
                'usageStore' => $usageStore,
                'meter' => $meter
            ));

        } else {
           return $this->_helper->redirector('index', 'meter');
        }
    }

    public function saveAction()
    {
        if (!$this->_request->isPost()) {
            return $this->_helper->redirector('index', 'meter');
        }

        $meterId = $this->_request->getParam('meterId');

        if ($this->_request->getParam('cancel')) {
            $search = array();
            if ($this->_request->getParam('meter')) {
                $search['meter'] = $this->_request->getParam('meter');
            }

            if ($this->_request->getParam('site')) {
                $search['site'] = $this->_request->getParam('site');
            }

            return $this->_helper->redirector('index', 'meter', 'power', $search);
        }

        $action = $this->_request->getParam('returnAction');

        $this->getForm('meterSave')->addHiddenElement('returnAction', $action);


        if (!$this->getForm('meterSave')->isValid($this->_request->getPost())) {
            $this->view->assign(array(
                'meter'    => $meterId
            ));
            return $this->render($action); // re-render the edit form
        } else {
            $saved = $this->_model->save();

            if ($saved > 0) {
                $this->_helper->FlashMessenger(array(
                    'pass' => 'Meter saved to database'
                ));

                return $this->_helper->redirector('index', 'meter');
            } elseif ($saved == 0) {
                $this->_helper->FlashMessenger(array(
                    'fail' => 'Nothing new to save'
                ));

                return $this->_forward($action);
            }
        }
    }

}
