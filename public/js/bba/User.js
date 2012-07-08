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

        if (dom.byId('user')) {
            dom.byId('user').focus();
        }

        if (dom.byId('userGrid')) {
            var form = registry.byId('Search');
            if (form) bba.gridSearch(form, userGrid);
        }
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

        userGridRowClick : function(grid)
        {
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);
            id = grid.store.getValue(selectedItem, 'user_idUser');

            if (!dom.byId('userForm')) {
                bba.openFormDialog({
                    url: './user/edit-user',
                    content: {
                        type :  'edit',
                        user_idUser : id
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
                    url: './user/add-user',
                    content: { type :  'add'},
                    dialog: 'userForm'
                });
            } else {
                userForm.show();
            }
        },

        processUserForm : function()
        {
            //bba.closeDialog(userForm);

            values = arguments[0];
            values.type = (values.user_idUser) ? 'edit' : 'add';


            xhr.post({
                url: './user/save-user',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    dom.byId('dialog').innerHTML = data.html;
                    parser.parse('dialog');

                    if (data.error) {
                        error.show();
                    } else if (data.saved > 0) {
                        registry.byId('userGrid')._refresh();

                        if (bba.confrimBox) {
                            confirm.show();
                        }
                    } else {
                        bba.setupDialog(userForm);
                        userForm.show();
                    }
                }
            });
        }
    }

    return bba.User;

});
