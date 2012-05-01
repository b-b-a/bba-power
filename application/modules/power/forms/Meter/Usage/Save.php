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
 * @subpackage Form
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Save.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Meter_Usage_Save extends ZendSF_Dojo_Form_Abstract
{
    public function init()
    {
        $this->addElement('TextBox', 'usage_dateBill', array(
            'label'     => 'Bill Date:',
            'formatLength'   => 'short',
            'required'  => false,
			'dijitParams'   => array(
                'promptMessage' => 'Enter the date of the Bill',
                'style'         => 'width:100px'
            ),
            'filters'   => array('StripTags', 'StringTrim'),
            'validators'    => array(
                array('Date', true, array(
                    'format' => 'dd/MM/yyyy'
                ))
            )
        ));

        $this->addElement('TextBox', 'usage_dateReading', array(
            'label'     => 'Reading Date:',
            'formatLength'   => 'short',
            'required'  => true,
			'dijitParams'   => array(
                'promptMessage' => 'Enter the date of the Reading',
                'style'         => 'width:100px'
            ),
            'filters'   => array('StripTags', 'StringTrim'),
            'validators'    => array(
                array('Date', true, array(
                    'format' => 'dd/MM/yyyy'
                ))
            )
        ));

        $list = $this->getModel()->getDbTable('tables')->getSelectListByName('usage_type');
        $multiOptions = array();

        foreach($list as $row) {
            $multiOptions[$row->tables_key] = $row->tables_value;
        }

        $this->addElement('RadioButton', 'usage_type', array(
            'label'         => 'Reading Type:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autocomplete'  => false,
            'multiOptions'  => $multiOptions,
            'required'      => true,
            //'separator'       => '&nbsp;'
        ));

        $decors = $this->getElement('usage_type')->getDecorators();

        $decors['Zend_Form_Decorator_Label']->setOptions(array(
            'tag' => 'p',
            'style' => 'line-height: ' . count($multiOptions) * 22 . 'px;'
        ));

        $this->getElement('usage_type')->setDecorators($decors);

        $this->addElement('NumberTextBox', 'usage_usageDay', array(
            'label'     => 'Consumption - Day:',
            'constraints'   => array(
                'pattern'    => '#'
            ),
            'required'  => false,
			'value'		=> "0",
			'dijitParams'   => array(
                'promptMessage' => 'Enter the number of Day Units consumed',
                'style'         => 'width:75px'
            ),
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('NumberTextBox', 'usage_usageNight', array(
            'label'     => 'Consumption - Night:',
            'constraints'   => array(
                'pattern'    => '#'
            ),
            'required'  => false,
			'value'		=> "0",
			'dijitParams'   => array(
                'promptMessage' => 'Enter the number of Night Units consumed',
                'style'         => 'width:75px'
            ),
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('NumberTextBox', 'usage_usageOther', array(
            'label'         => 'Consumption - Other & Gas:',
            'constraints'   => array(
                'pattern'   => '#'
            ),
            'required'      => false,
 			'value'			=> "0",
			'dijitParams'   => array(
                'promptMessage' => 'Enter the number of Other or Gas Units consumed',
                'style'         => 'width:75px'
            ),
           'filters'       => array('StripTags', 'StringTrim')
        ));

        $this->addHiddenElement('usage_idUsage', '');
        $this->addHiddenElement('usage_idMeter', '');
    }

}
