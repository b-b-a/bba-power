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
 * @subpackage Form_Site
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Form Class Save.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Form_Site
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Form_Site_Edit extends ZendSF_Dojo_Form_Abstract
{
    protected $_simpleTextareaDecorators = array(
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
                'class' => 'form_row simple-textarea'
            )
        )
    );

    public function init()
    {

        $this->setName('site');

        $clientId = null;

        $request = Zend_Controller_Front::getInstance()->getRequest();

        $multiOptions = array();

        $siteId = $request->getPost('site_idSite');
        $row = $this->_model->getSiteById($siteId);
        $clientId =$row->site_idClient;

        $client = $row->getClient();
        $siteAd = $row->getSiteAddress();

        $multiOptions[$client->client_idClient] = $client->client_name;
        $this->addElement('FilteringSelect', 'site_idClient', array(
            'label'         => 'Client:',
            'filters'       => array('StripTags', 'StringTrim'),
            'dijitParams'   => array('hasDownArrow' => 'false'),
            'multiOptions'  => $multiOptions,
            'attribs'       => array('readonly' => true),
            'required'      => true
        ));

        $multiOptions = array();
        $multiOptions[$siteAd->clientAd_idAddress] = $siteAd->address1AndPostcode;
        $this->addElement('FilteringSelect', 'site_idAddress', array(
            'label'         => 'Address:',
            'filters'       => array('StripTags', 'StringTrim'),
            'dijitParams'   => array('hasDownArrow' => 'false'),
            'multiOptions'  => $multiOptions,
            'attribs'         => array('readonly' => true),
            'required'      => true
        ));

        $list = $this->getModel()->getDbTable('clientAddress')->getClientAddressesByClientId($clientId);

        // reset options
        $multiOptions = array(0 => ($list->count() > 0) ? 'Please select a client address' : 'No client addresses available');
        foreach($list as $row) {
            $multiOptions[$row->clientAd_idAddress] = $row->address1AndPostcode;
        }

        $this->addElement('FilteringSelect', 'site_idAddressBill', array(
            'label'         => 'Billing Address:',
            'filters'       => array('StripTags', 'StringTrim'),
            'multiOptions'  => $multiOptions,
            'value'         => '0',
            'required'      => false
        ));

        $list = $this->getModel()->getDbTable('clientContact')->getClientContactsByClientId($clientId);

        // reset options
        $multiOptions = array(0 => ($list->count() > 0) ? 'Please select a client contact' : 'No client contacts available');
        foreach($list as $row) {
            $multiOptions[$row->clientCo_idClientContact] = $row->clientCo_name;
        }

        $this->addElement('FilteringSelect', 'site_idClientContact', array(
            'label'         => 'Client Contact:',
            'filters'       => array('StripTags', 'StringTrim'),
            'multiOptions'  => $multiOptions,
            'value'         => '0',
            'required'      => false
        ));

        $this->addElement('SimpleTextarea', 'site_desc', array(
            'label'         => 'Description:',
            'required'      => false,
            'filters'       => array('StripTags', 'StringTrim'),
            'decorators'    => $this->_simpleTextareaDecorators
        ));

        $this->addHiddenElement('site_idSite', '');
    }
}
