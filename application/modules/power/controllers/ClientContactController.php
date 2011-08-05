<?php
/**
 * ClientContactController.php
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
 * Controller Class ClientContactController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_ClientContactController extends BBA_Controller_Action_Abstract
{
    /**
     * Initialization code.
     */
    public function init()
    {
        parent::init();

        $this->_model = new Power_Model_Mapper_ClientContact();

        $this->setForm('clientContactSave', array(
            'controller' => 'client-contact' ,
            'action' => 'save',
            'module' => 'power'
        ));
    }

    public function addAction()
    {
        $this->getForm('clientContactSave')
                ->populate(array(
                    'clientCo_idClient' => $this->_request->getParam('clientId')
                ))
                ->addHiddenElement('returnAction', 'add');

        $this->view->assign(array(
            'client'    => $this->_request->getParam('clientId')
        ));
    }

    public function editAction()
    {
        if ($this->_request->getParam('addressId')) {
            $clientCo = $this->_model->find($this->_request->getParam('contactId'));
            $this->getForm('clientContactSave')
                    ->populate($clientCo->toArray('dd/MM/yyyy'))
                    ->addHiddenElement('returnAction', 'edit')
                    ;

            $this->view->assign(array(
                'idClientContact' => $clientCo->getId(),
                'client'    => $clientCo->idClient
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

        $clientId = $this->_request->getParam('clientId');

        if ($this->_request->getParam('cancel')) {
            return $this->_helper->redirector('edit', 'client', 'power', array(
                'clientId'  => $clientId
            ));
        }

        $action = $this->_request->getParam('returnAction');

        if ($action == 'edit') {
            $this->getForm('clientContactSave')
                    ->excludeEmailFromValidation('clientCo_email', array(
                        'field' => 'clientCo_email',
                        'value' => $this->_model
                                    ->find($this->_request->getParam('contactId'))
                                    ->email
                    ))
                  ->addHiddenElement('returnAction', $action);
        }

        if (!$this->getForm('clientContactSave')->isValid($this->_request->getPost())) {
            $this->view->assign(array(
                'client'    => $clientId
            ));
            return $this->render($action); // re-render the edit form
        } else {
            $this->_log->info($this->_request->getParams());
            $saved = $this->_model->save();

            if ($saved) {
                $this->_helper->FlashMessenger(array(
                    'pass' => 'Client Contact saved to database'
                ));

                return $this->_helper->redirector('edit', 'client', 'power', array(
                    'clientId'  => $clientId
                ));
            } else {
                $this->_helper->FlashMessenger(array(
                    'fail' => 'Nothing new to save'
                ));

                return $this->_forward($action);
            }
        }
    }

}
