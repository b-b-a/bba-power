<?php
/**
 * SupplierContactController.php
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
 * Controller Class SupplierContactController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_SupplierContactController extends BBA_Controller_Action_Abstract
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

        $this->_model = new Power_Model_Mapper_SupplierContact();

        $this->setForm('supplierContactSave', array(
            'controller' => 'supplier-address' ,
            'action' => 'save',
            'module' => 'power'
        ));
    }

    public function addAction()
    {
        $this->getForm('supplierContactSave')
            ->populate(array(
                'supplierCo_idSupplierContact' => $this->_request->getParam('supplierId')
            ))
            ->addHiddenElement('returnAction', 'add');

        $this->view->assign(array(
            'supplierId'    => $this->_request->getParam('supplierId')
        ));
    }

    public function editAction()
    {
        if ($this->_request->getParam('contactId')) {
            $supplierCo = $this->_model->find($this->_request->getParam('contactId'));
            $this->getForm('supplierContactSave')
                ->populate($supplierCo->toArray())
                ->addHiddenElement('returnAction', 'edit');

            $contacts = $this->_model->getContactsBySupplierId($clientCo->idSupplier);
            $contactsStore = $this->getDataStore($contacts, 'supplierCo_idSupplierContact');

            $this->view->assign(array(
                'contactStore' => $contactStore,
                'supplierCo'    => $supplierCo
            ));
        } else {
           return $this->_helper->redirector('index', 'supplier');
        }
    }

    public function saveAction()
    {
        if (!$this->_request->isPost()) {
            return $this->_helper->redirector('index', 'supplier');
        }

        $supplierId = $this->_request->getParam('supplierId');

        if ($this->_request->getParam('cancel')) {
            return $this->_helper->redirector('edit', 'supplier', 'power', array(
                'supplierId'  => $supplierId
            ));
        }

        $action = $this->_request->getParam('returnAction');

        $this->getForm('supplierContactSave')->addHiddenElement('returnAction', $action);

        if (!$this->getForm('supplierContactSave')->isValid($this->_request->getPost())) {
            $this->view->assign(array(
                'supplierId'    => $supplierId
            ));
            return $this->render($action); // re-render the edit form
        } else {
            $saved = $this->_model->save();

            if ($saved) {
                $this->_helper->FlashMessenger(array(
                    'pass' => 'Supplier Contact saved to database'
                ));

                return $this->_helper->redirector('edit', 'supplier', 'power', array(
                    'supplierId'  => $supplierId
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
        if ($this->_request->getParam('contactId')) {
            $contact = $this->_model->delete($this->_request->getParam('contactId'));

            if ($contact) {
                $this->_helper->FlashMessenger(array(
                    'pass' => 'Supplier Contact deleted from database.'
                ));
            } else {
                $this->_helper->FlashMessenger(array(
                    'fail' => 'Could not delete supplier contact from database.'
                ));
            }
        }

        return $this->_helper->redirector('edit', 'supplier', 'power', array(
            'supplierId'  => $this->_request->getParam('supplierId')
        ));
    }

}
