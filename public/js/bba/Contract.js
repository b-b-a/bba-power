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
    ["dojo/dom","dojo/ready", "dojo/parser", "dojo/_base/xhr", "dojo/_base/array", "dijit/registry",
     "dijit/Dialog", "dojo",
    "bba/Core", "bba/Meter", "dijit/form/ValidationTextBox", "dojo/data/ItemFileReadStore",
    "dijit/form/FilteringSelect", "dijit/form/SimpleTextarea", "dojo/data/ItemFileWriteStore"],
    function(dom, ready, parser, xhr, array, registry, Dialog, dojo, bba) {

    ready(function () {
        if (dom.byId('contract')) {
            dom.byId('contract').focus();
        }

        if (dom.byId('contractGrid')) {
            var form = registry.byId('Search');
            if (form) bba.gridSearch(form, contractGrid);
        }

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
            ],
            meterContract : [
                {
                    type: "dojox.grid._CheckBoxSelector"
                },
                [
                    {field: 'meter_idMeter', width: '50px', name: 'Id'},
                    {field: 'meter_numberMain', width : '150px', name: 'Number Main'},
                    {field: 'meter_status', width : '100px', name: 'Meter Status'},
                    {field: 'meterContract_kvaNominated', width: '100px', name: 'Peak kVA', editable: true},
                    {field: 'meterContract_eac', width: '100px', name: 'EAC', editable: true},
                    {field: 'contract_idContract', width: '100px', name: 'Contract Id'},
                    {field: 'contract_type', width: '100px', name: 'Contract Type'},
                    {field: 'contract_status', width: '100px', name: 'Status'},
                    {field: 'contract_dateStart', width: '100px', name: 'Start Date'},
                    {field: 'contract_dateEnd', width: '100px', name: 'End Date'},
                    {field: '', width: 'auto', name: ''}
                ]
            ],
            meter : [
                {field: 'meter_idMeter', width: '50px', name: 'Id'},
                {field: 'meter_type', width: '85px', name: 'Meter Type'},
                {field: 'meter_status', width: '85px', name: 'Meter Status'},
                {field: 'meter_numberMain', width: '85px', name: 'Number Main'},
                {field: 'clientAd_addressName', width : '150px', name: 'Address Name'},
                {field: 'clientAd_address1', width : '150px', name: 'Address Line 1'},
                {field: 'clientAd_address2', width: '150px', name: 'Address Line 2'},
                {field: 'clientAd_address3', width: '150px', name: 'Address Line 3'},
                {field: 'clientAd_postcode', width: '85px', name: 'Postcode'},
                {field: 'meter_numberMain', width: '150px', name: 'Number Main'},
                {field: '', width: 'auto', name: ''}
            ],
            tender : [
                {field: 'tender_idTender', width: '50px', name: 'Id'},
                {field: 'supplier_nameShort', width : '80px', name: 'Supplier'},
                {field: 'supplierCo_name', width : '150px', name: 'Supplier Contact'},
                {field: 'supplierCo_phone', width: '100px', name: 'Phone'},
                {field: 'tender_periodContract', width: '100px', name: 'Contract Period'},
                {field: 'tender_dateExpiresQuote', width: '100px', name: 'Quote Expires'},
                {field: 'tender_chargeStanding', width: '100px', name: 'Standing Charge'},
                {field: 'tender_priceUnitDay', width: '100px', name: 'Day Rate'},
                {field: 'tender_priceUnitNight', width: '100px', name: 'Night Rate'},
                {field: 'tender_priceUnitOther', width: '100px', name: 'Other Rate'},
                {field: '', width: 'auto', name: ''}
            ]
        },

        closeDialog : function()
        {
            return registry.byId('addMeterContractDialog').hide();
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
                    if (!selectedItem.meterContract_kvaNominated || !selectedItem.meterContract_eac) {
                        kvaError = true;
                        return false;
                    }

                    data.meters.push({
                        id : selectedItem.meter_idMeter[0],
                        kva : selectedItem.meterContract_kvaNominated[0],
                        eac : selectedItem.meterContract_eac[0]
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
                            registry.byId('addMeterContractDialog').hide();
                        } else {
                            alert('meters could not be saved');
                        }
                    }
                });
            }else {
                alert('Please enter Peak kVA and EAC for all selected meters (enter zero if not known).');
            }
        },

        addMeterButtonClick : function()
        {
            if (!dom.byId('addMeterContractDialog')) {

                addMeterContractDialog = new Dialog({
                    id: 'addMeterContractDialog',
                    title: 'Add/Edit Meters on Contract',
                    ioArgs: {
                        content: {
                            type :  'add',
                            idContract : this.value
                        }
                    },
                    ioMethod: dojo.xhrPost,
                    href: '/contract/add-meter-contract'
                });
            }

            addMeterContractDialog.show();
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

        tenderGridRowClick : function(selectedIndex)
        {
            if (typeof(selectedIndex) != 'number') {
                selectedIndex = this.focus.rowIndex;
            }

            selectedItem = this.getItem(selectedIndex);
            id = this.store.getValue(selectedItem, 'tender_idTender');

            bba.openTab({
                tabId : 'tender' + id,
                title : this.store.getValue(selectedItem, 'supplier_name'),
                url : '/contract/edit-tender',
                contentVars : {
                    type : 'details',
                    idTender : id
                }
            });
        },

        editContractButtonClick : function()
        {
            if (!dom.byId('contractForm')) {
                bba.openFormDialog({
                    url: '/contract/edit-contract',
                    content: {
                        type :  'edit',
                        idContract : this.value
                    },
                    dialog: 'contractForm'
                });
            } else {
                contractForm.show();
            }
        },

        editTenderButtonClick : function()
        {
            if (!dom.byId('tenderForm')) {
                bba.openFormDialog({
                    url: '/contract/edit-tender',
                    content: {
                        type :  'edit',
                        idTender : this.value
                    },
                    dialog: 'tenderForm'
                });
            } else {
                tenderForm.show();
            }
        },

        newContractButtonClick : function()
        {
            if (!dom.byId('contractForm')) {
                bba.openFormDialog({
                    url: '/contract/add-contract',
                    content: {type :  'add'},
                    dialog: 'contractForm'
                });
            } else {
                contractForm.show();
            }
        },

        newTenderButtonClick : function()
        {
            if (!dom.byId('tenderForm')) {
                bba.openFormDialog({
                    url: '/contract/add-tender',
                    content: {type :  'add'},
                    dialog: 'tenderForm'
                });
            } else {
                tenderForm.show();
            }
        },

        processContractForm : function()
        {
            bba.closeDialog(contractForm);

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
                        bba.setupDialog(contractForm);
                        contractForm.show();
                    }
                }
            });
        },

        processTenderForm : function()
        {
            bba.closeDialog(tenderForm);

            values = arguments[0];
            values.idTender = values.tender_idTender

            xhr.post({
                url: '/contract/save-tender',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    if (data.saved > 0) {
                        if (values.idTender) {
                            registry.byId('tender' + values.idTender).refresh();
                        }
                        //bba.comfirmDialog();
                    } else {
                        dom.byId('dialog').innerHTML = data.html;
                        parser.parse('dialog');
                        bba.setupDialog(tenderForm);
                        tenderForm.show();
                    }
                }
            });
        }
    }

    return bba.Contract;
});