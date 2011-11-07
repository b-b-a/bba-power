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
        dlg : [],
        queryParent : '',

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

            if (this.queryParent === '') this.newParentForm();
        },

        getIdent : function()
        {
            var ident = this.store._identifier;

            if (!ident) {
                ident = this.store.getFeatures()["dojo.data.api.Identity"];
            }

            return ident;
        },

        gridRowClick : function(selectedIndex)
        {
            if (selectedIndex == null) {
                selectedIndex = this.focus.rowIndex;
            }

            selectedItem = this.getItem(selectedIndex);

            var ident = this.getIdent();

            i = ident.split("_");

            selectedId = this.store.getValue(selectedItem, ident);

            if (this.dialog === true) {
                this.showDialog(selectedId, i[1], i[0], 'edit');
            } else {
                this.tabTitle = this.store.getValue(selectedItem, this.tabTitleColumn);
                this.openTab(selectedId, i[1], i[0]);
            }
        },

        openTab : function(selectedId, inputName, name)
        {
            var contentVars = {type: 'details'};
            contentVars[inputName] = selectedId
            var tabId = name + selectedId;
            var tc = dijit.byId("ContentTabs");

            if (!dijit.byId(tabId)) {

                var pane = new dijit.layout.ContentPane({
                    id: tabId,
                    title: this.tabTitle,
                    href: '/' + this.hyphenate(name) + '/edit',
                    ioArgs: { content: contentVars },
                    closable: true,
                    onLoad : dojo.hitch(this, function() {
                        this.editForm(selectedId, inputName, name);
                        this.newChildForm(selectedId, inputName, name);
                    }),
                    onShow : function() {
                        this.refresh();
                    }
                });

                tc.addChild(pane);
            }

            tc.selectChild(tabId);
        },

        editForm : function(selectedId, inputName, name)
        {
            var id = 'edit-' + name + '-' + selectedId;

            dojo.connect(dijit.byId(id), 'onSubmit', dojo.hitch(this, function(e){
                dojo.stopEvent(e);
                this.tab = dijit.byId(name + selectedId);
                this.showDialog(selectedId, inputName, name, 'edit');
            }));
        },

        newChildForm : function(selectedId, inputName, name)
        {

        },

        newParentForm : function()
        {
            // this.getIdent(), this.query, this.queryParent
            var i = this.getIdent().split("_");
            var id = 'new-' + i[0] + '-button';

            dojo.connect(dijit.byId(id), 'onClick', dojo.hitch(this, function(e){
                dojo.stopEvent(e);
                this.tab = dijit.byId(i[0] + '-list');
                this.showDialog(null, i[1], i[0], 'add');
            }));
        },

        showDialog : function(selectedId, inputName, name, type)
        {
            if (!this.dlg[name]) {

                var contentVars = {type: type};
                contentVars[inputName] = selectedId

                if (this.queryParent) {
                    contentVars[this.queryParent] = this.query[this.queryParent];
                }

                this.dlg[name] = new dijit.Dialog({
                    title: (this.dialogName) ? this.dialogName : this.capitalize(type + ' ' + this.hyphenate(name).replace('-', ' ')),
                    style: "width:500px;",
                    ioArgs: { content: contentVars },
                    href: '/' + this.hyphenate(name) + '/' + type,
                    execute: dojo.hitch(this, function() {
                        var url = '/' + this.hyphenate(name) + '/save';
                        this.dlg[name].destroyRecursive();
                        this.dlg[name] = null;
                        this.processForm(arguments[0], url, selectedId, inputName, name, type);
                    }),
                    _onSubmit: dojo.hitch(this.dlg[name], function() {
                        if (!this.validate()) return false;
                        this.onExecute();
                        this.execute(this.get('value'));
                    }),
                    onHide: dojo.hitch(this, function() {
                        this.dlg[name].destroyRecursive();
                        this.dlg[name] = null;
                    })
                });

                dojo.connect(this.dlg[name], 'onLoad', dojo.hitch(this, function(){
                    dojo.connect(
                        dijit.byId(name + 'FormCancelButton'),
                        "onClick",
                        dojo.hitch(this.dlg[name], 'hide')
                    );
                }));
            }

            this.dlg[name].show();
        },

        processForm : function(form, url, selectedId, inputName, name, type)
        {
            form.cancel = null;
            form[inputName] = selectedId;
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
                      this.dlg[name] = new dijit.Dialog({
                        title: this.dialogName,
                        style: "width:500px;",
                        content: data.html,
                        execute: dojo.hitch(this, function() {
                            var url = '/' + this.hyphenate(name) + '/save';
                            this.dlg[name].destroyRecursive();
                            this.dlg[name] = null;
                            this.processForm(arguments[0], url, selectedId, inputName, name, type);
                        }),
                        _onSubmit: dojo.hitch(this.dlg[name], function() {
                            if (!this.validate()) return false;
                            this.onExecute();
                            this.execute(this.get('value'));
                        }),
                        onShow: dojo.hitch(this, function() {
                            dojo.connect(
                                dijit.byId(name + 'FormCancelButton'),
                                "onClick",
                                dojo.hitch(this.dlg[name], 'hide')
                            );
                        }),
                        onHide: dojo.hitch(this, function() {
                            this.dlg[name].destroyRecursive();
                            this.dlg[name] = null;
                        })
                    });

                    this.dlg[name].show();
                  }
              }),
              error: function(data) {
                  // error message here
              }
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

