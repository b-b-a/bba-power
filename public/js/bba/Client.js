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
    ["dojo/dom", "dojo/ready", "dojo/parser", "dojo/_base/xhr", "dijit/registry", "bba/Core", "bba/DataGrid",
    "dojox/widget/Wizard", "dijit/form/ValidationTextBox", "dijit/form/FilteringSelect", "dijit/form/SimpleTextarea"],
    function(dom, ready, parser, xhr, registry, bba) {

    ready(function () {
        dom.byId('client').focus();
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
                contentVars : {
                    type : 'details',
                    idClient : id
                }
            });
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
        
        processClientForm : function()
        {
            bba.closeDialog(clientForm);

            values = arguments[0];
            values.idClient = values.client_idClient;

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
        }
    };

    return bba.Client;

});
