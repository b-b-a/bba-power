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
    }

    public function editAction()
    {
        if ($this->_request->getParam('tenderId')) {
            $tender = $this->_model->find($this->_request->getParam('tenderId'));
            $tenders = $this->_model->getTendersByContractId(
                $tender->idContract
            );

            $tenderStore = $this->getDataStore($tenders, 'tender_idTender');

            $this->view->assign(array(
                'tender'        => $tender,
                'tenderStore'   => $tenderStore
            ));
        } else {
           return $this->_helper->redirector('index', 'contract');
        }
    }

}
