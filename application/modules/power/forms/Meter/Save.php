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
 * @subpackage Form_Meter
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Save.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Meter
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Meter_Save extends ZendSF_Form_Abstract
{
    public function init()
    {
        $this->setName('meter');

        $multiOptions = array();

        $table = new Power_Model_Mapper_Tables();
        $list = $table->getSelectListByName('meter_type');
        foreach($list as $row) {
            $multiOptions[$row->key] = $row->value;
        }

        $this->addElement('FilteringSelect', 'meter_type', array(
            'label'         => 'Type:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autocomplete'  => false,
            'multiOptions'  => $multiOptions,
            'required'      => true,
        ));

        $this->addElement('TextBox', 'meter_numberSerial', array(
            'label'     => 'Serial No:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('TextBox', 'meter_numberTop', array(
            'label'     => 'Top No:',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('TextBox', 'meter_numberMain', array(
            'label'     => 'Main No:',
            'required'  => true,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $auth = Zend_Auth::getInstance()
            ->getIdentity();

        $this->addHiddenElement('userId', $auth->getId());
        $this->addHiddenElement('meter_idMeter', '');
        $this->addHiddenElement('meter_idSite', '');

        $view = $this->getView();

        if (isset($view->request['meter'])) $this->addHiddenElement('meter', $view->request['meter']);
        if (isset($view->request['site'])) $this->addHiddenElement('site', $view->request['site']);

        if ($auth->role == 'admin') {
            $this->addSubmit('Save');
        }

        $this->addSubmit('Cancel', 'cancel');
    }

}
