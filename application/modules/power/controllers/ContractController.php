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
class Power_ContractController extends Zend_Controller_Action
{
    /**
     * @var Power_Model_Contract
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        $this->_model = new Power_Model_Contract();
    }

    /**
     * Checks if user is logged, if not then forwards to login.
     *
     * @return Zend_Controller_Action::_forward
     */
    public function preDispatch()
    {
        if ($this->_helper->acl('Guest')) {
            return $this->_forward('login', 'auth');
        }
    }

    public function dataStoreAction()
    {
        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            switch ($request->getParam('type')) {
                case 'contract':
                    $data = $this->_model->getContractDataStore($request->getPost());
                    break;
                case 'meter':
                    $data = $this->_model->getMeterContractDataStore($request->getPost());
                    break;
                case 'availableMeters':
                     $data = $this->_model->getAvailableMetersDataStore(
                        $request->getParam('meterContract_idContract')
                    );
                    break;
                case 'tender':
                    $data = $this->_model->getTenderDataStore($request->getPost());
                    break;
                default :
                    $data = '{}';
                    break;
            }

            $this->getResponse()
                ->setHeader('Content-Type', 'application/json')
                ->setBody($data);
        }
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $urlHelper = $this->_helper->getHelper('url');
        $form = $this->_model->getForm('contractSearch');
        $form->populate($this->getRequest()->getPost());

        $form->setAction($urlHelper->url(array(
            'controller'    => 'cntract' ,
            'action'        => 'index',
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');

        // assign search to the view script.
        $this->view->assign(array(
            'search'                => Zend_Json::encode($form->getValues()),
            'contractSearchForm'    => $form
        ));
    }

    public function addContractAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if ($request->isXmlHttpRequest() && $request->getParam('type') == 'add'
                && $request->isPost()) {

            $form = $this->_getForm('contractSave', 'save-contract');

            $this->view->assign(array('contractSaveForm' => $form));

            $this->render('contract-form');
        } else {
            return $this->_helper->redirector('index', 'contract');
        }
    }

    public function editContractAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if ($request->getParam('idContract') && $request->isPost()
                && $request->isXmlHttpRequest()) {

            $contract = $this->_model->getContractById($request->getPost('idContract'));


            $this->view->assign('contract', $contract);

            if ($request->getPost('type') == 'edit') {
                $form = $this->_getForm('contractSave', 'save-contract');
                $form->populate($contract->toArray('dd/MM/yyyy'));
                $this->view->assign('contractSaveForm', $form);
                $this->render('contract-form');
            }

        } else {
           return $this->_helper->redirector('index', 'contract');
        }
    }

    public function saveContractAction()
    {
        $request = $this->getRequest();

        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$request->isPost() && !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'contract');
        }

        $saved = $this->_model->saveContract($request->getPost());

        $returnJson = array('saved' => $saved);

        if (false === $saved) {
            $form = $this->_getForm('contractSave', 'save-meter');
            $form->populate($request->getPost());

            $this->view->assign(array('contractSaveForm' => $form));

            $html = $this->view->render('contract/contract-form.phtml');
            $returnJson['html'] = $html;
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }

    public function addMeterContractAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if ($request->getParam('meterContract_idContract') && $request->isPost()
                && $request->isXmlHttpRequest()) {
            $this->view->assign(array(
                'idContract' => $this->_request->getParam('meterContract_idContract')
            ));
        }
    }

    public function saveMeterContractAction()
    {
        $request = $this->getRequest();

        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$request->isPost() && !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'contract');
        }

        $saved = $this->_model->saveMetersToContract($request->getPost());

        $returnJson = array('saved' => $saved);

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }

    public function addTenderAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if ($request->isXmlHttpRequest() && $request->getParam('type') == 'add'
                && $request->isPost()) {
            $form = $this->_getForm('tenderSave', 'save-tender');
            $form->populate(array(
                'tender_idContract' => $request->getParam('tender_idContract')
            ));

            $this->view->assign(array('tenderSaveForm' => $form));

            $this->render('tender-form');
        } else {
            return $this->_helper->redirector('index', 'contract');
        }
    }

    public function editTenderAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if ($request->getParam('idTender') && $request->isPost()
                && $request->isXmlHttpRequest()) {
            $tender = $this->_model->getTenderById($request->getParam('idTender'));

            $this->view->assign(array(
                'tender' => $tender
            ));

            if ($request->getParam('type') == 'edit') {
                $form = $this->_getForm('tenderSave', 'save-tender');
                $form->populate($tender->toArray('dd/MM/yyyy'));
                $this->view->assign(array('tenderSaveForm' => $form));
                $this->render('tender-form');
            }
        } else {
           return $this->_helper->redirector('index', 'contract');
        }
    }

    public function saveTenderAction()
    {
        $request = $this->getRequest();

        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$request->isPost() && !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'contract');
        }

        $saved = $this->_model->saveTender($request->getPost());

        $returnJson = array('saved' => $saved);

        if (false === $saved) {
            $form = $this->_getForm('tenderSave', 'save-tender');
            $form->populate($request->getPost());

            $this->view->assign(array('tenderSaveForm' => $form));

            $html = $this->view->render('contract/tender-form.phtml');
            $returnJson['html'] = $html;
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }

    private function _getForm($name, $action)
    {
        $urlHelper = $this->_helper->getHelper('url');
        $form = $this->_model->getForm($name);

        $form->setAction($urlHelper->url(array(
            'controller'    => 'contract',
            'action'        => $action,
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');
        return $form;
    }
}
