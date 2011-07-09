<?php
/**
 * Search.php
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
 * Form Class Search.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Meter
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Meter_Search extends ZendSF_Form_Abstract
{
    protected $_elementDecorators = array(
        'ViewHelper',
        'Errors',
        array(
            'HtmlTag',
            array(
                'tag'   => 'span',
                'class' => 'search'
            )
        ),
        array(
            'Label',
            array(
                'tag'   => 'span',
                'class' => 'search'
            )
        ),
        array(
            array('row' => 'HtmlTag'),
            array(
                'tag'   => 'span',
                'class' => 'row'
            )
        )
    );

    protected $_submitDecorators = array(
        'ViewHelper',
        array(
            'HtmlTag',
            array(
                'tag'   => 'span',
                'class' => 'search'
            )
        )
    );

    public function init()
    {
        $this->setName('MeterSearch');

        $this->addElement('text', 'search_meter', array(
            'label'     => 'Meter:',
            'attribs'   => array('class' => 'search'),
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addElement('text', 'search_client', array(
            'label'     => 'Client:',
            'attribs'   => array('class' => 'search'),
            'filters'   => array('StripTags', 'StringTrim')
        ));

        $this->addSubmit('Search', 'submit', 'search');
    }

}
