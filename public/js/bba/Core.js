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
dojo.provide('bba.Core');

dojo.declare(
    'bba.Core',
    null,
    {
        abrev : {
            ad : "address",
            co : "contact"
        },
        dialog : [],
        tab : null,

        editFormSetup : function(selectedId, inputName, name)
        {
            var id = 'edit-' + name + '-' + selectedId;

            dojo.connect(dijit.byId(id), 'onSubmit', dojo.hitch(this, function(e){
                dojo.stopEvent(e);
                console.log(name + selectedId);
                this.tab = dijit.byId(name + selectedId);
                this.showDialog(selectedId, inputName, name, 'edit');
            }));
        },

        newFormSetup : function(ident, query, parentKey)
        {
            var i = ident.split("_");
            var selectedId = (query[parentKey]) ? '-' + query[parentKey] : '';
            var id = 'new-' + i[0] + '-button' + selectedId;

            dojo.connect(dijit.byId(id), 'onClick', dojo.hitch(this, function(e){
                dojo.stopEvent(e);

                if (parentKey) {
                    var tab = i[0].substring(0,i[0].length - 2) + selectedId.substring(1);
                } else {
                    var tab = i[0] + '-list';
                }

                this.tab = dijit.byId(tab);
                this.showDialog(selectedId.substring(1), i[1], i[0], 'add', parentKey);
            }));
        },

        formSubmit : function(form, url, selectedId, inputName, name, type)
        {
            form.cancel = null;
            form.returnAction = type;
            form[inputName] = selectedId;
            form.type = type;

            dojo.xhrPost({
                url: url,
                content: form,
                handleAs: 'json',
                preventCache: true,
                load: dojo.hitch(this, function(data) {
                    if (data.saved > 0) {
                        console.log(this.tab);
                        this.tab.refresh();
                    } else {
                        this.dialog[name] = new dijit.Dialog({
                            title: this.capitalize(type + ' ' + name),
                            style: "width:500px;",
                            content: data.html,
                            execute: dojo.hitch(this, function() {
                                var url = '/' + this.hyphenate(name) + '/save';
                                this.dialog[name].destroyRecursive();
                                this.dialog[name] = null;
                                this.formSubmit(arguments[0], url, selectedId, inputName, name, type);
                            }),
                            _onSubmit: dojo.hitch(this.dialog[name], function() {
                                if (!this.validate()) return false;
                                this.onExecute();
                                this.execute(this.get('value'));
                            }),
                            onShow: dojo.hitch(this, function() {
                                dojo.connect(
                                    dijit.byId(name + 'FormCancelButton'),
                                    "onClick",
                                    dojo.hitch(this.dialog[name], 'hide')
                                );
                            }),
                            onHide: dojo.hitch(this, function() {
                                this.dialog[name].destroyRecursive();
                                this.dialog[name] = null;
                            })
                        });

                        this.dialog[name].show();
                    }
                }),
                error: function(data) {

                }
            });
        },

        showDialog : function(selectedId, inputName, name, type, parentKey)
        {
            if (!this.dialog[name]) {

                var contentVars = {type: type};
                if (parentKey) {
                    contentVars[parentKey] = selectedId;
                } else {
                    contentVars[inputName] = selectedId;
                }

                this.dialog[name] = new dijit.Dialog({
                    title: this.capitalize(type + ' ' + this.hyphenate(name).replace('-', ' ')),
                    style: "width:500px;",
                    href: '/' + this.hyphenate(name) + '/' + type,
                    ioArgs: {content: contentVars},
                    execute: dojo.hitch(this, function() {
                        var url = '/' + this.hyphenate(name) + '/save';
                        this.dialog[name].destroyRecursive();
                        this.dialog[name] = null;
                        this.formSubmit(arguments[0], url, selectedId, inputName, name, type);
                    }),
                    _onSubmit: dojo.hitch(this.dialog[name], function() {
                        if (!this.validate()) return false;
                        this.onExecute();
                        this.execute(this.get('value'));
                    }),
                    onHide: dojo.hitch(this, function() {
                        this.dialog[name].destroyRecursive();
                        this.dialog[name] = null;
                    })
                });

                dojo.connect(this.dialog[name], 'onLoad', dojo.hitch(this, function(){
                    dojo.connect(
                        dijit.byId(name + 'FormCancelButton'),
                        "onClick",
                        dojo.hitch(this.dialog[name], 'hide')
                    );
                }));
            }

            this.dialog[name].show();
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

dojo.require('bba.Core');

dojo.addOnLoad(function() {
    bbaCore = new bba.Core();

    var loader = dojo.byId("loader");
    loader.style.display = "none";
});

