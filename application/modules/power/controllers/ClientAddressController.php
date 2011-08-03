<?php
/**
 * ClientAddressController.php
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
 * Controller Class ClientAddressController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_ClientAddressController extends BBA_Controller_Action_Abstract
{
    /**
     * @var Power_Model_Mapper_ClientAddress
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        parent::init();

        $this->_model = new Power_Model_Mapper_ClientAddress();

        $this->setForm('clientAddressSave', array(
            'controller' => 'client-address' ,
            'action' => 'save',
            'module' => 'power'
        ));
    }

    public function addAction()
    {
        $this->getForm('clientAddressSave')
                ->populate(array(
                    'clientAd_idClient' => $this->_request->getParam('clientId')
                ))
                ->addHiddenElement('returnAction', 'add');

        $this->view->assign(array(
            'client'    => $this->_request->getParam('clientId')
        ));
    }

    public function editAction()
    {
        if ($this->_request->getParam('addressId')) {
            $clientAd = $this->_model->find($this->_request->getParam('addressId'));
            $this->getForm('clientAddressSave')
                    ->populate($clientAd->toArray('dd/MM/yyyy'))
                    ->addHiddenElement('returnAction', 'edit');

            $this->view->assign(array(
                'idAddress' => $clientAd->getId(),
                'client'    => $clientAd->idClient
            ));
        } else {
           return $this->_helper->redirector('index', 'client');
        }
    }

    public function saveAction()
    {
        if (!$this->_request->isPost()) {
            return $this->_helper->redirector('index', 'client');
        }

        if ($this->_request->getParam('cancel')) {
            return $this->_helper->redirector('edit', 'client', 'power', array(
                'clientId'  => $this->_request->getParam('clientAd_idClient')
            ));
        }

        $action = $this->_request->getParam('returnAction');

        $this->getForm('clientAddressSave')->addHiddenElement('returnAction', $action);

        if (!$this->getForm('clientAddressSave')->isValid($this->_request->getPost())) {
            return $this->render($action); // re-render the edit form
        } else {
            $saved = $this->_model->save();

            $this->_log->info($saved);

            if ($saved) {
                $this->_helper->FlashMessenger(array(
                    'pass' => 'Client Address saved to database'
                ));

                return $this->_helper->redirector('edit', 'client', 'power', array(
                    'clientId'  => $this->_request->getParam('clientAd_idClient')
                ));
            } else {
                $this->_helper->FlashMessenger(array(
                    'fail' => 'Nothing new to save'
                ));

                return $this->_forward($action);
            }
        }
    }

    public function deleteAction()
    {
        if ($this->_request->getParam('addressId')) {
            $client = $this->_model->delete($this->_request->getParam('addressId'));

            $this->_log->info($client);

            if ($client) {
                $this->_helper->FlashMessenger(array(
                    'pass' => 'Client Address deleted from database.'
                ));
            } else {
                $this->_helper->FlashMessenger(array(
                    'fail' => 'Could not delete client address from database.'
                ));
            }
        }

        return $this->_helper->redirector('edit', 'client', 'power', array(
            'clientId'  => $this->_request->getParam('clientId')
        ));
    }

}
