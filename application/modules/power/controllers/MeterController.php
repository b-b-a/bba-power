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

        if (!$this->_helper->acl('Guest')) {

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
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $this->getForm('meterSearch')
            ->populate($this->_getSearch());

        $this->view->assign(array(
            'search' => $this->_getSearchString('meterSearch')
        ));
    }

    public function meterStoreAction()
    {
        return $this->_getAjaxDataStore('getList' ,'meter_idMeter');
    }

    public function siteMeterStoreAction()
    {
        unset($this->_search);
        $this->_setSearch(array('meter_idSite'));

        return $this->_getAjaxDataStore('getMetersBySiteId' ,'meter_idMeter', true);
    }

    public function meterContractStoreAction()
    {
        unset($this->_search);
        $this->_setSearch(array('meterContract_idContract'));

        return $this->_getAjaxDataStore('getMetersByContractId' ,'meter_idMeter', true);
    }

    public function addAction()
    {
        if ($this->_request->isXmlHttpRequest()
                && $this->_request->getParam('type') == 'add'
                && $this->_request->isPost()) {
            $this->getForm('meterSave')
                ->populate(array('meter_idSite' => $this->_request->getParam('meter_idSite')));
            $this->render('ajax-form');
        } else {
            return $this->_helper->redirector('index', 'client');
        }
    }

    public function editAction()
    {
        if ($this->_request->getParam('idMeter')
                && $this->_request->isPost()
                && $this->_request->isXmlHttpRequest()) {
            $meter = $this->_model->getMeterDetails($this->_request->getParam('idMeter'));

            $this->getForm('meterSave')
                ->populate($meter->toArray('dd/MM/yyyy'));

            $this->view->assign(array(
                'meter' => $meter
            ));

            if ($this->_request->getParam('type') == 'edit') {
                $this->render('ajax-form');
            }
        } else {
           return $this->_helper->redirector('index', 'site');
        }
    }

    public function saveAction()
    {
        if (!$this->_request->isPost() && !$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'meter');
        }

        $this->_helper->viewRenderer->setNoRender(true);

        if (!$this->getForm('meterSave')->isValid($this->_request->getPost())) {
            $html = $this->view->render('meter/ajax-form.phtml');

            $returnJson = array(
                'saved' => 0,
                'html'  => $html
            );
        } else {
            $saved = $this->_model->save();

            $returnJson = array(
                'saved' => $saved
            );

            if ($saved == 0) {
                $html = $this->view->render('meter/ajax-form.phtml');
                $returnJson['html'] = $html;
            }
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }
}
