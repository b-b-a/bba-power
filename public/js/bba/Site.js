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
dojo.provide('bba.Site');

bba.Site = {
    addressStore : null,

    changeAddress : function(val)
    {
        if (dijit.byId("site_idAddress").get('disabled') == true) {
            dijit.byId("site_idAddress").set('disabled', false);
            dijit.byId("site_idClientContact").set('disabled', false);
        }

        dijit.byId('site_idAddress').set('value', '');
        dijit.byId('site_idAddressBill').set('value', '');
        dijit.byId('site_idClientContact').set('value', '');

        this.addressStore = new dojo.data.ItemFileReadStore({
            url:'/site/data-store/type/address/clientId/' + val
        });

        this.addressStore.fetch();

        dijit.byId("site_idAddress").store = this.addressStore;

        this.contactStore = new dojo.data.ItemFileReadStore({
            url:'/site/data-store/type/contact/clientId/' + val
        });

        this.contactStore.fetch();

        dijit.byId("site_idClientContact").store = this.contactStore;
    },

    changeBillAddress : function()
    {
        dijit.byId("site_idAddressBill").set('disabled', false);
        dijit.byId("site_idAddressBill").store = this.addressStore;
    },

    changeContact : function()
    {
        dijit.byId("site_idClientContact").set('disabled', false);
        dijit.byId("site_idClientContact").store = this.contactStore;
    }
};

//dojo.require('bba.Site');
