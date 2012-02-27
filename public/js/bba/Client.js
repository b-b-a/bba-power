/*
 * Client.js
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
 * @package
 * @subpackage
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
define("bba/Client",
    ["dojo/dom", "dojo/ready", "dojo/parser", "dojo/_base/xhr", "dijit/registry", "bba/Core", "bba/Contract",
    "dojox/widget/Wizard", "dijit/form/ValidationTextBox", "dijit/form/FilteringSelect", "dijit/form/SimpleTextarea"],
    function(dom, ready, parser, xhr, registry, bba) {

    ready(function () {

        if (dom.byId('client')) {
            dom.byId('client').focus();
        }

        if (dom.byId('clientGrid')) {
            var form = registry.byId('Search');
            if (form) bba.gridSearch(form, clientGrid);
        }
    });

    bba.Client = {
         gridLayouts : {
            client : [
                {field: 'client_idClient', width: '50px', name: 'Id'},
                {field: 'client_name', width: '200px', name: 'Client'},
                {field: 'clientAd_address1', width: '200px', name: 'Address 1'},
                {field: 'clientAd_address2', width: '200px', name: 'Address 2'},
                {field: 'clientAd_address3', width: '200px', name: 'Address 3'},
                {field: 'clientAd_postcode', width: '100px', name: 'Postcode'},
                {field: '', width: 'auto', name: ''}
            ],
            clientAddress : [
                {field: 'clientAd_idAddress', width: '50px', name: 'Id'},
                {field: 'clientAd_addressName', width: '250px', name: 'Address Name'},
                {field: 'clientAd_address1', width: '300px', name: 'Address 1'},
                {field: 'clientAd_address2', width: '200px', name: 'Address 2'},
                {field: 'clientAd_address3', width: '100px', name: 'Address 3'},
                {field: 'clientAd_postcode', width: '100px', name: 'Postcode'},
                {field: '', width: 'auto', name: ''}
            ],
            clientContact : [
                {field: 'clientCo_idClientContact', width: '50px', name: 'Id'},
                {field: 'clientCo_type', width: '100px', name: 'Type'},
                {field: 'clientCo_name', width: '200px', name: 'Name'},
                {field: 'clientCo_phone', width: '300px', name: 'Phone'},
                {field: 'clientCo_email', width: '200px', name: 'Email'},
                {field: 'clientAd_postcode', width: '100px', name: 'Postcode'},
                {field: '', width: 'auto', name: ''}
            ]
         },

         clientGridRowClick : function(selectedIndex)
         {
            if (typeof(selectedIndex) != 'number') {
                selectedIndex = this.focus.rowIndex;
            }

            selectedItem = this.getItem(selectedIndex);
            id = this.store.getValue(selectedItem, 'client_idClient');

             bba.openTab({
                tabId : 'client' + id,
                title : this.store.getValue(selectedItem, 'client_name'),
                url : '/client/edit-client',
                content : {
                    type : 'details',
                    idClient : id
                }
            });
        },

        clientAdGridRowClick : function(grid, contentVars)
        {
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);
            id = grid.store.getValue(selectedItem, 'clientAd_idAddress');

            bba.openTab({
                tabId : 'clientAd' + id,
                title : grid.store.getValue(selectedItem, 'clientAd_addressName'),
                url : '/client/edit-client-address',
                content : dojo.mixin({
                        type :  'details',
                        clientAd_idAddress : id
                    }, contentVars)
            });
        },

        clientCoGridRowClick : function(grid, contentVars)
        {
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);
            id = grid.store.getValue(selectedItem, 'clientCo_idClientContact');

             if (!dom.byId('clientCoForm')) {
                bba.openFormDialog({
                    url: '/client/edit-client-contact',
                    content: dojo.mixin({
                        type :  'edit',
                        idClientContact : id
                    }, contentVars),
                    dialog: 'clientCoForm'
                });
            } else {
                clientCoForm.show();
            }
        },

        newClientButtonClick : function()
        {
            if (!dom.byId('clientForm')) {
                bba.openFormDialog({
                    url: '/client/add-client',
                    content: {type :  'add'},
                    dialog: 'clientForm'
                });
            } else {
                clientForm.show();
            }
        },

        newClientAdButtonClick : function(contentVars)
        {
            if (!dom.byId('clientAdForm')) {
                bba.openFormDialog({
                    url: '/client/add-client-address',
                    content: dojo.mixin({type :  'add'}, contentVars),
                    dialog: 'clientAdForm'
                });
            } else {
                clientAdForm.show();
            }
        },

        newClientCoButtonClick : function(contentVars)
        {
            if (!dom.byId('clientCoForm')) {
                bba.openFormDialog({
                    url: '/client/add-client-contact',
                    content: dojo.mixin({type :  'add'}, contentVars),
                    dialog: 'clientCoForm'
                });
            } else {
                clientCoForm.show();
            }
        },

        editClientButtonClick : function()
        {
            if (!dom.byId('clientForm')) {
                bba.openFormDialog({
                    url: '/client/edit-client',
                    content: {
                        type :  'edit',
                        idClient : this.value
                    },
                    dialog: 'clientForm'
                });
            } else {
                clientForm.show();
            }
        },

        editClientAdButtonClick : function(contentVars)
        {
            if (!dom.byId('clientAdForm')) {
                bba.openFormDialog({
                    url: '/client/edit-client-address',
                    content: dojo.mixin({type :  'edit'}, contentVars),
                    dialog: 'clientAdForm'
                });
            } else {
                clientAdForm.show();
            }
        },

        processClientForm : function()
        {
            bba.closeDialog(clientForm);

            values = arguments[0];
            values.idClient = values.client_idClient;
            values.type = (values.idClient) ? 'edit' : 'add';

            xhr.post({
                url: '/client/save-client',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    if (data.saved > 0) {
                        if (values.idClient) {
                            registry.byId('client' + values.idClient).refresh();
                        } else {
                            registry.byId('clientGrid')._refresh();
                        }
                    } else {
                        dom.byId('dialog').innerHTML = data.html;
                        parser.parse('dialog');
                        bba.setupDialog(clientForm);
                        clientForm.show();
                    }
                }
            });
        },

        processClientAdForm : function()
        {
            bba.closeDialog(clientAdForm);

            values = arguments[0];
            values.idAddress = values.clientAd_idAddress;

            xhr.post({
                url: '/client/save-client-address',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    if (data.saved > 0) {
                        if (values.idAddress) {
                            registry.byId('clientAd' + values.idAddress).refresh();
                        } else {
                            registry.byId('clientAdGrid' + values.clientAd_idClient)._refresh();
                        }
                    } else {
                        dom.byId('dialog').innerHTML = data.html;
                        parser.parse('dialog');
                        bba.setupDialog(clientAdForm);
                        clientAdForm.show();
                    }
                }
            });
        },

        processClientCoForm : function()
        {
            bba.closeDialog(clientCoForm);

            values = arguments[0];
            values.idClientContact = values.clientCo_idClientContact;

            if (values.idClientContact) {
                values.type = 'edit';
            }

            xhr.post({
                url: '/client/save-client-contact',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    dom.byId('dialog').innerHTML = data.html;
                    parser.parse('dialog');

                    if (data.error) {
                        error.show();
                    } else if (data.saved > 0) {
                        if (registry.byId('clientCoGrid' + values.clientCo_idClient)) {
                            registry.byId('clientCoGrid' + values.clientCo_idClient)._refresh();
                        }

                        if (registry.byId('clientAdCoGrid' + values.clientCo_idAddress)) {
                            registry.byId('clientAdCoGrid' + values.clientCo_idAddress)._refresh();
                        }

                        confirm.show();
                    } else {
                        bba.setupDialog(clientCoForm);
                        clientCoForm.show();
                    }
                }
            });
        }
    };

    return bba.Client;

});
