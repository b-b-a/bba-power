<?php
/**
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Form Class DocLoa.
 *
 * @category   BBA_Power
 * @package
 * @subpackage Form
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Client_DocLoa extends ZendSF_Dojo_Form_Abstract
{
    public function init()
    {
        $this->setName('clientDocLoa');
        $this->setAttrib('enctype', 'multipart/form-data');

        $this->addElement('file','client_docLoa', array(
            'label'         => 'New LoA Document (pdf):',
            'filters'       => array('StripTags', 'StringTrim'),
            'validators'    => array(
                array('Count', false, array(1)),
                array('Extension', false, array('pdf')),
            ),
            'destination'   => realpath(APPLICATION_PATH . '/../bba-power-docs/client_docLoa'),
            'decorators'    => $this->_fileDecorators,
            'required'      => false,
            'attribs'       => array(
                'data-dojo-type'    => 'dojox.form.Uploader',
                'data-dojo-id'      => 'client_docLoa',
                'data-dojo-props'   => "isDebug:true, label: 'Choose LoA File', showInput: 'before'",
                'force'             => "iframe"
            )
        ));
    }
}
