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
[
    "dojo/dom",
    "dojo/parser",
    "dojo/_base/xhr",
    "dijit/registry",
    "bba/Core",
    "bba/Contract",
    "dijit/form/FilteringSelect"
],
    function(dom, parser, xhr, registry, core){

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
                {field: 'contract_type', width: '100px', name: 'Type'},
                {field: 'contract_status', width: '110px', name: 'Status'},
                {field: 'contract_dateStart', width: '80px', name: 'Start Date'},
                {field: 'contract_dateEnd', width: '80px', name: 'End Date'},
                {field: 'contract_reference', width: '200px', name: 'Reference'},
                {field: '', width: 'auto', name: ''}
            ],
            supplierPersonnel : [
                {field: 'supplierPers_idSupplierPersonnel', width: '50px', name: 'Id'},
                {field: 'supplierPers_type_tables', width: '120px', name: 'Type'},
                {field: 'supplierPers_name', width: '200px', name: 'Name'},
                {field: 'supplierPers_phone', width: '100px', name: 'Phone'},
                {field: 'supplierPers_email', width: '300px', name: 'Email'},
                {field: 'supplierPers_address1', width: '200px', name: 'Address 1'},
                {field: 'supplierPers_postcode', width: '100px', name: 'Postcode'},
                {field: '', width: 'auto', name: ''}
            ]
        },
        
        init : function()
        {
            core.addDataStore('supplierStore', core.storeUrls.supplier);

            core.addGrid({
                id : 'supplierGrid',
                store : core.dataStores.supplierStore,
                structure : bba.Supplier.gridLayouts.supplier,
                sortInfo : '2',
                onRowClick : function() {
                     bba.Supplier.supplierGridRowClick();
                }
            });
        },

        supplierGridRowClick : function(grid)
        {
            grid = (grid) ? grid : core.grids.supplierGrid;
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);
            id = grid.store.getValue(selectedItem, 'supplier_idSupplier');
            tabTitle = grid.store.getValue(selectedItem, 'supplier_name');

             bba.openTab({
                tabId : 'supplier' + id,
                title : (tabTitle) ? tabTitle : 'Supplier',
                url : './supplier/edit-supplier',
                content : {
                    type : 'details',
                    supplier_idSupplier : id
                }
            });
        },

        supplierPersGridRowClick : function(grid)
        {
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);
            id = grid.store.getValue(selectedItem, 'supplierPers_idSupplierPersonnel');

             if (!dom.byId('supplierPersForm')) {
                bba.openFormDialog({
                    url: './supplier/edit-supplier-personnel',
                    content: {
                        type :  'edit',
                        supplierPers_idSupplierPersonnel : id
                    },
                    dialog: 'supplierPersForm'
                });
            } else {
                supplierPersForm.show();
            }
        },

        newSupplierButtonClick : function()
        {
            if (!dom.byId('supplierForm')) {
                bba.openFormDialog({
                    url: './supplier/add-supplier',
                    content: {type :  'add'},
                    dialog: 'supplierForm'
                });
            } else {
                supplierForm.show();
            }
        },

        newSupplierPersButtonClick : function(contentVars)
        {
            if (!dom.byId('supplierPersForm')) {
                bba.openFormDialog({
                    url: './supplier/add-supplier-personnel',
                    content: dojo.mixin({type :  'add'}, contentVars),
                    dialog: 'supplierPersForm'
                });
            } else {
                supplierPersForm.show();
            }
        },

        editSupplierButtonClick : function(contentVars)
        {
            if (!dom.byId('supplierForm')) {
                bba.openFormDialog({
                    url: './supplier/edit-supplier',
                    content: dojo.mixin({type :  'edit'}, contentVars),
                    dialog: 'supplierForm'
                });
            } else {
                supplierForm.show();
            }
        },

        processSupplierForm : function()
        {
            //bba.closeDialog(supplierForm);
        	bba.pageStandby.show();

            values = arguments[0];
            values.type = (values.supplier_idSupplier) ? 'edit' : 'add';

            xhr.post({
                url: './supplier/save-supplier',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    dom.byId('dialog').innerHTML = data.html;
                    parser.parse('dialog');
                    bba.pageStandby.hide();

                    if (data.error) {
                        error.show();
                    } else if (data.saved > 0) {
                        if (values.supplier_idSupplier) {
                            registry.byId('supplier' + values.supplier_idSupplier).refresh();
                        } else {
                            registry.byId('supplierGrid')._refresh();
                        }

                        if (bba.config.confirmBox) {
                            confirm.show();
                        }
                    } else {
                        bba.setupDialog(supplierForm);
                        supplierForm.show();
                    }
                }
            });
        },

        processSupplierPersForm : function()
        {
            //bba.closeDialog(supplierCoForm);
        	bba.pageStandby.show();

            values = arguments[0];
            values.type = (values.supplierPers_idSupplierPersonnel) ? 'edit' : 'add';

            xhr.post({
                url: './supplier/save-supplier-personnel',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    dom.byId('dialog').innerHTML = data.html;
                    parser.parse('dialog');
                    bba.pageStandby.hide();

                    if (data.error) {
                        error.show();
                    } else if (data.saved > 0) {
                        registry.byId('supplierPersGrid' + values.supplierPers_idSupplier)._refresh();

                        if (bba.config.confirmBox) {
                            confirm.show();
                        }

                    } else {
                        bba.setupDialog(supplierPersForm);
                        supplierPersForm.show();
                    }
                }
            });
        }
    }

    return bba.Supplier;

});