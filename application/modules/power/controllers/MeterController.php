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
     * Initialization code.
     */
    public function init()
    {
        parent::init();

        $this->_model = new Power_Model_Mapper_Meter();

        // search form
        $this->setForm('meterSearch', array(
            'controller' => 'meter' ,
            'action' => 'search',
            'module' => 'power'
        ));
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $this->_forward('list');
    }

    public function listAction()
    {
        // gets all meters and assigns them to the view script.
        $this->view->assign(array(
            'meters' => $this->_model->listMeters()
        ));
    }

    public function searchAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);

        if (!$this->_request->isPost()) {
            return $this->_forward('list');
        }

        if (!$this->getForm('meterSearch')->isValid($this->_request->getPost())) {
            return $this->render('list'); // re-render the search form
        }

        $this->view->assign(array(
            'meters' => $this->_model->meterSearch()
        ));

        return $this->render('list');
    }

    public function addAction()
    {
        $this->setForm('meterAdd', array(
            'controller' => 'meter' ,
            'action' => 'add-new',
            'module' => 'power'
        ));
    }

}
