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
[
    "dojo/dom",
    "dojo/_base/connect",
    "dojo/parser",
    "dojo/_base/xhr",
    "dojo/data/ItemFileReadStore",
    "dijit/registry",
    "bba/Core",
    "bba/Meter",
    "bba/Client",
    "dijit/form/RadioButton",
    "dijit/form/NumberTextBox",
    "dijit/form/FilteringSelect",
    "dijit/form/SimpleTextarea",
    "dojox/widget/Standby"
],
    function(dom, connect, parser, xhr, ItemFileReadStore, registry, core) {

    bba.Site = {
        clientStore : null,
        addressStore : null,
        billAddressStore : null,
        contactStore : null,

        gridLayouts : {
            site : [
                {field: 'site_idSite', width: '50px', name: 'Id'},
                {field: 'clientAd_addressName', width: '200px', name: 'Address Name'},
                {field: 'clientAd_address1', width: '200px', name: 'Address 1'},
                {field: 'clientAd_address2', width: '200px', name: 'Address 2'},
                {field: 'clientAd_address3', width: '200px', name: 'Town/City'},
                {field: 'clientAd_postcode', width: '100px', name: 'Postcode'},
                {field: 'clientPers_name', width: '100px', name: 'Liaison'},
                {field: '', width: 'auto', name: ''}
            ],
            meter : [
                {field: 'meter_idMeter', width: '50px', name: 'Id'},
                {field: 'meter_type', width: '150px', name: 'Type'},
                {field: 'meter_status', width: '150px', name: 'Status'},
                {field: 'meter_numberSerial', width: '200px', name: 'Number Serial'},
                {field: 'meter_numberTop', width: '100px', name: 'Number Top'},
                {field: 'meter_numberMain', width: '200px', name: 'Number Main'},
                {field: '', width: 'auto', name: ''}
            ]
        },
        
        init : function()
        {
            core.addDataStore('siteStore', core.storeUrls.site);

            core.addGrid({
                id : 'siteGrid',
                store : core.dataStores.siteStore,
                structure : this.gridLayouts.site,
                sortInfo : '2',
                onRowClick : function() {
                     this.siteGridRowClick();
                }.bind(this)
            });
        },

        setupClientStore : function()
        {
            siteFormStandby.show();
            id = registry.byId("site_idClient").get('value');

            bba.Site.clientStore = new ItemFileReadStore({
                url:'./site/data-store/type/clients'
            });

            bba.Site.clientStore.fetch({
                onComplete: function() {
                    siteFormStandby.hide();
                },
                onError: function(error, request) {
                    bba.dataStoreError(request.store.url, null);
                }
            });

            registry.byId("site_idClient").set('store', bba.Site.clientStore);
            registry.byId("site_idClient").set('value', 0);

            connect.connect(registry.byId("site_idClient"), 'onChange', function() {
                    bba.Site.changeAddress(registry.byId("site_idClient").get('value'));
            });
        },

        getAddressStore : function(val, id)
        {
            siteFormStandby.show();
            this.addressStore = new ItemFileReadStore({
                url:'./site/data-store/type/address/clientId/' + id
            })

            this.addressStore.fetch({
                onComplete: function() {
                    siteFormStandby.hide();
                },
                onError: function(error, request) {
                    bba.dataStoreError(request.store.url, null);
                }
            });

            registry.byId("site_idAddress").set('store', this.addressStore);
            if (val) registry.byId('site_idAddress').set('value', val);
        },

        getBillAddressStore : function(val, id)
        {
            siteFormStandby.show();
            this.billAddressStore = new ItemFileReadStore({
                url:'./site/data-store/type/billAddress/clientId/' + id
            })

            this.billAddressStore.fetch({
                onComplete: function() {
                    siteFormStandby.hide();
                },
                onError: function(error, request) {
                    bba.dataStoreError(request.store.url, null);
                }
            });

            registry.byId("site_idAddressBill").set('store', this.billAddressStore);
            if (val) registry.byId('site_idAddressBill').set('value', val);
        },

        getPersonnelStore : function(val, id)
        {
            siteFormStandby.show();
            val = (val) ? val : 0;
            this.personnelStore = new ItemFileReadStore({
                url:'./site/data-store/type/personnel/clientId/' + id
            })

            this.personnelStore.fetch({
                onComplete: function() {
                    siteFormStandby.hide();
                },
                onError: function(error, request) {
                    bba.dataStoreError(request.store.url, null);
                }
            });

            registry.byId("site_idClientPersonnel").set('store', this.personnelStore);
            registry.byId('site_idClientPersonnel').set('value', val);
        },

        changeAddress : function(val)
        {
            siteFormStandby.show();
            if (registry.byId("site_idAddress").get('disabled') == true) {
                registry.byId("site_idAddress").set('disabled', false);
                registry.byId("site_idClientPersonnel").set('disabled', false);
            }

            registry.byId('site_idAddress').set('value', '');
            registry.byId('site_idAddressBill').set('value', '');
            registry.byId('site_idClientPersonnel').set('value', '');

            this.getAddressStore(null, val);

            this.getBillAddressStore(null, val);

            this.getPersonnelStore(null, val);

            registry.byId("site_idAddress").set('value', 0);
            registry.byId("site_idAddressBill").set('value', 0);
            registry.byId("site_idClientPersonnel").set('value', 0);
        },

        changeBillAddress : function(obj)
        {
            if (obj.value === -1) {
                bba.Client.newClientAdButtonClick({
                    clientAd_idClient : registry.byId("site_idClient").get('value')
                });

                bba.deferredFunction = function(val) {
                    bba.Site.getAddressStore(val, registry.byId("site_idClient").get('value'));
                    bba.Site.getBillAddressStore(val, registry.byId("site_idClient").get('value'));
                }
            }

            registry.byId("site_idAddressBill").set('disabled', false);
            registry.byId("site_idAddressBill").set('store', this.billAddressStore);
            registry.byId("site_idAddressBill").set('value', 0);
        },

        addPersonnel : function(obj)
        {
            if (obj.value === -1) {
                bba.Client.newClientPersButtonClick({
                    clientPers_idClient : registry.byId('site_idClient').get('value')
                });

                bba.deferredFunction = function(val) {
                    bba.Site.getPersonnelStore(val, registry.byId('site_idClient').get('value'));
                }
            }
        },

        changePersonnel : function()
        {
            registry.byId("site_idClientPersonnel").set('disabled', false);
            registry.byId("site_idClientPersonnel").set('store', this.personnelStore);
            registry.byId("site_idClientPersonnel").set('value', 0);
        },

        siteGridRowClick : function(grid)
        {
            grid = (grid) ? grid : core.grids.siteGrid;
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);
            id = grid.store.getValue(selectedItem, 'site_idSite');
            tabTitle = grid.store.getValue(selectedItem, 'clientAd_addressName');

            this.showSiteTab(id, tabTitle);
        },

        showSiteTab : function(id, tabTitle)
        {
             bba.openTab({
                tabId : 'site' + id,
                title : (tabTitle) ? tabTitle : 'Site',
                url : './site/edit-site',
                content : {
                    type : 'details',
                    site_idSite : id
                }
            });
        },

        newSiteButtonClick : function()
        {
            if (!dom.byId('siteForm')) {
                bba.openFormDialog({
                    url: './site/add-site',
                    content: dojo.mixin({type :  'add'}),
                    dialog: 'siteForm',
                    deferredFunction: function() {
                        bba.Site.setupClientStore();
                    }
                });
            } else {
                siteForm.show();
            }
        },

        editSiteButtonClick : function(contentVars)
        {
            if (!dom.byId('siteForm')) {
                bba.openFormDialog({
                    url: './site/edit-site',
                    content: dojo.mixin({type :  'edit'}, contentVars),
                    dialog: 'siteForm',
                    deferredFunction : function() {
                        val = registry.byId('site_idClientPersonnel').get('value');
                        id = registry.byId('site_idClient').get('value');
                        bba.Site.getPersonnelStore(val, id);
                    }
                });
            } else {
                siteForm.show();
            }
        },

        processSiteForm : function()
        {
            //bba.closeDialog(siteForm);

            values = arguments[0];
            values.type = (values.site_idSite) ? 'edit' : 'add';

            xhr.post({
                url: './site/save-site',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    dom.byId('dialog').innerHTML = data.html;
                    parser.parse('dialog');

                    if (data.error) {
                        error.show();
                    } else if (data.saved > 0) {
                        if (values.site_idSite) {
                            registry.byId('site' + values.site_idSite).refresh();
                        } else {
                            registry.byId('siteGrid')._refresh();
                        }

                        if (bba.config.confirmBox) {
                            confirm.show();
                        }

                        if (values.type === 'add') {
                            bba.Site.showSiteTab(data.saved, data.clientAd_addressName);
                        }
                    } else {
                        bba.setupDialog(siteForm);
                        siteForm.show();
                        bba.Site.changeAddress(values.site_idClient);
                    }
                }
            });
        }
    };

    return bba.Site;

});
