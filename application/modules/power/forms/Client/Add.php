<?php
/**
 * Add.php
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
 * @subpackage Form_Client
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Add.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Client
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Client_Add extends Power_Form_Client_Base
{
    public function init()
    {
    	parent::init();
    	
    	$this->setDisableLoadDefaultDecorators(true);
    	
    	$this->setDecorators(array(
    		array('ViewScript', array('viewScript' => 'client/_addClientForm.phtml'))
    	));
    	
    	$this->addHiddenElement('type', 'add');
    	$this->addHiddenElement('client_idRegAddress', '');
    	
        $clientAdForm = new Power_Form_Client_Address_Save(array('model' => $this->_model));
        $clientPersForm = new Power_Form_Client_Personnel_Save(array('model' => $this->_model));

        /**
         * Add Client Address form Elements
         */
        $this->addElement($clientAdForm->getElement('clientAd_addressName'));
        $this->addElement($clientAdForm->getElement('clientAd_address1'));
        $this->addElement($clientAdForm->getElement('clientAd_address2'));
        $this->addElement($clientAdForm->getElement('clientAd_address3'));
        $this->addElement($clientAdForm->getElement('clientAd_postcode'));

        /**
         * Add Client Contact form Elements
         */
        $this->addElement($clientPersForm->getElement('clientPers_type')->setValue('liaison'));
        $this->addElement($clientPersForm->getElement('clientPers_name'));
        $this->addElement($clientPersForm->getElement('clientPers_position'));
        $this->addElement($clientPersForm->getElement('clientPers_phone'));
        $this->addElement($clientPersForm->getElement('clientPers_email'));
    }
}
