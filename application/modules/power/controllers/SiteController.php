<?php
/**
 * SiteController.php
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
 * Controller Class SiteController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_SiteController extends Zend_Controller_Action
{
    /**
     * @var Power_Model_Site
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        $this->_model = new Power_Model_Site();
    }

    /**
     * Checks if user is logged, if not then forwards to login.
     *
     * @return Zend_Controller_Action::_forward
     */
    public function preDispatch()
    {
        if (!$this->_helper->acl('Site', 'view')) {
            throw new Zend_Acl_Exception('Access Denied');
        }
    }

    public function dataStoreAction()
    {
        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            switch ($request->getParam('type')) {
                case 'site':
                    $data = $this->_model
                        //->getCached('site')
                    	->getSiteDataStore($request->getPost());
                    break;
                case 'siteMeters':
                    $data = $this->_model
                        //->getCached('meter')
                    	->getSiteMetersDataStore($request->getPost());
                    break;
                case 'clients':
                	$data = $this->_model
                	    //->getCached('client')
                		->getFileringSelectData($request->getParams());
                	break;
                case 'address':
                case 'billAddress':
                	$data = $this->_model
                	    //->getCached('clientAddress')
                		->getFileringSelectData($request->getParams());
                	break;
                case 'personnel':
                    $data = $this->_model
                        //->getCached('clientPersonnel')
                    	->getFileringSelectData($request->getParams());
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
        if (!$this->_helper->acl('Site', 'view')) {
            throw new Zend_Acl_Exception('Access Denied');
        }
        
        $urlHelper = $this->_helper->getHelper('url');
        $form = $this->_model->getForm('siteSearch');
        $form->populate($this->getRequest()->getPost());

        $form->setAction($urlHelper->url(array(
            'controller'    => 'site' ,
            'action'        => 'index',
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');

        // assign search to the view script.
        $this->view->assign(array(
            'search'            => Zend_Json::encode($form->getValues()),
            'siteSearchForm'   => $form
        ));
    }

    public function addSiteAction()
    {
        if (!$this->_helper->acl('Site', 'add')) {
            throw new Zend_Acl_Exception('Access Denied');
        }
        
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if ($request->isXmlHttpRequest() && $request->getParam('type') == 'add'
                && $request->isPost()) {

            $form = $this->_getForm('siteAdd', 'save-site');

            $this->view->assign(array(
                'formName'      => 'siteAddForm',
                'siteAddForm'   => $form
            ));

            $this->render('site-form');
        } else {
            return $this->_helper->redirector('index', 'site');
        }
    }

    public function editSiteAction()
    {
        if (!$this->_helper->acl('Site', 'view')) {
            throw new Zend_Acl_Exception('Access Denied');
        }
        
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if ($request->getParam('site_idSite') && $request->isPost()
                && $request->isXmlHttpRequest()) {

            $site = $this->_model->getSiteById($request->getPost('site_idSite'));

            $form = $this->_getForm('siteEdit', 'save-site');
            $form->populate($site->toArray('dd/MM/yyyy', true));

            $this->view->assign(array(
                'site'          => $site,
                'formName'      => 'siteEditForm',
                'siteEditForm'  => $form
            ));

            if ($request->getPost('type') == 'edit') {
                if (!$this->_helper->acl('Site', 'edit')) {
                    throw new Zend_Acl_Exception('Access Denied');
                }
                $this->render('site-form');
            }
        } else {
           return $this->_helper->redirector('index', 'site');
        }
    }

    public function saveSiteAction()
    {
        $request = $this->getRequest();

        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();

        if (!$request->isPost() && !$request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'site');
        }

        $type = ($request->getPost('type') == 'add') ? 'Add' : 'Edit';
        $formName = 'site' . $type . 'Form';

        try {
            $saved = $this->_model->saveSite($request->getPost(), 'site' . $type);

            $returnJson = array('saved' => $saved);

            if (false === $saved) {

                $form = $this->_getForm('site' . $type, 'save-site');
                $form->populate($request->getPost());

                $this->view->assign(array(
                    'formName'  => $formName,
                    'site' . $type . 'Form'  => $form
                ));

                $html = $this->view->render('site/site-form.phtml');
                $returnJson['html'] = $html;
            } else {
                $this->view->assign(array(
                    'id'    => $saved,
                    'type'  => 'site'
                ));
                $html = $this->view->render('confirm.phtml');
                $returnJson['html'] = $html;

                if ($request->getParam('type') === 'add') {
                    $client = $this->_model->getSiteById($saved)
                        ->getSiteAddress();
                    $returnJson['clientAd_addressName'] = $client->clientAd_addressName;
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
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }

    private function _getForm($name, $action)
    {
        $urlHelper = $this->_helper->getHelper('url');
        $form = $this->_model->getForm($name);

        $form->setAction($urlHelper->url(array(
            'controller'    => 'site',
            'action'        => $action,
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');
        return $form;
    }
}
