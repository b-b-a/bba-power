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
 * Core.js
 *
 * @category   BBA
 * @package    JavaScript
 * @subpackage Core
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
String.prototype.trunc = function()
{
    return this.substr(0,20);
};

define("bba/Core",
[
    "dojo/dom",
    "dojo/dom-construct",
    "dojo/ready",
    "dojo/parser",
    "dojo/_base/connect",
    "dojo/_base/xhr",
    "dojo/_base/array",
    "dojo/_base/lang",
    "dijit/registry",
    "dojo/cookie",
    "dojo/json",
    "dijit/WidgetSet",
    "dijit/layout/ContentPane",
    "dijit/Dialog",
    "dojox/grid/DataGrid",
    "dojox/data/QueryReadStore",
    "dojox/form/Uploader",
    "dojox/form/uploader/plugins/IFrame",
    "dijit/layout/StackContainer",
    "dijit/layout/BorderContainer",
    "dijit/layout/TabContainer",
    "dijit/form/Form",
    "dijit/form/Button",
    "dojox/widget/Standby"
],
    function(dom, domConstruct, ready, parser, connect, xhr, array, lang, registry, cookie, json, WidgetSet, ContentPane, Dialog, DataGrid, QueryReadStore, IFrame) {

    if (bbaModule != 'Auth') {
        ready(function () {
            bba.init();
        });
    }

    bba = {
        gridMessage : '<span class="dojoxGridNoData">No records found matching query</span>',

        config : {
            tabRefresh : true,
            confirmBox : true
        },

        tabs : [],

        tabPrefix : {
            client          : 'CL-',
            clientAd        : 'CA-',
            clientPers      : 'CP-',
            contract        : 'C-',
            invoice         : 'I-',
            invoiceLine     : 'IL-',
            invoiceUsage    : 'IU-',
            meter           : 'M-',
            meterContract   : 'MC-',
            site            : 'S-',
            supplier        : 'SU-',
            supplierPers    : 'SUP-',
            tender          : 'T-',
            usage           : 'U-'
        },

        deferredFunction : function() {},

        storeUrls : {
            client : './client/data-store/type/client',
            contract : './contract/data-store/type/contract',
            invoice : './invoice/data-store/type/invoice',
            invoiceLines : './invoice/data-store/type/invoice-lines',
            meter : './meter/data-store/type/meter',
            site : './site/data-store/type/site',
            supplier : './supplier/data-store/type/suppliers',
            user : './user/user-store'
        },

        dataStores : {},

        grids : {},

        init : function()
        {
        	if( bbaModule != 'Error' ){
	            if (lang.isFunction(bba[bbaModule].init)) {
	                this[bbaModule].init();
	            }
	
	            dom.byId(bbaModule.toLowerCase()).focus();
        	

	            this.gridSearch(
	                registry.byId('Search'),
	                registry.byId(bbaModule.toLowerCase() + 'Grid')
	            );
	            
	            if (cookie("bba-prefs")) {
                    this.config = json.parse(cookie("bba-prefs"));
                }
    
                for (id in bba.config) {
                    registry.byId(id+'Button').set(
                        'checked', this.config[id]
                    );
                }
    
                dom.byId("dojoVersion").innerHTML = 'dojo ' + dojo.version.toString();
        	}

            this.pageLoaded();
        },

        pageLoaded : function()
        {
        	bba.pageStandby.hide();
        	
            if (registry.byId('error')) {
                error.show();
            }
        },
        
        pageStandby : {
        	show : function()
        	{
        		loader = dom.byId("loader");
                loader.style.display = "block";
        	},
        	
        	hide : function()
        	{
        		loader = dom.byId("loader");
                loader.style.display = "none";
        	}
        },

        setPref : function(pref, val)
        {
            id = pref.get('id').replace('Button', '');
            this.config[id] = val;
            pref.set('checked', val);
            cookie('bba-prefs', json.stringify(this.config), {expires: 5});

            if ('tabRefresh' === id) {
                tabs = registry.byId("ContentTabs").getChildren();
                array.forEach(tabs, function(tab){
                    tab.refreshOnShow = val;
                });
            }
        },

        addDataStore : function(id, url)
        {
            this.dataStores[id] = new QueryReadStore({
                url : url,
                requestMethod : 'post'
            });
        },

        addGrid : function(options)
        {
            this.grids[options.id] = new DataGrid(lang.mixin({
                noDataMessage : this.gridMessage
            }, options), dom.byId(options.id));

            this.grids[options.id].startup();
            
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

                for (x in options.content) {
                    if (x != 'type') {
                        prefix = x.split('_');
                    }
                }

                options.title = this.tabPrefix[prefix[0]] + options.title;

                var pane = new ContentPane({
                    id: options.tabId,
                    title: options.title.trunc(),
                    href: options.url,
                    ioMethod: xhr.post,
                    ioArgs: {content : options.content},
                    closable: true,
                    refreshOnShow: this.config.tabRefresh,
                    onLoad : function() {
                        pos = array.indexOf(bba.tabs, options.tabId);
                        if (pos == -1) bba.tabs.push(options.tabId);

                        if (registry.byId('login')) return login.show();
                        if (registry.byId('error')) return error.show();

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
                    }
                });

                tc.addChild(pane);
            }

            tc.selectChild(options.tabId);
            tc.layout();
        },

        openFormDialog : function(options)
        {
        	bba.pageStandby.show();
        	
            def = xhr.post({
                url: options.url,
                content: options.content,
                handleAs: 'text',
                preventCache: true,
                load: function(data) {
                    dom.byId('dialog').innerHTML = data;
                    parser.parse('dialog');
                    dialog = registry.byId(options.dialog);
                    bba.pageStandby.hide();
                    
                    if (registry.byId('error')) return error.show();

                    if (dialog) {
                        bba.setupDialog(dialog);
                    } else if (!registry.byId('login')) {
                        dialog = bba.errorDialog(data);
                    } else {
                        dialog = registry.byId('login');
                    }

                    dialog.show();
                },
                error: function(data) {
                	bba.showXhrError(data.xhr.responseText);
                }
            });

            def.then(options.deferredFunction);
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

        closeDialog : function(dialog, funct)
        {
            dialog.hide();
            dialog.destroyRecursive();

            if (funct) funct;
        },
        
        /**
         * Main process form function to try to optimise the js code better.
         * This will reduced the code performance and make maintaince easier.
         * 
         * @todo performace tweeks
         */
        processForm : function()
        {
        	
        },
        
        showXhrError : function(data)
        {
        	bba.pageStandby.hide();
            dom.byId('errorDialog').innerHTML = data;
            parser.parse('errorDialog');
            error.show();
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

        dataStoreError : function(requestUrl, query)
        {
            xhr.post({
                url: requestUrl,
                content: query,
                handleAs: 'text',
                preventCache: true,
                load: function(data) {
                    dom.byId('errorDialog').innerHTML = data;
                    parser.parse('errorDialog');

                    pattern = /Fatal error/;
                    if (pattern.test(data)) {
                        dialog = bba.errorDialog(data);
                    } else {
                        dialog = (registry.byId("login")) ? registry.byId("login") : registry.byId("error");
                    }

                    dialog.show();
                }
            });
        },

        docFileList : function(fileArray, id)
        {
            dom.byId(id).innerHTML = fileArray[0].name;
        },
        
        clearCache : function()
        {
        	xhr.post({
                url: './clear-cache',
                handleAs: 'text',
                preventCache: true,
                load: function(data) {
                    dom.byId('dialog').innerHTML = data;
                    parser.parse('dialog');
                    dialog = registry.byId('confirm');

                    bba.setupDialog(dialog);

                    dialog.show();
                }
            });
        }
    };

    DataGrid.extend({
        onRowClick: function(e){
            this.edit.rowClick(e);
        },
        onFetchError: function(){
            req = arguments[1];
            bba.dataStoreError(req.store.url, req.query);
        }
    });
    
    IFrame.extend({
    	uploadIFrame: function(data){
    		console.log(data);
    		return false;
    	}
    });

    return bba;

});

