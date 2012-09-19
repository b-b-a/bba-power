<?php
/**
 * SearchBase.php
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
 * Form Class SearchBase.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_SearchBase extends ZendSF_Dojo_Form_Abstract
{
	protected $_defaultDecorators = array(
		'Description',
		'FormElements',
		array(
			'HtmlTag',
			array(
				'tag'   => 'div',
				'class' => 'zend_form'
			)
		),
		'Form'
	);
	
    protected $_elementDecorators = array(
        'DijitElement',
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
        'DijitElement',
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
        $this->setName('Search');

        $this->addSubmit('Search', 'submit', array(
            'class' => 'search'
        ));

        $this->addElement('ResetButton', 'reset', array(
            'ignore'        => true,
            'required'      => false,
            'decorators'    => $this->_submitDecorators,
            'label'         => 'Reset',
            'attribs'       => array(
                'class' => 'search '
            )
        ));
    }
}
