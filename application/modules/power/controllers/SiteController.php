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
class Power_SiteController extends BBA_Controller_Action_Abstract
{
    /**
     * Initialization code.
     */
    public function init()
    {
        parent::init();

        if (!$this->_helper->acl('Guest')) {

            $this->_model = new Power_Model_Mapper_Site();

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
            ));
        }
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $this->getForm('siteSearch')
            ->populate($this->_getSearch());

        $this->view->assign(array(
            'search' => $this->_getSearchString('siteSearch')
        ));
    }

    public function addAction()
    {
        if ($this->_request->isXmlHttpRequest()
                && $this->_request->getParam('type') == 'add'
                && $this->_request->isPost()) {
            $this->getForm('siteAdd');
            $this->view->assign(array(
                'formName' => 'siteAddForm'
            ));
            $this->render('ajax-form');
        } else {
            return $this->_helper->redirector('index', 'client');
        }
    }

    public function editAction()
    {
        if ($this->_request->getParam('idSite')
                && $this->_request->isPost()
                && $this->_request->isXmlHttpRequest()) {
            $site = $this->_model->getSiteDetails($this->_request->getParam('idSite'));

            $this->getForm('siteEdit')
                ->populate($site->toArray('dd/MM/yyyy'));

            $this->view->assign(array(
                'formName'  => 'siteEditForm',
                'site'      => $site
            ));

            if ($this->_request->getParam('type') == 'edit') {
                $this->render('ajax-form');
            }
        } else {
           return $this->_helper->redirector('index', 'site');
        }
    }

    public function siteStoreAction()
    {
        return $this->_getAjaxDataStore('getList' ,'site_idSite');
    }

    public function autocompleteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(true);

        switch ($this->_request->getParam('param')) {
            case 'client':
                $model = new Power_Model_Mapper_Client();
                $identifier = 'client_idClient';
                $searchItems = array('idClient', 'name');
                $result = $model->fetchAll();
                break;

            case 'address':
                $model = new Power_Model_Mapper_ClientAddress();
                $identifier = 'clientAd_idAddress';
                $searchItems = array('idAddress', 'address1AndPostcode');
                $result = $model->getAddressByClientId(array(
                   'clientAd_idClient' => $this->_request->getParam('clientId')
                ), 'clientAd_postcode');
                break;

            case 'contact':
                $model = new Power_Model_Mapper_ClientContact();
                $identifier = 'clientCo_idClientContact';
                $searchItems = array('idClientContact', 'name');
                $result = $model->getContactByClientId(array(
                   'clientCo_idClient' => $this->_request->getParam('clientId')
                ), 'clientCo_name');
                break;
        }

        $items = array();

        foreach ($result as $row) {
            //$row = $row->toArray();
            $items[] = array(
                $identifier                     => $row->{$searchItems[0]},
                $row->prefix . $searchItems[1]  => $row->{$searchItems[1]}
            );
        }

        $data = new Zend_Dojo_Data($identifier, $items);

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody($data->toJson());
    }

    public function saveAction()
    {
        if (!$this->_request->isPost() && !$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'site');
        }

        $this->_helper->viewRenderer->setNoRender(true);

        $action = $this->_request->getParam('type');
        $form = 'site' . ucfirst($action);

        $this->view->assign(array(
            'formName' => 'site' . ucfirst($action) . 'Form'
        ));

        if (!$this->getForm($form)->isValid($this->_request->getPost())) {
            $html = $this->view->render('site/ajax-form.phtml');

            $returnJson = array(
                'saved' => 0,
                'html'  => $html
            );
        } else {
            $saved = $this->_model->save($form);

            $returnJson = array(
                'saved' => $saved
            );

            if ($saved == 0) {
                $html = $this->view->render('site/ajax-form.phtml');
                $returnJson['html'] = $html;
            }
        }

         $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($returnJson));
    }
}
