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
        editFormSetup : function(id) {
            var form = dojo.query('[id^=edit]')[0];
            var id = form.id;
            dojo.connect(dijit.byId(id), 'onSubmit', dojo.hitch(this, 'formSubmit'));
        },

        formSubmit : function(e) {
            dojo.stopEvent(e);
            console.log(e.getValues())
        },

        newClientAddress : function(id) {
            console.log(id)
        }
    }
);

dojo.require('bba.Core');

dojo.addOnLoad(function() {
    bbaCore = new bba.Core();

    var loader = dojo.byId("loader");
    loader.style.display = "none";
});

