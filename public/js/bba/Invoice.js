/*
 * Invoice.js
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

define("bba/Invoice",
    ["dojo/dom", "dojo/ready", "dojo/parser", "dojo/_base/xhr", "dijit/registry", "bba/Core",
    "dijit/form/ValidationTextBox"],
    function(dom, ready, parser, xhr, registry, bba){

    ready(function () {

        if (dom.byId('invoice')) {
            dom.byId('invoice').focus();
        }

        if (dom.byId('invoiceGrid')) {
            var form = registry.byId('Search');
            if (form) bba.gridSearch(form, invoiceGrid);
        }
    });

    bba.Invoice = {
        gridLayouts : {
            invoice : [
                {field: 'invoice_idInvoice', width: '50px', name: 'Id'},
                {field: 'invoice_type', width: '100px', name: 'Type'},
                {field: 'invoice_nameSupplier', width: '200px', name: 'Supplier'},
                {field: 'invoice_dateInvoice', width: '150px', name: 'Invoice Date'},
                {field: 'invoice_amountTotal', width: '100px', name: 'Total'},
                {field: 'invoice_refSupplier', width: '150px', name: 'Supplier Ref'},
                {field: 'invoice_numberInvoice', width: '100px', name: 'Invoice No.'},
                {field: '', width: 'auto', name: ''}
            ]
        },

        invoiceGridRowClick : function(grid)
         {
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);
            id = grid.store.getValue(selectedItem, 'invoice_idInvoice');
            tabTitle = grid.store.getValue(selectedItem, 'invoice_numberInvoice');

             bba.openTab({
                tabId : 'client' + id,
                title : (tabTitle) ? tabTitle : 'Invoice',
                url : './invoice/view-invoice',

                content : {
                    type : 'details',
                    invoice_idInvoice : id
                }
            });
        }
    }

    return bba.Invoice;

});
