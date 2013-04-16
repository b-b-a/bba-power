<?php
/**
 * Abstract.php
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
 * @package    BBA
 * @subpackage Dojo_Form
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Base class for forms.
 *
 * @category   BBA
 * @package    BBA
 * @subpackage Dojo_Form
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class BBA_Dojo_Form_Abstract extends Power_Form_Dojo_Abstract
{
    /**
     * @var ZendSF_Model_Abstract
     */
    protected $_model;

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
        'DijitForm'
    );

    /**
     * An array that set the global decorators for all elements.
     *
     * @var array
     */
    protected $_elementDecorators = array(
        'DijitElement',
        'Errors',
        'Description',
        array(
            array('data' => 'HtmlTag'),
            array(
                'tag' => 'p',
                'class' => 'element'
            )
        ),
        array(
            'Label',
            array('tag' => 'p')
        ),
        array(
            array('row' => 'HtmlTag'),
            array(
                'tag' => 'div',
                'class' => 'form_row'
            )
        )
    );

    protected $_fileDecorators = array(
        'File',
        'Errors',
        'Description',
        array(
            array('data' => 'HtmlTag'),
            array(
                'tag' => 'p',
                'class' => 'element'
            )
        ),
        array(
            'Label',
            array('tag' => 'p')
        ),
        array(
            array('row' => 'HtmlTag'),
            array('tag' => 'div')
        )
    );

    protected $_hashDecorators = array(
        'ViewHelper',
        array(
            'HtmlTag',
            array('tag' => 'div')
        )
    );

    protected $_hiddenDecorators = array(
        'ViewHelper',
        array(
            'HtmlTag',
            array('tag' => 'div')
        )
    );

    protected $_submitDecorators = array(
        'DijitElement',
        array(
            'HtmlTag',
            array('tag' => 'span')
        )
    );
}
