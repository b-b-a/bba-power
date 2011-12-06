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
        /*
        $this->setForm('siteAdd', array(
            'controller'    => 'site' ,
            'action'        => 'save',
            'module'        => 'power'
        ));

        $this->setForm('siteEdit', array(
            'controller'    => 'site' ,
            'action'        => 'save',
            'module'        => 'power'
        ));

        // search form
        $this->setForm('siteSearch', array(
            'controller'    => 'site' ,
            'action'        => 'index',
            'module'        => 'power'
        ));

        $this->_setSearch(array(
            'site', 'client', 'meter_idSite'
        ));*/
    }

    public function dataStoreAction()
    {
        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            switch ($request->getParam('type')) {
                case 'site':
                    $data = $this->_model->getSiteDataStore($request->getPost());
                    break;
                case 'siteMeters':
                    $data = $this->_model->getSiteMetersDataStore($request->getPost());
                    break;
                case 'clients':
                case 'address':
                case 'contact':
                    $data = $this->_model->getFileringSelectData($request->getParams());
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
        $request = $this->getRequest();
        $this->_helper->layout->disableLayout();

        if ($request->getParam('idSite') && $request->isPost()
                && $request->isXmlHttpRequest()) {

            $site = $this->_model->getSiteDetailsById($request->getPost('idSite'));

            $form = $this->_getForm('siteEdit', 'save-site');
            $form->populate($site->toArray());

            $this->view->assign(array(
                'site'          => $site,
                'formName'      => 'siteEditForm',
                'siteEditForm'  => $form
            ));

            if ($request->getPost('type') == 'edit') {
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

        $saved = $this->_model->saveSite($request->getPost(), 'site' . $type);

        $returnJson = array('saved' => $saved);

        if (false === $saved) {

            $form = $this->_getForm('site' . $type, 'save-site');
            $form->populate($request->getPost());

            $this->view->assign(array(
                'formName'  => $formName,
                $fromName   => $form
            ));
            $html = $this->view->render('site/site-form.phtml');
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
            'controller'    => 'site',
            'action'        => $action,
            'module'        => 'power'
        ), 'default'));

        $form->setMethod('post');
        return $form;
    }
}
