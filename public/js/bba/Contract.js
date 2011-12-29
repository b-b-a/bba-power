/*
 * Contract.js
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
 * @subpackage Contract
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
dojo.provide('bba.Contract');
dojo.require('bba.Core');

bba.Contract = {

    closeDialog : function()
    {
        dijit.byId('addmeter').hide();
    },

    preselectMeters : function(grid, id, items)
    {
        dojo.forEach(items, function(item){
            if (item.contract_idContract == id) {
                grid.selection.addToSelection(item)
            }
        });
    },

    selectAll : function(grid)
    {
        for (i = 0; i < grid.rowCount; i++) {
          var obj = grid.getItem(i);
          grid.selection.addToSelection(obj);
        }
    },

    addMeterToContract : function(grid, meterContract)
    {
        var items = grid.selection.getSelected();

        var kvaError = false;

        var data = {type: 'insert', contract : meterContract, meters : []};

        if (items.length) {
            items.forEach(function(selectedItem){
                if (!selectedItem.meterContract_kvaNominated) {
                    kvaError = true;
                    return false;
                }

                data.meters.push({
                    id : selectedItem.meter_idMeter[0],
                    kva : selectedItem.meterContract_kvaNominated[0]
                });
            });
        }

        if (!kvaError) {
            dojo.xhrPost({
                url: '/contract/save-meter-contract',
                content: {jsonData : dojo.toJson(data)},
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    if (data.saved) {
                        dijit.byId('meterContractGrid' + meterContract)._refresh();
                        dijit.byId('addmeter').hide();
                    } else {
                        alert('meters could not be saved');
                    }
                }
            });
        } else {
            alert('No yearly comsuption was entered for one or more selected meters.');
        }
    }
}

dojo.addOnLoad(function () {
    dijit.byId('contract').focus();
});