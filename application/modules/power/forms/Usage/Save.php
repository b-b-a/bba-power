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
class Power_Form_Usage_Save extends ZendSF_Form_Abstract
{
    public function init()
    {
        $this->addElement('DateTextBox', 'usage_dateBill', array(
            'label'     => 'Bill Date:',
            'formatLength'   => 'short',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim'),
            'validators'    => array(
                array('Date', true, array(
                    'format' => 'yyyy-MM-dd'
                ))
            )
        ));

        $this->addElement('DateTextBox', 'usage_dateReading', array(
            'label'     => 'Reading Date:',
            'formatLength'   => 'short',
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim'),
            'validators'    => array(
                array('Date', true, array(
                    'format' => 'yyyy-MM-dd'
                ))
            )
        ));

       $table = new Power_Model_Mapper_Tables();
        $list = $table->getSelectListByName('usage_type');
        foreach($list as $row) {
            $multiOptions[$row->key] = $row->value;
        }

        $this->addElement('FilteringSelect', 'usage_type', array(
            'label'         => 'Type:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autocomplete'  => false,
            'multiOptions'  => $multiOptions,
            'required'      => true,
        ));

        $this->addElement('NumberSpinner', 'usage_usageDay', array(
            'label'     => 'Day:',
            'constraints'   => array(
                'pattern'    => '#',
                'min'       => 0
            ),
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('NumberSpinner', 'usage_usageNight', array(
            'label'     => 'Night:',
            'constraints'   => array(
                'pattern'    => '#',
                'min'       => 0
            ),
            'required'  => false,
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('NumberSpinner', 'usage_usageOther', array(
            'label'         => 'Other:',
            'constraints'   => array(
                'pattern'   => '#',
                'min'       => 0
            ),
            'required'      => false,
            'filters'       => array('StripTags', 'StringTrim')
        ));

        $auth = Zend_Auth::getInstance();

        $this->addHiddenElement('userId', $auth->getIdentity()->getId());
        $this->addHiddenElement('usage_idUsage', '');
        $this->addHiddenElement('usage_idMeter', '');

        $this->addSubmit('Save', 'submit');
        $this->addSubmit('Cancel', 'cancel');
    }

}
