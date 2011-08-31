<?php
/**
 * SupplierController.php
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
 * Controller Class SupplierController.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_SupplierController extends BBA_Controller_Action_Abstract
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

        $this->_model = new Power_Model_Mapper_Supplier();
        
        $this->setForm('supplierSave', array(
            'controller' => 'supplier' ,
            'action' => 'save',
            'module' => 'power'
        ));
        
        // search form
        $this->setForm('supplierSearch', array(
            'controller' => 'supplier' ,
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
            'supplier'    => $this->_request->getParam('supplier'),
            'contact'   => $this->_request->getParam('contact')
        );

        $suppliers = $this->_model->supplierSearch($search);
        $store = $this->getDataStore($suppliers, 'supplier_idSupplier');

        // gets all clients and assigns them to the view script.
        $this->view->assign(array(
            'suppliers'   => $suppliers,
            'search'    => $search,
            'store'     => $store
        ));
    }
    
    public function addAction()
    {
        $this->getForm('supplierSave')
            ->addHiddenElement('returnAction', 'add');
    }
    
    public function editAction()
    {
        if ($this->_request->getParam('supplierId')) {

            $supplier = $this->_model->find($this->_request->getParam('supplierId'));
            $contracts = $this->_model->getContractsBySupplierId($supplier->getId());
            
            $contractStore = $this->getDataStore($contracts, 'supplier_contract_idContract');
            
            $this->_log->info($contractStore);

            $this->getForm('supplierSave')
                ->populate($supplier->toArray())
                ->addHiddenElement('returnAction', 'edit');

            $this->view->assign(array(
                'supplier'        => $supplier,
                'contractStore'   => $contractStore
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

        $clientId = $this->_request->getParam('supplierId');

        if ($this->_request->getParam('cancel')) {
            return $this->_helper->redirector('index', 'supplier', 'power', array(
                'supplierId'  => $supplierId
            ));
        }

        $action = $this->_request->getParam('returnAction');

        $this->getForm('supplierSave')->addHiddenElement('returnAction', $action);

        if (!$this->getForm('suppliertSave')->isValid($this->_request->getPost())) {
            $this->view->assign(array(
                'supplier'    => $supplierId
            ));
            return $this->render($action); // re-render the edit form
        } else {
            $saved = $this->_model->save();

            if ($saved > 0) {
                $this->_helper->FlashMessenger(array(
                    'pass' => 'Supplier saved to database'
                ));

                return $this->_helper->redirector('index', 'supplier');
            } elseif ($saved == 0) {
                $this->_helper->FlashMessenger(array(
                    'fail' => 'Nothing new to save'
                ));

                return $this->_forward($action);
            }
        }
    }

    public function autocompleteAction()
    {
        $this->_helper->layout->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender(true);

        switch ($this->_request->getParam('param')) {
            case 'supplier':
                $identifier = 'supplier_idSupplier';
                $searchItems = array('idSupplier', 'name');
                $result = $this->_model->fetchAll();
                break;
        }

        $items = array();

        foreach ($result as $row) {
            $items[] = array(
                $identifier                     => $row->{$searchItems[0]},
                $row->prefix . $searchItems[1]  => $row->{$searchItems[1]}
            );
        }

        $data = new Zend_Dojo_Data($identifier, $items);
        echo $data->toJson();
    }

}
