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
     * @var Power_Model_Mapper_Usage
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        parent::init();

        if (!$this->_helper->acl('Guest')) {

            $this->_model = new Power_Model_Mapper_Usage();

            $this->setForm('usageSave', array(
                'controller' => 'usage' ,
                'action' => 'save',
                'module' => 'power'
            ));

            $this->_setSearch(array(
                'usage_idMeter'
            ));
        }
    }

    public function usageStoreAction()
    {
        return $this->_getAjaxDataStore('getUsageByMeterId' ,'usage_idUsage', true);
    }

    public function addAction()
    {
        if ($this->_request->getParam('usage_idMeter')
                && $this->_request->isXmlHttpRequest()
                && $this->_request->isPost()) {
            $this->getForm('usageSave')
                ->populate(array('usage_idMeter' => $this->_request->getParam('usage_idMeter')));
            $this->render('ajax-form');
        } else {
            return $this->_helper->redirector('index', 'client');
        }
    }

    public function editAction()
    {
        if ($this->_request->getParam('idUsage')
                && $this->_request->isPost()
                && $this->_request->isXmlHttpRequest()) {
            $usage = $this->_model->find($this->_request->getParam('idUsage'));

            $this->getForm('usageSave')
                ->populate($usage->toArray('dd/MM/yyyy'));

            $this->render('ajax-form');
        } else {
           return $this->_helper->redirector('index', 'client');
        }
    }

    public function saveAction()
    {
        if (!$this->_request->isPost() && !$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'meter');
        }

        $this->_helper->viewRenderer->setNoRender(true);

        if (!$this->getForm('usageSave')->isValid($this->_request->getPost())) {
           $html = $this->view->render('usage/ajax-form.phtml');

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
                $html = $this->view->render('usage/ajax-form.phtml');
                $returnJson['html'] = $html;
            }
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }
}
