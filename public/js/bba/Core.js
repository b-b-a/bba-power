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
    "dijit/form/Form", "dijit/form/Button"],
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
                //connect.connect(widget, 'onFocus', widget._startSearchAll);
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

