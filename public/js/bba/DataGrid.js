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
dojo.extend(
    dojox.grid.DataGrid,
    {
        autoWidth : false,
        selectionMode : 'single',
        clientSort : true,
        noDataMessage : '<span class="dojoxGridNoData">No records found matching query</span>',
        abrev : {
            ad : "address",
            co : "contact"
        },
        search : false,
        tabs : false,
        tabTitle: '',
        tabTitleColumn : '',
        dialog : false,
        dialogName : '',
        dlg : null,
        queryParent : '',
        newButtonId : null,

        _onFetchComplete : function(items, req)
        {
            if(!this.scroller){return;}

            if(items && items.length > 0){
                dojo.forEach(items, function(item, idx){
                    this._addItem(item, req.start+idx, true);
                }, this);

                if(this._autoHeight){
                    this._skipRowRenormalize = true;
                }

                this.updateRows(req.start, items.length);

                if(this._autoHeight){
                    this._skipRowRenormalize = false;
                }

                if(req.isRender){
                    this.setScrollTop(0);
                    this.postrender();
                }else if(this._lastScrollTop){
                    this.setScrollTop(this._lastScrollTop);
                }
            }

            delete this._lastScrollTop;

            if(!this._isLoaded){
                this._isLoading = false;
                this._isLoaded = true;
            }

            this._pending_requests[req.start] = false;

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

            if (!dijit.byId(tabId)) {

                var pane = new dijit.layout.ContentPane({
                    id: tabId,
                    title: this.store.getValue(this.selectedItem, this.tabTitleColumn),
                    href: '/' + this.hyphenate(identParts[0]) + '/edit',
                    ioArgs: { content:contentVars },
                    closable: true,
                    onLoad : dojo.hitch(this, function() {
                        this.editForm();
                        this.tab = pane;
                    }),
                    onShow : function() {
                        this.refresh();
                        this.tab = pane;
                    }
                });

                tc.addChild(pane);
            }

            tc.selectChild(tabId);
        },

        editForm : function()
        {
            var identParts = this.getIdentParts();
            var id = 'edit-' + identParts[0] + '-' + this.getId();

            dojo.connect(dijit.byId(id), 'onSubmit', dojo.hitch(this, function(e){
                dojo.stopEvent(e);
                this.showDialog('edit');
            }));
        },

        newChildForm : function()
        {
            var identParts = this.getIdentParts();
            var selectedId = (this.newButtonId === null) ? this.query[this.queryParent] : this.newButtonId;
            var id = 'new-' + identParts[0] + '-button-' + selectedId;

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

        showDialog : function(type)
        {
            if (!this.dlg) {

                var identParts = this.getIdentParts();
                var contentVars = {type: type};

                if (this.selectedItem) {
                    var id = this.getId();
                    contentVars[identParts[1]] = id;
                }

                if (this.queryParent) {
                    contentVars[this.queryParent] = this.query[this.queryParent];
                }

                this.dlg = new dijit.Dialog({
                    title: (this.dialogName) ? this.dialogName :
                        this.capitalize(type + ' ' + this.hyphenate(identParts[0]).replace('-', ' ')),
                    style: "width:500px;",
                    ioArgs: {content: contentVars},
                    href: '/' + this.hyphenate(identParts[0]) + '/' + type,
                    execute: dojo.hitch(this, function() {
                        var url = '/' + this.hyphenate(identParts[0]) + '/save';
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
                    })
                });

                dojo.connect(this.dlg, 'onLoad', dojo.hitch(this, function(){
                    dojo.connect(
                        dijit.byId(identParts[0] + 'FormCancelButton'),
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

            form.cancel = null;
            form.type = type;

            dojo.xhrPost({
              url: url,
              content: form,
              handleAs: 'json',
              preventCache: true,
              load: dojo.hitch(this, function(data) {
                  if (data.saved > 0) {
                      if (this.tab) {
                          this.tab.refresh();
                      } else {
                          this._refresh();
                      }
                  } else {
                      this.dlg = new dijit.Dialog({
                        title: this.dialogName,
                        style: "width:500px;",
                        content: data.html,
                        execute: dojo.hitch(this, function() {
                            var url = '/' + this.hyphenate(identParts[0]) + '/save';
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
                                dijit.byId(identParts[0] + 'FormCancelButton'),
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

