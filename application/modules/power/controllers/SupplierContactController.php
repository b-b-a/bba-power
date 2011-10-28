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
         if ($this->_request->isXmlHttpRequest()
                && $this->_request->getParam('type') == 'add'
                && $this->_request->isPost()) {
            $this->getForm('supplierContactSave')
                ->populate(array(
                    'supplierCo_idSupplierContact' => $this->_request->getParam('supplierId')
                ));

            $this->render('ajax-form');
        } else {
            return $this->_helper->redirector('index', 'client');
        }
    }

    public function editAction()
    {
        if ($this->_request->getParam('idContact')
                && $this->_request->isXmlHttpRequest()
                && $this->_request->isPost()) {
            $supplierCo = $this->_model->find($this->_request->getParam('contactId'));
            $this->getForm('supplierContactSave')
                ->populate($supplierCo->toArray('dd/MM/yyyy'));

            $this->render('ajax-form');

        } else {
           return $this->_helper->redirector('index', 'client');
        }
    }

    public function saveAction()
    {
        if (!$this->_request->isPost()&& !$this->_request->isXmlHttpRequest()) {
            return $this->_helper->redirector('index', 'supplier');
        }

        $this->_helper->viewRenderer->setNoRender(true);

        if (!$this->getForm('supplierContactSave')->isValid($this->_request->getPost())) {
            $html = $this->view->render('supplier-contact/ajax-form.phtml');

            echo json_encode(array(
                'saved' => 0,
                'html'  => $html
            ));
        } else {
            $saved = $this->_model->save();

            $returnJson = array(
                'saved' => $saved
            );

            if ($saved == 0) {
                $html = $this->view->render('supplier-contact/ajax-form.phtml');
                $returnJson['html'] = $html;
            }

            echo json_encode($returnJson);
        }
    }
}
