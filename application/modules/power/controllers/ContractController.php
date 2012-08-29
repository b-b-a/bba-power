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
                    $data = $this->_model->getCached('contract')
                    	->getContractDataStore($request->getPost());
                    break;
                case 'meter':
                    $data = $this->_model->getMeterContractDataStore($request->getPost());
                    break;
                case 'availableMeters':
                     $data = $this->_model->getCached('meterContract')
                     	->getAvailableMetersDataStore(
                        $request->getParam('meterContract_idContract')
                    );
                    break;
                case 'tender':
                    $data = $this->_model->getCached('tender')
                    	->getTenderDataStore($request->getPost());
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
            'controller'    => 'contract' ,
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

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if ($request->isXmlHttpRequest() && $request->getParam('type') == 'add'
                && $request->isPost()) {

            $form = $this->_getForm('contractSave', 'save-contract');
            $form->populate($request->getPost());
            $docForm = $this->_getForm('docContract', 'save-contract');

            $this->view->assign(array(
                'contractSaveForm'  => $form,
                'docForm'           => $docForm
            ));

            $this->render('contract-form');
        } else {
            return $this->_helper->redirector('index', 'contract');
        }
    }

    public function editContractAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if ($request->getParam('contract_idContract') && $request->isPost()
                && $request->isXmlHttpRequest()) {

            $contract = $this->_model->getContractById($request->getPost('contract_idContract'));

            $this->view->assign('contract', $contract);

            if ($request->getPost('type') == 'edit') {
                if (!$this->_helper->acl('User')) {
                    throw new ZendSF_Acl_Exception('Access Denied');
                }
                $form = $this->_getForm('contractSave', 'save-contract');
                $docForm = $this->_getForm('docContract', 'save-contract');
                $form->populate($contract->toArray('dd/MM/yyyy', true));
                $docForm->populate($contract->toArray('dd/MM/yyyy', true));
                $this->view->assign(array(
                    'contractSaveForm'  => $form,
                    'docForm'           => $docForm
                ));
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

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if (!$request->isPost() && !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'contract');
        }

        try {
            $saved = $this->_model->saveContract($request->getPost());

            $returnJson = array('saved' => $saved);

            if (false === $saved) {
                $form = $this->_getForm('contractSave', 'save-contract');
                $docForm = $this->_getForm('docContract', 'save-contract');
                $form->populate($request->getPost());
                $docForm->populate($request->getPost());

                $this->view->assign(array(
                    'contractSaveForm'  => $form,
                    'docForm'           => $docForm
                ));

                $html = $this->view->render('contract/contract-form.phtml');
                $returnJson['html'] = $html;
            } else {
                $this->view->assign(array(
                    'id'    => $saved,
                    'type'  => 'contract'
                ));

                $html = $this->view->render('confirm.phtml');
                $returnJson['html'] = $html;

                if ($request->getParam('type') === 'add') {
                    $client = $this->_model->getContractById($saved)
                        ->getClient('client_name');
                    $returnJson['client_name'] = $client;
                }

                if ($request->getParam('contract_idContract')) {
                    $returnJson['contract_idContract'] = $request->getParam('contract_idContract');
                }
            }
        } catch (Exception $e) {
            $log = Zend_Registry::get('log');
            $log->err($e);
            $this->view->assign(array(
                'message' => $e
            ));
            $html = $this->view->render('error/error.phtml');
            $returnJson = array(
                'html'  => $html,
                'saved' => false,
                'error' => true
            );
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'text/html')
            ->setBody('<textarea>' . json_encode($returnJson) . '</textarea>');
    }

    public function addMeterContractAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if ($request->getParam('contract_idContract') && $request->isPost()
                && $request->isXmlHttpRequest()) {
            $this->view->assign(array(
                'contract' => $this->_model->getContractById($request->getParam('contract_idContract'))
            ));
        }
    }

    public function saveMeterContractAction()
    {
        $request = $this->getRequest();

        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if (!$request->isPost() && !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'contract');
        }

        try {
            $saved = $this->_model->saveMetersToContract($request->getPost());
            $returnJson = array('saved' => $saved);

            $this->view->assign(array(
                'id'    => $saved,
                'type'  => 'contract'
            ));
            $html = $this->view->render('confirm.phtml');
            $returnJson['html'] = $html;

        } catch (Exception $e) {
            $log = Zend_Registry::get('log');
            $log->err($e);
            $this->view->assign(array(
                'message' => $e
            ));
            $html = $this->view->render('error/error.phtml');
            $returnJson = array(
                'html'  => $html,
                'saved' => false,
                'error' => true
            );
        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }

    public function addTenderAction()
    {
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if ($request->isXmlHttpRequest() && $request->getParam('type') == 'add'
                && $request->isPost()) {
            $form = $this->_getForm('tenderSave', 'save-tender');
            $form->populate($request->getPost());

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

        if ($request->getParam('tender_idTender') && $request->isPost()
                && $request->isXmlHttpRequest()) {
            $tender = $this->_model->getTenderById($request->getParam('tender_idTender'));

            $this->view->assign(array(
                'tender' => $tender
            ));

            if ($request->getParam('type') == 'edit') {
                if (!$this->_helper->acl('User')) {
                    throw new ZendSF_Acl_Exception('Access Denied');
                }
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

        if (!$this->_helper->acl('User')) {
            throw new ZendSF_Acl_Exception('Access Denied');
        }

        if (!$request->isPost() && !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'contract');
        }

        try {
            $saved = $this->_model->saveTender($request->getPost());

            $returnJson = array('saved' => $saved);

            if (false === $saved) {
                $form = $this->_getForm('tenderSave', 'save-tender');
                $form->populate($request->getPost());

                $this->view->assign(array('tenderSaveForm' => $form));

                $html = $this->view->render('contract/tender-form.phtml');
                $returnJson['html'] = $html;
            } else {
                $this->view->assign(array(
                    'id'    => $saved,
                    'type'  => 'tender'
                ));
                $html = $this->view->render('confirm.phtml');
                $returnJson['html'] = $html;
            }
        } catch (Exception $e) {
            $log = Zend_Registry::get('log');
            $log->err($e);
            $this->view->assign(array(
                'message' => $e
            ));
            $html = $this->view->render('error/error.phtml');
            $returnJson = array(
                'html'  => $html,
                'saved' => false,
                'error' => true
            );
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
