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
     * @var Power_Model_Mapper_Abstarct
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
        ));
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $search = array(
            'meter'     => $this->_request->getParam('meter'),
            'client'    => $this->_request->getParam('client')
        );

        // gets all meters and assigns them to the view script.
        $this->view->assign(array(
            'meters' => $this->_model->meterSearch($search, $this->_page),
            'search'    => $search
        ));
    }

    public function usageAction()
    {
        $usageModel = new Power_Model_Mapper_Usage();
        $usage = $usageModel->getUsageByMeterId($this->_request->getParam('meterId'));
        $meter = $this->_model->getMeterDetails($this->_request->getParam('meterId'));

        $this->_log->info($meter);
        $this->view->assign(array(
            'usage' => $usage,
            'meter' => $meter
        ));
    }

    public function addAction()
    {
        $this->setForm('meterAdd', array(
            'controller' => 'meter' ,
            'action' => 'add',
            'module' => 'power'
        ));
    }

    public function editAction()
    {

    }

    public function saveAction()
    {

    }

    public function deleteAction()
    {

    }
}
