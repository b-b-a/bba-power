<?php
/**
 * TenderController.php
 *
 * Copyright (c) 2011 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of bba-power.
 *
 * bba-power is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * bba-power is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bba-power.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   bba-power
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Controller Class TenderController.
 *
 * @category   bba-power
 * @package    Power
 * @subpackage Controller
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_TenderController extends BBA_Controller_Action_Abstract
{
    /**
     * @var Power_Model_Mapper_Tender
     */
    protected $_model;

    /**
     * Initialization code.
     */
    public function init()
    {
        if ($this->_helper->acl('Guest')) {
            return $this->_forward('login', 'auth');
        }

        parent::init();

        $this->_model = new Power_Model_Mapper_Tender();

        $this->setForm('tenderSave', array(
            'controller' => 'tender' ,
            'action' => 'save',
            'module' => 'power'
        ));

        $this->_setSearch(array(
            'tender_idTender', 'tender_idContract'
        ));
    }

    public function tenderStoreAction()
    {
        return $this->_getAjaxDataStore('getTendersByContractId', 'tender_idTender', true);
    }

    public function editAction()
    {
        if ($this->_request->getParam('idTender')) {
            $tender = $this->_model->getTenderDetails($this->_request->getParam('idTender'));

            $this->getForm('tenderSave')
                    ->populate($tender->toArray('dd/MM/yyyy'))
                    ->addHiddenElement('returnAction', 'edit');

            $this->view->assign(array(
                'tender' => $tender
            ));
        } else {
           return $this->_helper->redirector('index', 'contract');
        }
    }

    public function saveAction()
    {
        if (!$this->_request->isPost()) {
            return $this->_helper->redirector('index', 'contract');
        }

        $tenderId = $this->_request->getParam('tenderId');

        if ($this->_request->getParam('cancel')) {
            // needs to return to contract edit.
            return $this->_helper->redirector('index', 'contract', 'power');
        }
    }

}
