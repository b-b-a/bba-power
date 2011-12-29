/*
 * DataGrid.js
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
 * @subpackage DataGrid
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */

/**
 * DataGrid.
 *
 * @category   BBA
 * @package    JavaScript
 * @subpackage DataGrid
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
dojo.provide("bba.DataGrid");
dojo.require("dojox.grid.DataGrid");

dojo.declare(
    "bba.DataGrid",
    dojox.grid.DataGrid,
    {
        autoWidth : false,
        selectionMode : 'single',
        clientSort : true,
        noDataMessage : '<span class="dojoxGridNoData">No records found matching query</span>',
        abrev : {
            ad : "address",
            co : "contact",
            contract : "contract"
        },
        search : false,
        tabs : false,
        tabTitle : '',
        tabController : '',
        tabTitleColumn : '',
        tabId : null,
        dialog : false,
        dialogName : '',
        dlg : null,
        queryParent : '',
        newButton : '',
        newButtonId : '',
        newButtonController : '',
        controller : '',

        _onFetchComplete : function(items, req)
        {
            this.inherited(arguments);

            dojo.connect(this, "onRowClick", function() {
                this.gridRowClick();
            });

            if (this.search) this.gridSearch();

            if (this.queryParent === '') {
                this.newParentForm();
            } else {
                this.newChildForm();
            }
        },

        getIdent : function()
        {
            var ident = this.store._identifier;

            if (!ident) {
                ident = this.store.getFeatures()["dojo.data.api.Identity"];
            }

            return ident;
        },

        getIdentParts : function()
        {
          var i = this.getIdent();
          return i.split("_");
        },

        getId : function()
        {
            return this.store.getValue(this.selectedItem, this.getIdent());
        },

        getController : function()
        {
            return (this.tabController != '') ? this.tabController : this.hyphenate(this.getIdentParts()[0]);
        },

        getNewController : function()
        {
            return (this.newButtonController != '') ? this.newButtonController : this.getController();
        },

        gridRowClick : function(selectedIndex)
        {
            if (selectedIndex == null) {
                selectedIndex = this.focus.rowIndex;
            }

            this.selectedItem = this.getItem(selectedIndex);

            if (this.dialog === true) {
                this.showDialog('edit');
            } else {
                this.openTab();
            }
        },

        openTab : function()
        {
            var identParts = this.getIdentParts();
            var id = this.getId();

            var contentVars = {type: 'details'};
            contentVars[identParts[1]] = id;

            var tabId = identParts[0] + id;
            var tc = dijit.byId("ContentTabs");

            var url = (this.tabController != '') ?
                '/' + this.tabController + '/edit-' + this.getIdentParts()[0] :
                '/' + this.getController().split('-')[0] + '/edit-' + this.getController();

            if (!dijit.byId(tabId)) {

                var pane = new dijit.layout.ContentPane({
                    id: tabId,
                    title: this.store.getValue(this.selectedItem, this.tabTitleColumn),
                    href: url,
                    ioArgs: { content:contentVars },
                    closable: true,
                    refreshOnShow: true,
                    onLoad : dojo.hitch(this, function() {
                        this.editForm(id);
                        this.tab = pane;
                    }),
                    onHide : function() {
                        tc.prevTab = pane;
                    }
                });

                tc.addChild(pane);
            }

            tc.selectChild(tabId);
        },

        editForm : function(tabId)
        {
            var identParts = (this.tabController != '') ? this.tabController : this.getIdentParts()[0];
            if (this.newButton != '') identParts = this.newButton;
            var id = 'edit-' + identParts + '-' + tabId;

            console.log(id)

            dojo.connect(dijit.byId(id), 'onSubmit', dojo.hitch(this, function(e){
                dojo.stopEvent(e);
                this.showDialog('edit', identParts, tabId);
            }));
        },

        newChildForm : function()
        {
            var selectedId = (!this.newButtonId) ? this.query[this.queryParent] : this.newButtonId;

            var con = (this.tabController != '') ? this.tabController : this.getNewController();
            if (this.newButton !== '') con = this.newButton;
            var id = 'new-' + con + '-button-' + selectedId;
            console.log(id)

            dojo.connect(dijit.byId(id), 'onClick', dojo.hitch(this, function(e){
                dojo.stopEvent(e);

                this.showDialog('add');
            }));
        },

        newParentForm : function()
        {
            var identParts = this.getIdentParts();
            var id = 'new-' + identParts[0] + '-button';

            dojo.connect(dijit.byId(id), 'onClick', dojo.hitch(this, function(e){
                dojo.stopEvent(e);
                this.tab = dijit.byId(identParts[0] + '-list');
                this.showDialog('add');
            }));
        },

        showDialog : function(type, controller, tabId)
        {
            if (!this.dlg) {

                var identParts = this.getIdentParts();
                var contentVars = {type: type};

                if (this.selectedItem) {
                    var id = (tabId) ? tabId : this.getId();
                    contentVars[identParts[1]] = id;
                }

                if (this.queryParent) {
                    contentVars = dojo.mixin(contentVars, this.query);
                }

                var con =  (controller) ? controller : this.hyphenate(this.getNewController());

                var urlCon = (this.controller != '') ?
                        this.controller : this.hyphenate(con).split('-')[0];

                this.dlg = new dijit.Dialog({
                    id: type + identParts[0],
                    title: (this.dialogName) ? this.dialogName :
                        this.capitalize(type + ' ' + con.replace('-', ' ')),
                    ioArgs: {content: contentVars},
                    href: '/' + urlCon + '/' + type + '-' + this.hyphenate(con),
                    execute: dojo.hitch(this, function() {
                        var url = '/' + urlCon + '/save-' + this.hyphenate(con);
                        this.dlg.destroyRecursive();
                        this.dlg = null;

                        this.processForm(arguments[0], url, type);
                    }),
                    _onSubmit: dojo.hitch(this.dlg, function() {
                        if (!this.validate()) return false;
                        this.onExecute();
                        this.execute(this.get('value'));
                    }),
                    onHide: dojo.hitch(this, function() {
                        this.dlg.destroyRecursive();
                        this.dlg = null;
                    }),
                    onShow: dojo.hitch(this.dlg, function(){
                        this.set("dimensions", [400, 200]);
                        this.layout();
                    })
                });

                dojo.connect(this.dlg, 'onLoad', dojo.hitch(this, function(){
                    dojo.connect(
                        dijit.byId(this.hyphenate(con) + 'FormCancelButton'),
                        "onClick",
                        dojo.hitch(this.dlg, 'hide')
                    );

                    var selects = dijit.registry.byClass("dijit.form.FilteringSelect");
                    selects.forEach(function(widget){
                        dojo.connect(widget, 'onClick', function(){
                            widget._startSearchAll();
                        });
                        dojo.connect(widget, 'onFocus', function(){
                            widget._startSearchAll();
                        });
                    });
                }));
            }

            this.dlg.show();
        },

        processForm : function(form, url, type)
        {
            var identParts = this.getIdentParts();
             if (this.selectedItem) {
                var id = this.getId();
                form[identParts[1]] = id;
            }

            if (form.cancel) form.cancel = null;
            form.type = type;

            dojo.xhrPost({
              url: url,
              content: form,
              handleAs: 'json',
              preventCache: true,
              load: dojo.hitch(this, function(data) {
                  if (data.saved > 0) {
                      if (this.tab && this.tab.id.split('-')[1] !== 'list') {
                          this.tab.refresh();
                      } else {
                          this._refresh();
                      }
                      console.log(data);
                  } else {
                      this.dlg = new dijit.Dialog({
                        title: (this.dialogName) ? this.dialogName :
                        this.capitalize(type + ' ' + this.hyphenate(this.getNewController()).replace('-', ' ')),
                        style: "width:500px;",
                        content: data.html,
                        execute: dojo.hitch(this, function() {
                            //var url = '/' + this.hyphenate(this.getNewController()) + '/save';
                            this.dlg.destroyRecursive();
                            this.dlg = null;
                            this.processForm(arguments[0], url, type);
                        }),
                        _onSubmit: dojo.hitch(this.dlg, function() {
                            if (!this.validate()) return false;
                            this.onExecute();
                            this.execute(this.get('value'));
                        }),
                        onShow: dojo.hitch(this, function() {
                            dojo.connect(
                                dijit.byId(this.getNewController() + 'FormCancelButton'),
                                "onClick",
                                dojo.hitch(this.dlg, 'hide')
                            );

                            var selects = dijit.registry.byClass("dijit.form.FilteringSelect");
                            selects.forEach(function(widget){
                                dojo.connect(widget, 'onClick', function(){
                                    widget._startSearchAll();
                                });
                                dojo.connect(widget, 'onFocus', function(){
                                    widget._startSearchAll();
                                });
                            });
                        }),
                        onHide: dojo.hitch(this, function() {
                            this.dlg.destroyRecursive();
                            this.dlg = null;
                        })
                    });

                    this.dlg.show();
                  }
              })
          });
        },

        gridSearch : function()
        {
            var form = dijit.byId('Search');
            if (form) {
                dojo.connect(form, 'onSubmit', dojo.hitch(this, function(e){
                    e.preventDefault();
                    var values = form.getValues();
                    delete values.reset;
                    delete values.submit;
                    this.setQuery(values);
                }));
            }
        },

        capitalize: function(string){
            return string.replace(/\b[a-z]/g, function(match){
                return match.toUpperCase();
            });
        },

        hyphenate : function(str)
        {
            str = str.replace(/[A-Z]/g, function(match) {
                return ('-{' + match.charAt(0).toLowerCase());
            });
            if (str.match('{')) str = str + '}';
            return dojo.replace(str, this.abrev);
        }
    }
);

bba.DataGrid.cell_markupFactory = function(cellFunc, node, cellDef){
	var field = dojo.trim(dojo.attr(node, "field")||"");
	if(field){
		cellDef.field = field;
	}
	cellDef.field = cellDef.field||cellDef.name;
	var fields = dojo.trim(dojo.attr(node, "fields")||"");
	if(fields){
		cellDef.fields = fields.split(",");
	}
	if(cellFunc){
		cellFunc(node, cellDef);
	}
};

bba.DataGrid.markupFactory = function(props, node, ctor, cellFunc){
	return dojox.grid._Grid.markupFactory(props, node, ctor,
					dojo.partial(dojox.grid.DataGrid.cell_markupFactory, cellFunc));
};

