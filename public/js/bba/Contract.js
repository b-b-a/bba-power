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
define("bba/Contract",
    ["dojo/dom","dojo/ready", "dojo/parser", "dojo/_base/xhr", "dojo/_base/array", "dijit/registry", "bba/Core", "bba/Meter", "dijit/form/ValidationTextBox",
    "dojo/data/ItemFileReadStore", "dijit/form/FilteringSelect", "dijit/form/SimpleTextarea",
    "dojo/data/ItemFileWriteStore"],
    function(dom, ready, parser, xhr, array, registry, bba) {

    ready(function () {
        if (dom.byId('contract')) {
            dom.byId('contract').focus();
        }

        var form = registry.byId('Search');
        if (form) bba.gridSearch(form, contractGrid);
    });

    bba.Contract = {
        gridLayouts : {
            contract : [
                {field: 'contract_idContract', width: '50px', name: 'Id'},
                {field: 'client_name', width: '250px', name: 'Client'},
                {field: 'contract_status', width: '70px', name: 'Status'},
                {field: 'contract_dateStart', width: '100px', name: 'Start Date'},
                {field: 'contract_dateEnd', width: '100px', name: 'End Date'},
                {field: 'meter_count', width: '100px', name: 'No. Meters'},
                {field: 'contract_reference', width: '200px', name: 'Reference'},
                {field: 'contract_desc', width: '300px', name: 'Description'},
                {field: '', width: 'auto', name: ''}
            ]
        },

        closeDialog : function()
        {
            return registry.byId('addmeter').hide();
        },

        preselectMeters : function(grid, id, items)
        {
            array.forEach(items, function(item){
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
                xhr.post({
                    url: '/contract/save-meter-contract',
                    content: {jsonData : dojo.toJson(data)},
                    handleAs: 'json',
                    preventCache: true,
                    load: function(data) {
                        if (data.saved) {
                            registry.byId('meterContractGrid' + meterContract)._refresh();
                            registry.byId('addmeter').hide();
                        } else {
                            alert('meters could not be saved');
                        }
                    }
                });
            }else {
                alert('No yearly comsuption was entered for one or more selected meters.');
            }
        },

        contractGridRowClick : function(selectedIndex)
        {
            if (typeof(selectedIndex) != 'number') {
                selectedIndex = this.focus.rowIndex;
            }

            selectedItem = this.getItem(selectedIndex);
            id = this.store.getValue(selectedItem, 'contract_idContract');

            bba.openTab({
                tabId : 'contract' + id,
                title : this.store.getValue(selectedItem, 'client_name'),
                url : '/contract/edit-contract',
                contentVars : {
                    type : 'details',
                    idContract : id
                }
            });
        },

        editContractButtonClick : function()
        {
            if (!dom.byId('contractform')) {
                bba.openFormDialog({
                    url: '/contract/edit-contract',
                    content: {
                        type :  'edit',
                        idContract : this.value
                    },
                    dialog: 'contractform'
                });
            } else {
                contractform.show();
            }
        },

        newContractButtonClick : function()
        {
            if (!dom.byId('contractform')) {
                bba.openFormDialog({
                    url: '/contract/add-contract',
                    content: {type :  'add'},
                    dialog: 'contractform'
                });
            } else {
                contractform.show();
            }
        },

        processForm : function()
        {
            values = arguments[0];
            values.idContract = values.contract_idContract

            xhr.post({
                url: '/contract/save-contract',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    if (data.saved > 0) {
                        if (values.idContract) {
                            registry.byId('contract' + values.idContract).refresh();
                        } else {
                            contractGrid._refresh();
                        }
                        //bba.comfirmDialog();
                    } else {
                        dom.byId('dialog').innerHTML = data.html;
                        parser.parse('dialog');
                        bba.setupDialog(contractform);
                        contractform.show();
                    }
                }
            });
        }
    }

    return bba.Contract;
});