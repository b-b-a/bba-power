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
        if ($this->_helper->acl('Guest')) {
            return $this->_forward('login', 'auth');
        }

        parent::init();

        $this->_model = new Power_Model_Mapper_Site();

        $this->setForm('siteAdd', array(
            'controller' => 'site' ,
            'action' => 'save',
            'module' => 'power'
        ));

        $this->setForm('siteEdit', array(
            'controller' => 'site' ,
            'action' => 'save',
            'module' => 'power'
        ));

        // search form
        $this->setForm('siteSearch', array(
            'controller' => 'site' ,
            'action' => 'index',
            'module' => 'power'
        ));
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $search = array(
            'client'    => $this->_request->getParam('client')
        );

        // gets all meters and assigns them to the view script.
        $this->view->assign(array(
            'sites'     => $this->_model->siteSearch($search, $this->_page),
            'search'    => $search
        ));
    }

    public function addAction()
    {
        $this->getForm('siteAdd')
                ->addHiddenElement('returnAction', 'add');
    }

    public function editAction()
    {
        if ($this->_request->getParam('siteId')) {
            $site = $this->_model->find($this->_request->getParam('siteId'));
            $meter = new Power_Model_Mapper_Meter();

            $this->getForm('siteEdit')
                    ->populate($site->toArray())
                    ->addHiddenElement('returnAction', 'edit');

            $this->view->assign(array(
                'site' => $site->getId(),
                'meters' => $meter->getMetersBySiteId($site->getId())
            ));
        } else {
           return $this->_helper->redirector('index', 'site');
        }
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
                $result = $model->getAddressByClientId($this->_request->getParam('addressId'));
                break;

            case 'contact':
                $model = new Power_Model_Mapper_ClientContact();
                $identifier = 'clientCo_idClientContact';
                $searchItems = array('idClientContact', 'name');
                 $result = $model->fetchAllById();
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
        echo $data->toJson();


    }

    public function saveAction()
    {
        if (!$this->_request->isPost()) {
            return $this->_helper->redirector('index', 'site');
        }

        $siteId = $this->_request->getParam('siteId');

        if ($this->_request->getParam('cancel')) {
            return $this->_helper->redirector('index', 'site', 'power', array(
                'siteId'  => $siteId
            ));
        }

        $action = $this->_request->getParam('returnAction');
        $form = 'site' . ucfirst($action);

        $this->getForm($form)->addHiddenElement('returnAction', $action);

        $this->_log->info($this->_request->getParams());

        if (!$this->getForm($form)->isValid($this->_request->getPost())) {
            $this->view->assign(array(
                'site'    => $siteId
            ));
            return $this->render($action); // re-render the edit form
        } else {
            $saved = $this->_model->save($form);

            if ($saved > 0) {
                $this->_helper->FlashMessenger(array(
                    'pass' => 'Site saved to database'
                ));

                return $this->_helper->redirector('index', 'site');
            } elseif ($saved == 0) {
                $this->_helper->FlashMessenger(array(
                    'fail' => 'Nothing new to save'
                ));

                return $this->_forward($action);
            }
        }
    }

    public function deleteAction()
    {
        if ($this->_request->getParam('siteId')) {
            $client = $this->_model->delete($this->_request->getParam('siteId'));

            if ($client) {
                $this->_helper->FlashMessenger(array(
                    'pass' => 'Site deleted from database'
                ));
            } else {
                $this->_helper->FlashMessenger(array(
                    'fail' => 'Could not delete site from database'
                ));
            }
        }

        return $this->_helper->redirector('index', 'site');
    }
}
