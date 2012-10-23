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
class ZendSF_Dojo_Form_Abstract extends Zend_Dojo_Form
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
                'tag'   => 'table',
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
                'tag' => 'td',
                'class' => 'element'
            )
        ),
        array(
            'Label',
            array(
            	'tag' => 'th',
            	'class' => 'label'
            )
        ),
        array(
            array('row' => 'HtmlTag'),
            array(
                'tag' => 'tr',
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
                'tag' => 'td',
                'class' => 'element'
            )
        ),
        array(
            'Label',
            array('tag' => 'th')
        ),
        array(
            array('row' => 'HtmlTag'),
            array('tag' => 'tr')
        )
    );

    protected $_hashDecorators = array(
        'ViewHelper',
        array(
            'HtmlTag',
            array('tag' => 'span')
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
            array(
            	'tag' => 'span',
            	'class' => 'formButton',
            	
            )
        )
    );
    
    protected $_submitGroupDecorators = array(
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
    );

    /**
     * Constructor
     *
     * @param  array|Zend_Config|null $options
     * @return void
     */
    public function __construct($options = null)
    {
        $this->addPrefixPath('ZendSF_Dojo_Form_Element', 'ZendSF/Dojo/Form/Element', 'element');
        Zend_Dojo::enableForm($this);
        parent::__construct($options);
    }

    /**
     * Loads the default form decorators.
     */
    public function loadDefaultDecorators()
    {
    	if ($this->loadDefaultDecoratorsIsDisabled()) {
    		return;
    	}
    	
    	$decorators = $this->getDecorators();
    	
    	if (empty($decorators)) {
        	$this->setDecorators($this->_defaultDecorators);
    	}
    }

    /**
     * Constructs a submit button.
     *
     * @param string $label
     * @param string $attribs elements attributes
     * @return ZendSF_Form_Abstract
     */
    public function addSubmit($label, $name = 'submit', $attribs = array())
    {
        trigger_error('Use $this->addElement() instead', E_USER_DEPRECATED);
        
        $this->addElement('SubmitButton', $name, array(
            'ignore'        => true,
            'required'      => false,
            'decorators'    => $this->_submitDecorators,
            'label'         => $label,
            'attribs'       => $attribs
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
            'decorators'    => $this->_hiddenDecorators,
            'attribs'       => array(
                'data-dojo-type'    => 'dijit.form.TextBox',
            )
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
            'decorators'    => $this->_hashDecorators,
            'attribs'       => array(
                'data-dojo-type'    => 'dijit.form.TextBox',
            )
        ));

        return $this;
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

    /**
     * Excludes the email from validating against the database.
     *
     * @param string $email
     * @return ZendSF_Form_Abstract
     * @access public
     */
    public function excludeEmailFromValidation($element, $exclude)
    {
        $val = $this->getElement($element)
            ->getValidator('Db_NoRecordExists')
            ->setExclude($exclude);

        return $this;
    }

    /**
     * Set the view object
     *
     * Ensures that the view object has the dojo view helper path set.
     *
     * @param  Zend_View_Interface $view
     * @return Zend_Dojo_Form_Element_Dijit
     */
    public function setView(Zend_View_Interface $view = null)
    {
        if (null !== $view) {
            if (false === $view->getPluginLoader('helper')->getPaths('ZendSF_Dojo_View_Helper')) {
                $view->addHelperPath('ZendSF/Dojo/View/Helper', 'ZendSF_Dojo_View_Helper');
            }
        }
        return parent::setView($view);
    }
}
