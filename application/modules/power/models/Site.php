<?php
/**
 * Site.php
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
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * DAO to represent a single Site.
 *
 * @category   BBA
 * @package    Power
 * @subpackage Model
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
class Power_Model_Site extends ZendSF_Model_Acl_Abstract
{
    /**
     * Get site by their id
     *
     * @param  int $id
     * @return null|Power_Model_DbTable_Row_Site
     */
    public function getSiteById($id)
    {
        $id = (int) $id;
        return $this->getDbTable('site')->getSiteById($id);
    }

    public function getSiteDetailsById($id)
    {
        $id = (int) $id;
        return $this->getDbTable('site')->getSiteDetailsById($id);
    }

    /**
     * Gets the site data store list, using search parameters.
     *
     * @param array $post
     * @return string
     */
    public function getSiteDataStore(array $post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $form = $this->getForm('siteSearch');
        $search = array();

        if ($form->isValid($post)) {
            $search = $form->getValues();
        }

        $dataObj = $this->getDbTable('site')->searchSites($search, $sort, $count, $start);

        $store = $this->_getDojoData($dataObj, 'site_idSite');

        $store->setMetadata(
            'numRows',
            $this->getDbTable('site')->numRows($search)
        );

        return $store->toJson();
    }

    /**
     * Gets the site meters data store list, using search parameters.
     *
     * @param array $post
     * @return string
     */
    public function getSiteMetersDataStore($post)
    {
        $sort = $post['sort'];
        $count = $post['count'];
        $start = $post['start'];

        $row = $this->getSiteById($post['meter_idSite']);
        $meters = $row->getMeters($sort, $count, $start);

        $store = $this->_getDojoData($meters, 'meter_idMeter');

        $store->setMetadata(
            'numRows',
            $row->getMeters()->count()
        );

        return $store->toJson();
    }

    /**
     * Gets the data for filtering selects.
     *
     * @param array $param
     * @return string
     */
    public function getFileringSelectData($params)
    {
        switch ($params['type']) {
            case 'clients':
                $result = $this->getDbTable('client')->fetchAll(null, 'client_name ASC');
                $identifier = 'client_idClient';
                $searchItems = array('client_idClient', 'client_name');
                break;
            case 'address':
            case 'billAddress':
                $identifier = 'clientAd_idAddress';
                $searchItems = array('clientAd_idAddress', 'address1AndPostcode');

                if ($params['type'] == 'address') {
                    $result = $this->getDbTable('clientAddress')
                        ->getAvailableSiteAddressesByClientId($params['clientId']);
                } else {
                    $result = $this->getDbTable('clientAddress')
                        ->getClientAddressesByClientId($params['clientId']);
                }

                break;
            case 'contact':
                $identifier = 'clientCo_idClientContact';
                $searchItems = array('clientCo_idClientContact', 'clientCo_name');
                $result = $this->getDbTable('clientContact')
                    ->getClientContactsByClientId($params['clientId']);
                break;
        }

        $items = array();

        foreach ($result as $row) {
            $items[] = array(
                $identifier     => $row->{$searchItems[0]},
                $searchItems[1] => $row->{$searchItems[1]}
            );
        }

        $data = new Zend_Dojo_Data($identifier, $items);

        return $data->toJson();
    }

    /**
     * Updates sites.
     *
     * @param array $post
     * @return false|int
     */
    public function saveSite($post, $form)
    {
        if (!$this->checkAcl('saveSite')) {
            throw new ZendSF_Acl_Exception('Insufficient rights');
        }

        $form = $this->getForm($form);

        if (!$form->isValid($post)) {
            return false;
        }

        // get filtered values
        $data = $form->getValues();

        $site = array_key_exists('site_idSite', $data) ?
            $this->getSiteById($data['site_idSite']) : null;

        return $this->getDbTable('site')->saveRow($data, $site);
    }

    /**
     * Injector for the acl, the acl can be injected directly
     * via this method.
     *
     * We add all the access rules for this resource here, so we first call
     * parent method to add $this as the resource then we
     * define it rules here.
     *
     * @param Zend_Acl_Resource_Interface $acl
     * @return ZendSF_Model_Abstract
     */
    public function setAcl(Zend_Acl $acl) {
        parent::setAcl($acl);

        // implement rules here.
        $this->_acl->allow('admin', $this);

        return $this;
    }
}
