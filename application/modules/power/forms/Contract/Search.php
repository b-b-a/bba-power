<?php
/**
 * Search.php
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
 * @subpackage Form
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Search.
 *
 * @category   bba-power
 * @package    Power
 * @subpackage Form
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Contract_Search extends Power_Form_SearchBase
{

    public function init()
    {
        $this->addElement('TextBox', 'contract', array(
            'label'     => 'Client:',
            'attribs'   => array('class' => 'search'),
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('TextBox', 'meter', array(
            'label'     => 'Meter:',
            'attribs'   => array('class' => 'search'),
            'filters'   => array('StripTags', 'StringTrim')
        ));

        parent::init();

    }

}
