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
        parent::init();

         if (!$this->_helper->acl('Guest')) {

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

            $this->_setSearch(array(
                'contract', 'meter'
            ));
         }
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $this->getForm('contractSearch')
            ->populate($this->_getSearch());

        $this->view->assign(array(
            'search'    => $this->_getSearchString('contractSearch')
        ));
    }

    public function contractStoreAction()
    {
        return $this->_getAjaxDataStore('getList', 'contract_idContract');
    }

    public function addAction()
    {
        if ($this->_request->isXmlHttpRequest()
                && $this->_request->getParam('type') == 'add'
                && $this->_request->isPost()) {

            $this->render('add-form');
        } else {
            return $this->_helper->redirector('index', 'contract');
        }
    }

    public function editAction()
    {
        if ($this->_request->getParam('idContract')
                && $this->_request->isPost()
                && $this->_request->isXmlHttpRequest()) {
            $contract = $this->_model->getContractById($this->_request->getParam('idContract'));

            $this->getForm('contractSave')
                ->populate($contract->toArray('dd/MM/yyyy'));

            $this->_log->info($contract);

            $this->view->assign(array(
                'contract'      => $contract,
                'previousContract' => $this->_model->getContractById(
                    $contract->idContractPrevious
                )
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

        if ($this->_request->getParam('type') == 'edit') {
            $render = 'ajax-form.phtml';
            //$form = 'clientSave';
        } else {
            $render = 'add-form.phtml';
           // $form = 'clientAdd';
        }

        if (!$this->getForm('contractSave')->isValid($this->_request->getPost())) {
            $html = $this->view->render('contract/' . $render);
            $this->_log->info('not saved');

            $returnJson = array(
                'saved' => 0,
                'html'  => $html
            );
        } else {
            $saved = $this->_model->save('contractSave');

            $returnJson = array(
                'saved' => $saved
            );

            if ($saved == 0) {
                $html = $this->view->render('contract/' . $render);
                $returnJson['html'] = $html;
            }
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }
}
