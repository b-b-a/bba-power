/*
 * Supplier.js
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
define("bba/Supplier",
    ["dojo/dom", "dojo/ready", "dojo/parser", "dojo/_base/xhr", "dijit/registry", "bba/Core",
    "bba/Contract", "dijit/form/FilteringSelect"],
    function(dom, ready, parser, xhr, registry, bba){

    ready(function () {
        
        if (dom.byId('supplier')) {
            dom.byId('supplier').focus();
        }

        if (dom.byId('supplierGrid')) {
            var form = registry.byId('Search');
            if (form) bba.gridSearch(form, supplierGrid);
        }
    });

    bba.Supplier = {
        gridLayouts : {
            supplier : [
                {field: 'supplier_idSupplier', width: '50px', name: 'Id'},
                {field: 'supplier_name', width: '300px', name: 'Supplier Name'},
                {field: 'supplier_nameShort', width: '75px', name: 'Supplier'},
                {field: 'supplier_address1', width: '350px', name: 'Address 1'},
                {field: 'supplier_postcode', width: '100px', name: 'Postcode'},
                {field: '', width: 'auto', name: ''}
            ],
            supplierContract : [
                {field: 'contract_idContract', width: '50px', name: 'Id'},
                {field: 'client_name', width: '300px', name: 'Client'},
                {field: 'contract_type', width: '150px', name: 'Type'},
                {field: 'contract_status', width: '100px', name: 'Status'},
                {field: 'contract_dateStart', width: '100px', name: 'Start Date'},
                {field: 'contract_dateEnd', width: '100px', name: 'End Date'},
                {field: 'contract_reference', width: '200px', name: 'Reference'},
                {field: '', width: 'auto', name: ''}
            ],
            supplierContact : [
                {field: 'supplierCo_idSupplierContact', width: '50px', name: 'Id'},
                {field: 'supplierCo_name', width: '150px', name: 'Name'},
                {field: 'supplierCo_phone', width: '100px', name: 'Phone'},
                {field: 'supplierCo_email', width: '300px', name: 'Email'},
                {field: 'supplierCo_address1', width: '200px', name: 'Address 1'},
                {field: 'supplierCo_postcode', width: '100px', name: 'Postcode'},
                {field: '', width: 'auto', name: ''}
            ]
        },

        supplierGridRowClick : function(selectedIndex)
        {
            if (typeof(selectedIndex) != 'number') {
                selectedIndex = this.focus.rowIndex;
            }

            selectedItem = this.getItem(selectedIndex);
            id = this.store.getValue(selectedItem, 'supplier_idSupplier');

             bba.openTab({
                tabId : 'supplier' + id,
                title : this.store.getValue(selectedItem, 'supplier_name'),
                url : '/supplier/edit-supplier',
                contentVars : {
                    type : 'details',
                    idSupplier : id
                }
            });
        },

        supplierCoGridRowClick : function(selectedIndex)
        {
            if (typeof(selectedIndex) != 'number') {
                selectedIndex = this.focus.rowIndex;
            }

            selectedItem = this.getItem(selectedIndex);
            id = this.store.getValue(selectedItem, 'supplierCo_idSupplierContact');

             if (!dom.byId('supplierCoForm')) {
                bba.openFormDialog({
                    url: '/supplier/edit-supplier-contact',
                    content: {
                        type :  'edit',
                        idSupplierContact : id
                    },
                    dialog: 'supplierCoForm'
                });
            } else {
                supplierCoForm.show();
            }
        },

        newSupplierButtonClick : function()
        {
            if (!dom.byId('supplierForm')) {
                bba.openFormDialog({
                    url: '/supplier/add-supplier',
                    content: {type :  'add'},
                    dialog: 'supplierForm'
                });
            } else {
                supplierForm.show();
            }
        },

        newSupplierCoButtonClick : function()
        {
            if (!dom.byId('supplierCoForm')) {
                bba.openFormDialog({
                    url: '/supplier/add-supplier-contact',
                    content: {
                        type :  'add',
                        supplierCo_idSupplier : this.value
                    },
                    dialog: 'supplierCoForm'
                });
            } else {
                supplierCoForm.show();
            }
        },

        editSupplierButtonClick : function()
        {
            if (!dom.byId('supplierForm')) {
                bba.openFormDialog({
                    url: '/supplier/edit-supplier',
                    content: {
                        type :  'edit',
                        idSupplier : this.value
                    },
                    dialog: 'supplierForm'
                });
            } else {
                supplierForm.show();
            }
        },

        processSupplierForm : function()
        {
            bba.closeDialog(supplierForm);

            values = arguments[0];
            values.idSupplier = values.supplier_idSupplier;

            xhr.post({
                url: '/supplier/save-supplier',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    if (data.saved > 0) {
                        if (values.idSupplier) {
                            registry.byId('supplier' + values.idSupplier).refresh();
                        } else {
                            registry.byId('supplierGrid')._refresh();
                        }
                    } else {
                        dom.byId('dialog').innerHTML = data.html;
                        parser.parse('dialog');
                        bba.setupDialog(supplierForm);
                        supplierForm.show();
                    }
                }
            });
        },

        processSupplierCoForm : function()
        {
            bba.closeDialog(supplierCoForm);

            values = arguments[0];
            values.idSupplierContact = values.supplierclientCo_idSupplierContact;

            if (values.idSupplierContact) {
                values.type = 'edit';
            }

            xhr.post({
                url: '/supplier/save-supplier-contact',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    if (data.saved > 0) {
                        registry.byId('supplierCoGrid' + values.supplierCo_idSupplier)._refresh();
                    } else {
                        dom.byId('dialog').innerHTML = data.html;
                        parser.parse('dialog');
                        bba.setupDialog(supplierCoForm);
                        supplierCoForm.show();
                    }
                }
            });
        }
    }

    return bba.Supplier;

});