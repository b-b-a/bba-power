<?php
/**
 * TenderController.php
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
 * @category   bba-power
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Controller Class TenderController.
 *
 * @category   bba-power
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_TenderController extends BBA_Controller_Action_Abstract
{
    /**
     * @var Power_Model_Mapper_Tender
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        parent::init();

        if (!$this->_helper->acl('Guest')) {

            $this->_model = new Power_Model_Mapper_Tender();

            $this->setForm('tenderSave', array(
                'controller' => 'tender' ,
                'action' => 'save',
                'module' => 'power'
            ));

            $this->_setSearch(array(
                'tender_idTender', 'tender_idContract'
            ));
        }
    }

    public function tenderStoreAction()
    {
        return $this->_getAjaxDataStore('getTendersByContractId', 'tender_idTender', true);
    }

    public function addAction()
    {
        if ($this->_request->isXmlHttpRequest()
                && $this->_request->getParam('type') == 'add'
                && $this->_request->isPost()) {
            $this->getForm('tenderSave')
                ->populate(array(
                    'tender_idContract' => $this->_request->getParam('tender_idContract')
                ));

            $this->render('ajax-form');
        } else {
            return $this->_helper->redirector('index', 'contract');
        }
    }

    public function editAction()
    {
        if ($this->_request->getParam('idTender')
                && $this->_request->isPost()
                && $this->_request->isXmlHttpRequest()) {
            $tender = $this->_model->getTenderDetails($this->_request->getParam('idTender'));

            $this->getForm('tenderSave')
                ->populate($tender->toArray('dd/MM/yyyy'));

            $this->view->assign(array(
                'tender' => $tender
            ));

            if ($this->_request->getParam('type') == 'edit') {
                $this->render('ajax-form');
            }
        } else {
           return $this->_helper->redirector('index', 'contract');
        }
    }

    public function saveAction()
    {
        if (!$this->_request->isPost() && !$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'contract');
        }

        $this->_helper->viewRenderer->setNoRender(true);

        if (!$this->getForm('tenderSave')->isValid($this->_request->getPost())) {
            $html = $this->view->render('tender/ajax-form.phtml');
            $this->_log->info('not saved');

            $returnJson = array(
                'saved' => 0,
                'html'  => $html
            );
        } else {
            $saved = $this->_model->save('tenderSave');

            $returnJson = array(
                'saved' => $saved
            );

            if ($saved == 0) {
                $html = $this->view->render('tender/ajax-form.phtml');
                $returnJson['html'] = $html;
            }
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }

}
