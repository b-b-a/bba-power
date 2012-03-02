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
    ["dojo/dom", "dojo/dom-construct","dojo/ready", "dojo/parser", "dojo/_base/connect",
        "dojo/_base/xhr", "dojo/_base/array", "dijit/registry", "dojo/cookie",
    "dijit/WidgetSet", "dijit/layout/ContentPane", "dijit/Dialog", "dojox/grid/DataGrid", "dijit/layout/StackContainer",
    "dijit/layout/BorderContainer", "dijit/layout/TabContainer", "dojox/data/QueryReadStore",
    "dijit/form/Form", "dijit/form/Button"],
    function(dom, domConstruct, ready, parser, connect, xhr, array, registry, cookie, WidgetSet, ContentPane, Dialog, DataGrid) {

    ready(function(){
        loader = dom.byId("loader");
        loader.style.display = "none";
        if (registry.byId('error')) error.show();

        if (registry.byId('tabRefreshButton')) {
            registry.byId('tabRefreshButton').set('checked', (cookie("tabRefresh") == 'false') ? false : true);
        }

    });

    DataGrid.extend({
        onRowClick: function(e){
            this.edit.rowClick(e);
        },
        onFetchError: function(err, req) {
            xhr.post({
                url: req.store.url,
                content: req.query,
                handleAs: 'text',
                preventCache: true,
                load: function(data) {
                    dom.byId('dialog').innerHTML = data;
                    parser.parse('dialog');
                    dialog = registry.byClass("dijit.Dialog").toArray()[0];

                    dialog = (!dialog) ? bba.errorDialog(data) : dialog;

                    dialog.show();
                },
                error: function(error) {
                    bba.showXhrError(error);
                }
            });
        }
    });

    bba = {
        gridMessage : '<span class="dojoxGridNoData">No records found matching query</span>',

        tabRefreshButton : function(val)
        {
            cookie("tabRefresh", val, {expires: 5});
            tabs = registry.byId("ContentTabs").getChildren();
            array.forEach(tabs, function(tab){
                tab.refreshOnShow = (val) ? true : false;
            });
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

        tabs : [],

        openTab : function(options)
        {
            var tc = registry.byId("ContentTabs");

            if (!registry.byId(options.tabId)) {

                var pane = new ContentPane({
                    id: options.tabId,
                    title: options.title,
                    href: options.url,
                    ioMethod: xhr.post,
                    ioArgs: {content : options.content},
                    closable: true,
                    refreshOnShow: (cookie("tabRefresh") == 'false') ? false : true,
                    onLoad : function() {
                        pos = array.indexOf(bba.tabs, options.tabId);
                        if (pos == -1) bba.tabs.push(options.tabId);

                        if (registry.byId('login')) return login.show();

                        pattern = /Fatal error/;
                        if (pattern.test(dom.byId(options.tabId).innerHTML)) {
                            txt = dom.byId(options.tabId).innerHTML;
                            dom.byId(options.tabId).innerHTML = '';
                            node = domConstruct.create("pre", {
                                innerHTML: txt
                            }, dom.byId(options.tabId));
                        }
                    },
                    onClose : function() {
                        array.forEach(bba.tabs, function(item, i) {
                            if (item == options.tabId) {
                                bba.tabs.splice(i, 1);
                            }
                        });

                        if (bba.tabs.length > 0) {
                            tc.selectChild(bba.tabs[bba.tabs.length - 1]);
                        }

                        return true;
                    },
                    onDownloadError : function(error) {
                        xhr.post({
                            url: options.url,
                            content: options.content,
                            handleAs: 'text',
                            preventCache: true,
                            error: function(error) {
                                bba.showXhrError(error);
                            }
                        });
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

                    if (dialog) {
                        bba.setupDialog(dialog);
                    } else if (!registry.byId('login')) {
                        dialog = bba.errorDialog(data);
                    } else {
                        dialog = registry.byId('login');
                    }

                    dialog.show();
                },
                error: function(error) {
                    bba.showXhrError(error);
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

        errorDialog : function(data)
        {
            data = domConstruct.create("pre", {innerHTML: data});
            return new Dialog({
                title : 'BBA System Error',
                content : data,
                onHide : function() {
                    bba.closeDialog(dialog);
                }
            });
        },

        showXhrError : function(data)
        {
            dom.byId('dialog').innerHTML = data.responseText;
            parser.parse('dialog');
            error.show();
        }
    };

    return bba;

});

