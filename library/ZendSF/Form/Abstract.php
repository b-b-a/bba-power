<?php
/**
 * Abstract.php
 *
 * Copyright (c) 2011 Shaun Freeman <shaun@shaunfreeman.co.uk>.
 *
 * This file is part of ZendSF.
 *
 * ZendSF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ZendSF is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ZendSF.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage Form
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Base class for forms.
 *
 * @category   ZendSF
 * @package    ZendSF
 * @subpackage Form
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class ZendSF_Form_Abstract extends Zend_Form
{
    /**
     * @var ZendSF_Model_Mapper_Abstract
     */
    protected $_model;

    protected $_captchaDecorators = array(
        'Errors',
        array(
            array('data' => 'HtmlTag'),
            array(
                'tag'   => 'p',
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
                'tag'   => 'div',
                'id'    => 'captcha'
            )
        )
    );

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

    /**
     * An array that set the global decorators for all elements.
     *
     * @var array
     */
    protected $_elementDecorators = array(
        'ViewHelper',
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
        'ViewHelper',
        array(
            'HtmlTag',
            array('tag' => 'div')
        )
    );

    /**
     * Loads the default form decorators.
     */
    public function loadDefaultDecorators()
    {
        $this->setDecorators($this->_defaultDecorators);
    }

    /**
     * Constructs a submit button.
     *
     * @param string $label
     * @param string $class elements class name
     * @return ZendSF_Form_Abstract
     */
    public function addSubmit($label, $name = 'submit', $class = '')
    {
        $this->addElement('submit', $name, array(
            'ignore'        => true,
            'decorators'    => $this->_submitDecorators,
            'label'         => $label,
            'attribs'       => array ('class' => $class)
        ));

       return $this;
    }

    /**
     * Adds a hidden element to the form.
     *
     * @param string $id id or name of element.
     * @param string $value value to be inserted into the hidden element.
     * @return ZendSF_Form_Abstract
     */
    public function addHiddenElement($id, $value)
    {
        $this->addElement('hidden', $id, array(
            'value'         => $value,
            'decorators'    => $this->_hiddenDecorators
        ));

        return $this;
    }

    /**
     * Injects a hash input element into the form to protect against CSRF attacks.
     *
     * @param string $id id or name of element.
     * @return ZendSF_Form_Abstract
     */
    public function addHash($id)
    {
        $this->addElement('hash', $id, array(
            'ignore'        => true,
            'salt'          => 'unique',
            'decorators'    => $this->_hashDecorators
        ));

        return $this;
    }

    /**
     * Adds the Captcha element to the form.
     *
     * @param array $data array of options to apply for the captcha
     * @param string $class elements class name
     */
    public function addCaptcha($data, $class = '')
    {
        $this->addElement('captcha', 'captcha', array(
            'captcha'    => $data,
            'required'   => true,
            'label'      => _('Please enter the letters displayed below:'),
            'attribs'    => array ('class' => $class),
            'decorators' => $this->_captchaDecorators
        ));
    }

    /**
     * Model setter
     *
     * @param ZendSF_Model_Abstract
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }

    /**
     * Model Getter
     *
     * @return ZendSF_Model_Abstract
     */
    public function getModel()
    {
        return $this->_model;
    }
}
