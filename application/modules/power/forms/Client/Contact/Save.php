<?php
/**
 * Save.php
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
 * Form Class Save.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Client
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Client_Contact_Save extends ZendSF_Form_Abstract
{
    public function init()
    {
        $this->setName('client-contact');

        $table = new Power_Model_Mapper_Tables();
        $list = $table->getSelectListByName('ClientCo_type');
        foreach($list as $row) {
            $multiOptions[$row->key] = $row->value;
        }

        $this->addElement('select', 'clientCo_type', array(
            'label'     => 'Type:',
            'filters'   => array('StripTags', 'StringTrim'),
            'MultiOptions'  => $multiOptions,
            'required'  => true
        ));

        $this->addElement('text', 'clientCo_name', array(
            'label'     => 'Name:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('text', 'clientCo_phone', array(
            'label'     => 'Phone:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('text', 'clientCo_email', array(
            'label'     => 'email:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $auth = Zend_Auth::getInstance();

        $this->addHiddenElement('userId', $auth->getIdentity()->getId());
        $this->addHiddenElement('clientCo_idClientContact', '');
        $this->addHiddenElement('clientCo_idClient', '');

        $this->addSubmit('Save');
        $this->addSubmit('Cancel', 'cancel');
    }

}
