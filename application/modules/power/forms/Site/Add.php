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
class Power_Form_Site_Add extends ZendSF_Form_Abstract
{
    public function init()
    {
        $this->setName('site');

        $this->addElement('FilteringSelect', 'site_idClient', array(
            'label'         => 'Client:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autoComplete'  => false,
            'hasDownArrow'  => true,
            'storeId'       => 'clientStore',
            'storeType'     => 'dojo.data.ItemFileReadStore',
            'storeParams'   => array('url' => "/site/autocomplete/param/client"),
            'dijitParams'   => array(
                'searchAttr' => 'client_name',
                'promptMessage' => 'Select a Client'
            ),
            'attribs'       => array(
                'onChange' => 'bba.Site.changeAddress(this);'
            ),
            'required'      => true,
            'value'         => '0'
        ));

        $this->addElement('FilteringSelect', 'site_idAddress', array(
            'label'         => 'Address:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autoComplete'  => false,
            'hasDownArrow'  => true,
            'storeId'       => 'addressStore',
            //'storeType'     => 'dojo.data.ItemFileReadStore',
            //'storeParams'   => array('url' => "/site/autocomplete/param/address"),
            'dijitParams'   => array('searchAttr' => 'clientAd_address1AndPostcode'),
            'attribs'       => array(
                'disabled' => true,
                'onChange' => 'bba.Site.changeBillAddress(this);'
            ),
            'required'      => true
        ));

        $this->addElement('FilteringSelect', 'site_idAddressBill', array(
            'label'         => 'Billing Address:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autoComplete'  => false,
            'hasDownArrow'  => true,
            'storeId'       => 'addressStore',
            //'storeType'     => 'dojo.data.ItemFileReadStore',
            //'storeParams'   => array('url' => "/site/autocomplete/param/address"),
            'dijitParams'   => array('searchAttr' => 'clientAd_address1AndPostcode'),
            'attribs'         => array('disabled' => true),
            'required'      => true
        ));

        $this->addElement('FilteringSelect', 'site_idClientContact', array(
            'label'         => 'Client Contact:',
            'filters'       => array('StripTags', 'StringTrim'),
            'autoComplete'  => false,
            'hasDownArrow'  => true,
            'storeId'       => 'contactStore',
            //'storeType'     => 'dojo.data.ItemFileReadStore',
            //'storeParams'   => array('url' => "/site/autocomplete/param/contact"),
            'dijitParams'   => array('searchAttr' => 'clientCo_name'),
            'attribs'         => array('disabled' => true),
            'required'      => false
        ));

        $auth = Zend_Auth::getInstance()->getIdentity();

        $this->addHiddenElement('userId', $auth->getId());
        $this->addHiddenElement('site_idSite', '');
    }
}
