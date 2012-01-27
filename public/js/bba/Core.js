/*
 * Core.js
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
 * @subpackage Core
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * Core.
 *
 * @category   BBA
 * @package    JavaScript
 * @subpackage Core
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
define("bba/Core",
    ["dojo/dom","dojo/ready", "dojo/parser", "dojo/_base/connect", "dojo/_base/xhr", "dijit/registry",
    "dijit/WidgetSet", "dijit/layout/ContentPane", "dijit/Dialog", "dojox/grid/DataGrid", "dijit/layout/StackContainer",
    "dijit/layout/BorderContainer", "dijit/layout/TabContainer", "dojox/data/QueryReadStore",
    "dijit/form/Form", "dijit/form/Button", "dojox/grid/_CheckBoxSelector"],
    function(dom, ready, parser, connect, xhr, registry, WidgetSet, ContentPane, Dialog, DataGrid) {

    ready(function(){
        loader = dom.byId("loader");
        loader.style.display = "none";
    });

    DataGrid.extend({
        onRowClick: function(e){
            this.edit.rowClick(e);
        }
    })

    bba = {
        gridMessage : '<span class="dojoxGridNoData">No records found matching query</span>',

        gridLayouts : {
            siteMeter : [
                {field: 'meter_idMeter', width: '50px', name: 'Id'},
                {field: 'meter_type', width: '150px', name: 'Type'},
                {field: 'meter_status', width: '150px', name: 'Status'},
                {field: 'meter_numberSerial', width: '200px', name: 'Number Serial'},
                {field: 'meter_numberMain', width: '200px', name: 'Number Main'},
                {field: '', width: 'auto', name: ''}
            ],
            meterContract : [
                {
                    type: "dojox.grid._CheckBoxSelector"
                },
                [
                    {field: 'meter_idMeter', width: '50px', name: 'Id'},
                    {field: 'meter_numberMain', width : '150px', name: 'Number Main'},
                    {field: 'meter_type', width : '100px', name: 'Meter Type'},
                    {field: 'meterContract_kvaNominated', width: '120px', name: 'Expected Yearly Consumption', editable: true},
                    {field: 'contract_idContract', width: '100px', name: 'Contract Id'},
                    {field: 'contract_type', width: '100px', name: 'Contract Type'},
                    {field: 'contract_status', width: '100px', name: 'Status'},
                    {field: 'contract_dateStart', width: '100px', name: 'Start Date'},
                    {field: 'contract_dateEnd', width: '100px', name: 'End Date'},
                    {field: '', width: 'auto', name: ''}
                ]
            ],
            meterUsage : [
                {field: 'usage_idUsage', width: '50px', name: 'Id'},
                {field: 'usage_dateBill', width: '100px', name: 'Bill Date'},
                {field: 'usage_dateReading', width: '100px', name: 'Reading Date'},
                {field: 'usage_type', width: '100px', name: 'type'},
                {field: 'usage_usageDay', width: '100px', name: 'Day'},
                {field: 'usage_usageNight', width: '100px', name: 'Night'},
                {field: 'usage_usageOther', width: '100px', name: 'Other'},
                {field: '', width: 'auto', name: ''}
            ],
            supplierContract : [
                {field: 'contract_idContract', width: '50px', name: 'Id'},
                {field: 'client_name', width: '300px', name: 'Client'},
                {field: 'contract_type', width: '150px', name: 'Type'},
                {field: 'contract_status', width: '100px', name: 'Status'},
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

        gridSearch : function(form, grid)
        {
            connect.connect(form, 'onSubmit', function(e) {
                e.preventDefault();
                var values = form.getValues();
                delete values.reset;
                delete values.submit;
                grid.setQuery(values);
            });
        },

        openTab : function(options)
        {
            var tc = registry.byId("ContentTabs");

            if (!registry.byId(options.tabId)) {

                var pane = new ContentPane({
                    id: options.tabId,
                    title: options.title,
                    href: options.url,
                    ioMethod: xhr.post,
                    ioArgs: {content : options.contentVars},
                    closable: true,
                    refreshOnShow: true,
                    onLoad : function() {
                        //this.tabs = pane;
                    },
                    onHide : function() {
                        //tc.prevTab = pane;
                    },
                    onContentError : function(error) {
                        console.log(error);
                    }
                });

                tc.addChild(pane);
            }

            tc.selectChild(options.tabId);
        },

        openFormDialog : function(options)
        {
            xhr.post({
                url: options.url,
                content: options.content,
                handleAs: 'text',
                preventCache: true,
                load: function(data) {
                    dom.byId('dialog').innerHTML = data;
                    parser.parse('dialog');
                    dialog = registry.byId(options.dialog);
                    bba.setupDialog(dialog);
                    dialog.show();
                }
            });
        },

        setupDialog : function(dialog)
        {
            ws = new WidgetSet();
            ws.add(dialog);
            selects = registry.byClass("dijit.form.FilteringSelect");
            selects.forEach(function(widget){
                connect.connect(widget, 'onClick', widget._startSearchAll);
                connect.connect(widget, 'onFocus', widget._startSearchAll);
            });
            connect.connect(dialog, 'onHide', function() {
                bba.closeDialog(dialog);
            });
        },

        closeDialog : function(dialog)
        {
            dialog.hide();
            dialog.destroyRecursive();
        },

        comfirmDialog : function()
        {
            dialog = new Dialog({
                content: '<p>Done</p>',
                onHide: function() {
                    this.destroyRecursive();
                }
            });

            dialog.show();
        }
    };

    return bba;

});

