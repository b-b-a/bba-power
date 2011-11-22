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

bba.Contract = {

    meterContractGridLayout : [
        { field: 'meter_idMeter', width : '50px', name : 'Id' },
        { field: 'meter_numberMain', width : '150px', name : 'Number Main' },
        { field: 'meter_type', width : '100px', name : 'meter_type' },
        { field: 'meterContract_kvaNominated', width : '120px', name : 'Expected Yearly Consumption', editable : true },
        { field: 'contract_idContract', width : '100px', name : 'Contract Id'},
        { field: 'contract_type', width : '100px', name : 'Contract Type'},
        { field: 'contract_status', width : '100px', name : 'Status'},
        { field: 'contract_dateStart', width : '100px', name : 'Start Date'},
        { field: 'contract_dateEnd', width : '100px', name : 'End Date'},
        { field: '', width : 'auto', name : ''}
    ],

    closeDialog : function()
    {
        dijit.byId('addmeterContract').hide();
    },

    addMeterToContract : function(grid, meterContract) {

        var items = grid.selection.getSelected();

        var kvaError = false;

        if (items.length) {
            var data = { type: 'insert', contract : meterContract, meters : [] };

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

            if (!kvaError) {
                dojo.xhrPost({
                    url: '/meter-contract/save',
                    content: {jsonData : dojo.toJson(data)},
                    handleAs: 'json',
                    preventCache: true,
                    load: function(data) {
                        if (data.saved) {
                            dijit.byId('meterContractGrid' + meterContract)._refresh();
                            dijit.byId('addmeterContract').hide();
                        } else {
                            alert('meters could not be saved');
                        }
                    }
                });
            } else {
                alert('No yearly comsuption was entered for one or more selected meters.');
            }

            console.log(data);
        } else {
            alert('No selections were made, please select meters to add to contract.');
        }
    }
}

