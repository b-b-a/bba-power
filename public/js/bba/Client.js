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
    ["dojo/dom", "dojo/ready", "dojo/parser", "dojo/_base/connect", "dojo/_base/xhr",
    "dijit/registry", "dojo/date", "dijit/Dialog", "dojo/text!./html/LoaEmptyMessage.html",
    "dojo/text!./html/LoaDateMessage.html", "bba/Core", "bba/Site", "bba/Contract",
    "dojox/widget/Wizard", "dijit/form/ValidationTextBox", "dijit/form/FilteringSelect",
    "dijit/form/SimpleTextarea",
    "dojox/form/Uploader", "dojox/form/uploader/plugins/IFrame"],
    function(dom, ready, parser, connect, xhr, registry, date, Dialog, LoaEmpty, LoaDate, bba) {

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
        dateExpiryLoa : null,

        gridLayouts : {
            client : [
                {field: 'client_idClient', width: '50px', name: 'Id'},
                {field: 'client_name', width: '200px', name: 'Client'},
                {field: 'clientAd_address1', width: '200px', name: 'Address 1'},
                {field: 'clientAd_address2', width: '200px', name: 'Address 2'},
                {field: 'clientAd_address3', width: '200px', name: 'Town/City'},
                {field: 'clientAd_postcode', width: '100px', name: 'Postcode'},
                {field: '', width: 'auto', name: ''}
            ],
            clientAddress : [
                {field: 'clientAd_idAddress', width: '50px', name: 'Id'},
                {field: 'clientAd_addressName', width: '250px', name: 'Address Name'},
                {field: 'clientAd_address1', width: '300px', name: 'Address 1'},
                {field: 'clientAd_address2', width: '200px', name: 'Address 2'},
                {field: 'clientAd_address3', width: '100px', name: 'Town/City'},
                {field: 'clientAd_postcode', width: '100px', name: 'Postcode'},
                {field: '', width: 'auto', name: ''}
            ],
            clientPersonnel : [
                {field: 'clientPers_idClientPersonnel', width: '50px', name: 'Id'},
                {field: 'tables_value', width: '100px', name: 'Type'},
                {field: 'clientPers_name', width: '200px', name: 'Name'},
                {field: 'clientPers_phone', width: '300px', name: 'Phone'},
                {field: 'clientPers_email', width: '200px', name: 'Email'},
                {field: 'clientAd_postcode', width: '100px', name: 'Postcode'},
                {field: '', width: 'auto', name: ''}
            ]
         },

         clientGridRowClick : function(grid)
         {
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);
            id = grid.store.getValue(selectedItem, 'client_idClient');
            tabTitle = grid.store.getValue(selectedItem, 'client_name');

             bba.openTab({
                tabId : 'client' + id,
                title : (tabTitle) ? tabTitle : 'Client',
                url : './client/edit-client',

                content : {
                    type : 'details',
                    client_idClient : id
                }
            });
        },

        clientAdGridRowClick : function(grid, contentVars)
        {
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);
            id = grid.store.getValue(selectedItem, 'clientAd_idAddress');
            tabTitle = grid.store.getValue(selectedItem, 'clientAd_addressName');

            bba.openTab({
                tabId : 'clientAd' + id,
                title : (tabTitle) ? tabTitle : 'Client Address',
                url : './client/edit-client-address',
                content : dojo.mixin({
                        type : 'details',
                        clientAd_idAddress : id
                    }, contentVars)
            });
        },

        clientPersGridRowClick : function(grid, contentVars)
        {
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);
            id = grid.store.getValue(selectedItem, 'clientPers_idClientPersonnel');

             if (!dom.byId('clientPersForm')) {
                bba.openFormDialog({
                    url: './client/edit-client-personnel',
                    content: dojo.mixin({
                        type : 'edit',
                        clientPers_idClientPersonnel : id
                    }, contentVars),
                    dialog: 'clientPersForm'
                });
            } else {
                clientPersForm.show();
            }
        },

        newClientButtonClick : function()
        {
            if (!dom.byId('clientForm')) {
                bba.openFormDialog({
                    url: './client/add-client',
                    content: {type : 'add'},
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
                    url: './client/add-client-address',
                    content: dojo.mixin({type : 'add'}, contentVars),
                    dialog: 'clientAdForm'
                });
            } else {
                clientAdForm.show();
            }
        },

        newClientPersButtonClick : function(contentVars)
        {
            if (!dom.byId('clientPersForm')) {
                bba.openFormDialog({
                    url: './client/add-client-personnel',
                    content: dojo.mixin({type : 'add'}, contentVars),
                    dialog: 'clientPersForm'
                });
            } else {
                clientPersForm.show();
            }
        },

        editClientButtonClick : function(contentVars)
        {
            if (!dom.byId('clientForm')) {
                bba.openFormDialog({
                    url: './client/edit-client',
                    content: dojo.mixin({type : 'edit'}, contentVars),
                    dialog: 'clientForm',
                    deferredFunction: function() {
                        bba.Client.setupDocEvents();
                        this.dateExpiryLoa = clientForm.getValues().client_dateExpiryLoa;
                    }.bind(this)
                });
            } else {
                clientForm.show();
            }
        },

        editClientAdButtonClick : function(contentVars)
        {
            if (!dom.byId('clientAdForm')) {
                bba.openFormDialog({
                    url: './client/edit-client-address',
                    content: dojo.mixin({type : 'edit'}, contentVars),
                    dialog: 'clientAdForm'
                });
            } else {
                clientAdForm.show();
            }
        },

        clientLoaEmptyDialog : function()
        {
            clientFormLoaEmpty = new Dialog({
                title: "Client Form Warning",
                content: LoaEmpty,
                style: "width: 300px",
                onShow : function(){
                    connect.connect(clientOKButton, 'onClick', function(){
                        clientFormLoaEmpty.hide();
                    });
                },
                onHide : function() {
                    bba.closeDialog(clientFormLoaEmpty);
                }
            });
            clientFormLoaEmpty.show();
        },

        clientFormValidate : function()
        {
            formValues = clientForm.getValues();

            if (!formValues.client_docLoa[0]) {
                return clientForm.validate();
            }

            if (formValues.client_dateExpiryLoa === '') {
                bba.Client.clientLoaEmptyDialog();
                return false;
            }

            oldDate = (this.dateExpiryLoa) ? new Date(this.dateExpiryLoa) : new Date('01/01/1970');
            newDate = new Date(formValues.client_dateExpiryLoa.replace(/\./g, '/'));

            // if newDate is newer than oldDate validate form.
            if (date.compare(newDate, oldDate)) {
                return clientForm.validate();
            }

            clientFormLoaDate = new Dialog({
                title: "Client Form Warning",
                content: LoaDate,
                style: "width: 300px",
                onShow : function(){
                    connect.connect(clientYesButton, 'onClick', function(){
                        if (clientForm.validate()) {
                            client_docLoa.submit();
                        }
                        clientFormLoaDate.hide();
                    });
                    connect.connect(clientNoButton, 'onClick', function(){
                        clientFormLoaDate.hide();
                    });
                },
                onHide : function() {
                    bba.closeDialog(clientFormLoaDate);
                }
            });
            clientFormLoaDate.show();

            return false;
        },

        processClientForm : function()
        {
            bba.closeDialog(clientForm);
            data = arguments[0];

            dom.byId('dialog').innerHTML = data.html;
            parser.parse('dialog');

            if (data.error) {
                error.show();
            } else if (data.saved > 0) {
                if (data.client_idClient) {
                    registry.byId('client' + data.client_idClient).refresh();
                } else {
                    registry.byId('clientGrid')._refresh();
                }

                if (bba.confrimBox) {
                    confirm.show();
                }
            } else {
                bba.setupDialog(clientForm);
                bba.Client.setupDocEvents();
                clientForm.show();
            }
        },

        processClientAdForm : function()
        {
            //bba.closeDialog(clientAdForm);

            values = arguments[0];
            values.type = (values.clientAd_idAddress) ? 'edit' : 'add';

            xhr.post({
                url: './client/save-client-address',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    dom.byId('dialog').innerHTML = data.html;
                    parser.parse('dialog');

                    if (data.error) {
                        error.show();
                    } else if (data.saved > 0) {
                        if (values.clientAd_idAddress) {
                            registry.byId('clientAd' + values.clientAd_idAddress).refresh();
                        } else if (registry.byId('clientAdGrid' + values.clientAd_idClient)) {
                            registry.byId('clientAdGrid' + values.clientAd_idClient)._refresh();
                        }

                        if (bba.confrimBox) {
                            confirm.show();
                        }

                        bba.deferredFunction(data.saved);
                    } else {
                        bba.setupDialog(clientAdForm);
                        clientAdForm.show();
                    }
                }
            });
        },

        processClientPersForm : function()
        {
            //bba.closeDialog(clientCoForm);

            values = arguments[0];
            values.type = (values.clientPers_idClientPersonnel) ? 'edit' : 'add';

            xhr.post({
                url: './client/save-client-personnel',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    dom.byId('dialog').innerHTML = data.html;
                    parser.parse('dialog');

                    if (data.error) {
                        error.show();
                    } else if (data.saved > 0) {
                        if (registry.byId('clientPersGrid' + values.clientPers_idClient)) {
                            registry.byId('clientPersGrid' + values.clientPers_idClient)._refresh();
                        }

                        if (registry.byId('clientAdPersGrid' + values.clientPers_idAddress)) {
                            registry.byId('clientAdPersGrid' + values.clientPers_idAddress)._refresh();
                        }

                        if (bba.confrimBox) {
                            confirm.show();
                        }
                    } else {
                        bba.setupDialog(clientPersForm);
                        clientPersForm.show();
                    }
                }
            });
        },

        setupDocEvents : function()
        {
            dojo.connect(dom.byId('client_docLoa_file'), "onclick", function(){
                dojo.query('input[name=client_docLoa]')[0].click();
            });

            connect.connect(client_docLoa, "onChange", function(fileArray){
                bba.docFileList(fileArray, 'client_docLoa_file');
            });

            connect.connect(client_docLoa, "onComplete", bba.Client.processClientForm);
            connect.connect(client_docLoa, "onError", bba.Client.processClientForm);
        }
    };

    return bba.Client;

});
