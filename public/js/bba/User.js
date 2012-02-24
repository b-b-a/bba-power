/*
 * User.js
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

define("bba/User",
    ["dojo/dom", "dojo/ready", "dojo/parser", "dojo/_base/xhr", "dijit/registry", "bba/Core",
    "dijit/form/ValidationTextBox", "dijit/form/FilteringSelect"],
    function(dom, ready, parser, xhr, registry, bba){

    ready(function () {
        dom.byId('user').focus();
    });

    bba.User = {
        gridLayouts : {
            user : [
                {field: 'user_idUser', width: '50px', name: 'Id'},
                {field: 'user_name', width: '200px', name: 'Username'},
                {field: 'user_fullName', width: '200px', name: 'Full Name'},
                {field: 'user_role', width: '100px', name: 'Role'},
                {field: 'user_accessClient', width: '200px', name: 'Access Client'},
                {field: '', width: 'auto', name: ''}
            ]
        },

        userGridRowClick : function(selectedIndex)
        {
            if (typeof(selectedIndex) != 'number') {
                selectedIndex = this.focus.rowIndex;
            }

            selectedItem = this.getItem(selectedIndex);
            id = this.store.getValue(selectedItem, 'user_idUser');

            if (!dom.byId('userForm')) {
                bba.openFormDialog({
                    url: '/user/edit-user',
                    content: {
                        type :  'edit',
                        idUser : id
                    },
                    dialog: 'userForm'
                });
            } else {
                userForm.show();
            }
        },

        newUserButtonClick : function()
        {
            if (!dom.byId('userForm')) {
                bba.openFormDialog({
                    url: '/user/add-user',
                    content: { type :  'add'},
                    dialog: 'userForm'
                });
            } else {
                userForm.show();
            }
        },

        processUserForm : function()
        {
            bba.closeDialog(userForm);

            values = arguments[0];
            values.idUser = values.user_idUser;
            values.type = (values.idUser) ? 'edit' : 'add';

            xhr.post({
                url: '/user/save-user',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    if (data.saved > 0) {
                        registry.byId('userGrid')._refresh();
                        //bba.comfirmDialog();
                    } else {
                        dom.byId('dialog').innerHTML = data.html;
                        parser.parse('dialog');
                        bba.setupDialog(userForm);
                        userForm.show();
                    }
                }
            });
        }
    }

    return bba.User;

});
