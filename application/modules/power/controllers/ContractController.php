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

        // search form
        /*$this->setForm('contractSearch', array(
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
        ));*/

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
        if (!$this->_request->isPost() && !$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'contract');
        }

        $this->_helper->viewRenderer->setNoRender(true);

        if (!$this->getForm('contractSave')->isValid($this->_request->getPost())) {
            $html = $this->view->render('contract/ajax-form.phtml');
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
                $html = $this->view->render('contract/ajax-form.phtml');
                $returnJson['html'] = $html;
            }
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
