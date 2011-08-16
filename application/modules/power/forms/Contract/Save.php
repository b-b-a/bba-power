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
class Power_Form_Contract_Save extends ZendSF_Form_Abstract
{
    public function init()
    {
        $this->setName('contract');

        $this->addElement('TextBox', 'contract_reference', array(
            'label'     => 'Contract Ref:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $table = new Power_Model_Mapper_Tables();
        $list = $table->getSelectListByName('contract_type');
        foreach($list as $row) {
            $multiOptions[$row->key] = $row->value;
        }

        $this->addElement('FilteringSelect', 'contract_type', array(
            'label'     => 'Type:',
            'filters'   => array('StripTags', 'StringTrim'),
            'autocomplete' => false,
            'multiOptions'  => $multiOptions,
            'required'  => true,
        ));

        $list = $table->getSelectListByName('contract_status');
        foreach($list as $row) {
            $multiOptions[$row->key] = $row->value;
        }

        $this->addElement('FilteringSelect', 'contract_status', array(
            'label'     => 'Status:',
            'filters'   => array('StripTags', 'StringTrim'),
            'autocomplete' => false,
            'multiOptions'  => $multiOptions,
            'required'  => true,
        ));

        $auth = Zend_Auth::getInstance();

        $this->addHiddenElement('userId', $auth->getIdentity()->getId());
        $this->addHiddenElement('contract_idContract', '');

        $this->addSubmit('Save');
        $this->addSubmit('Cancel', 'cancel');
    }

}
