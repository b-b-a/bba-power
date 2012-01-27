/*
 * Site.js
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
 * @package    JavaScript
 * @subpackage Site
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Site.
 *
 * @category   BBA
 * @package    JavaScript
 * @subpackage Core
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
define("bba/Site",
    ["dojo/dom", "dojo/ready", "dojo/data/ItemFileReadStore", "dijit/registry", "bba/Core",
    "dijit/form/RadioButton", "dijit/form/ValidationTextBox", "dijit/form/FilteringSelect", "bba/DataGrid"],
    function(dom, ready, ItemFileReadStore, registry, bba) {

    ready(function(){
        dom.byId('site').focus();
    });

    bba.Site = {
        addressStore : null,

        gridLayouts : {
            site : [
                {field: 'site_idSite', width: '50px', name: 'Id'},
                {field: 'client_name', width: '300px', name: 'Client'},
                {field: 'clientAd_address1', width: '200px', name: 'Address 1'},
                {field: 'clientAd_address2', width: '200px', name: 'Address 2'},
                {field: 'clientAd_address3', width: '200px', name: 'Address 3'},
                {field: 'clientAd_postcode', width: '100px', name: 'Postcode'},
                {field: 'clientCo_name', width: '100px', name: 'Contact'},
                {field: '', width: 'auto', name: ''}
            ]
        },

        changeAddress : function(val)
        {
            if (registry.byId("site_idAddress").get('disabled') == true) {
                registry.byId("site_idAddress").set('disabled', false);
                registry.byId("site_idClientContact").set('disabled', false);
            }

            registry.byId('site_idAddress').set('value', '');
            registry.byId('site_idAddressBill').set('value', '');
            registry.byId('site_idClientContact').set('value', '');

            this.addressStore = new ItemFileReadStore({
                url:'/site/data-store/type/address/clientId/' + val
            });

            this.addressStore.fetch();

            registry.byId("site_idAddress").set('store', this.addressStore);

            this.billAddressStore = new ItemFileReadStore({
                url:'/site/data-store/type/billAddress/clientId/' + val
            });

            this.billAddressStore.fetch();

            this.contactStore = new ItemFileReadStore({
                url:'/site/data-store/type/contact/clientId/' + val
            });

            this.contactStore.fetch();

            registry.byId("site_idClientContact").set('store', this.contactStore);

            registry.byId("site_idAddress").set('value', 0);
            registry.byId("site_idAddressBill").set('value', 0);
            registry.byId("site_idClientContact").set('value', 0);
        },

        changeBillAddress : function()
        {
            registry.byId("site_idAddressBill").set('disabled', false);
            registry.byId("site_idAddressBill").set('store', this.billAddressStore);
            registry.byId("site_idAddressBill").set('value', 0);
        },

        changeContact : function()
        {
            registry.byId("site_idClientContact").set('disabled', false);
            registry.byId("site_idClientContact").set('store', this.contactStore);
            registry.byId("site_idClientContact").set('value', 0);
        }
    };

    return bba.Site;

});
