<?php
/**
 * Login.php
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
 * @subpackage Form_Auth
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Description of Login
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Auth
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Auth_Login extends Power_Form_Dojo_Abstract
{
    protected $_defaultDecorators = array(
        'Description',
        'FormElements',
        array(
            'HtmlTag',
            array(
                'tag'   => 'table',
                'class' => 'zend_form'
            )
        ),
        'DijitForm'
    );
    
    public function init()
    {
        $this->setName('auth');

        $this->addElement('ValidationTextBox', 'user_name', array(
            'label'     => 'Username:',
            'required'  => true,
            'filters'   => array('StringTrim', 'StripTags')
        ));

        $this->addElement('PasswordTextBox', 'user_password', array(
            'label'     => 'Password:',
            'required'  => true,
            'filters'   => array('StringTrim', 'StripTags')
        ));
        
        $this->addElement('Button', 'loginSubmitButton', array(
                'required'  => false,
                'ignore'    => true,
                'decorators'    => $this->_submitDecorators,
                'label'     => 'Submit',
                'value'     => 'Submit',
                'attribs' => array('type' => 'submit')
        ));
        
        $this->addDisplayGroup(
            array(
                'loginSubmitButton',
            ),
            'Buttons',
            array(
                'decorators' => array(
                    'FormElements',
                    array(
                        array('data' => 'HtmlTag'),
                        array(
                            'tag' => 'td',
                            'class' => 'submitElement',
                            'colspan' => '2'
                        )
                    ),
                    array(
                        array('row' => 'HtmlTag'),
                        array(
                            'tag' => 'tr',
                            'class' => 'form_row'
                        )
                    )
                )
            )
        );

        $this->addHash('csrf');
    }
}

?>
