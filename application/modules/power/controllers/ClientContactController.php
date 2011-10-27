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
        if ($this->_helper->acl('Guest')) {
            return $this->_forward('login', 'auth');
        }

        parent::init();

        $this->_model = new Power_Model_Mapper_ClientContact();

        $this->setForm('clientContactSave', array(
            'controller' => 'client-contact' ,
            'action' => 'save',
            'module' => 'power'
        ));

         $this->_setSearch(array(
            'clientCo_idClient'
        ));
    }

    public function clientContactStoreAction()
    {
        $this->_log->info($this->_request->getPost());
        return $this->_getAjaxDataStore('getContactByClientId' ,'clientCo_idClientContact', true);
    }

    public function addAction()
    {
        if ($this->_request->getParam('clientCo_idClient')
                && $this->_request->isXmlHttpRequest()
                && $this->_request->isPost()) {
            $this->getForm('clientContactSave')
                ->populate(array(
                    'clientCo_idClient' => $this->_request->getParam('clientCo_idClient')
                )
            );

            $this->render('ajax-form');
        }
    }

    public function editAction()
    {
        if ($this->_request->getParam('idClientContact')
                && $this->_request->isXmlHttpRequest()
                && $this->_request->isPost()) {
            $clientCo = $this->_model->find($this->_request->getParam('idClientContact'));

            $this->getForm('clientContactSave')
                ->populate($clientCo->toArray('dd/MM/yyyy'));

            $this->render('ajax-form');

        } else {
           return $this->_helper->redirector('index', 'client');
        }
    }

    public function saveAction()
    {
         if (!$this->_request->isPost() && !$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'client');
        }

        $this->_helper->viewRenderer->setNoRender(true);

        $clientId = $this->_request->getParam('clientId');

        if ($this->_request->getParam('type') == 'edit') {
            $this->getForm('clientContactSave')
                    ->excludeEmailFromValidation('clientCo_email', array(
                        'field' => 'clientCo_email',
                        'value' => $this->_model
                            ->find($this->_request->getParam('clientCo_contactId'))
                            ->email
                    ));
        }

        if (!$this->getForm('clientContactSave')->isValid($this->_request->getPost())) {
             $html = $this->view->render('client-contact/ajax-form.phtml');

            echo json_encode(array(
                'saved' => 0,
                'html'  => $html
            ));
        } else {
             $saved = $this->_model->save();

            echo json_encode(array(
                'saved' => $saved
            ));
        }
    }

}
