/*
 * Meter.js
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
define("bba/Meter",
    ["dojo/dom","dojo/ready", "dojo/parser", "dojo/_base/xhr", "dijit/registry", "bba/Core", "bba/Contract",
    "dijit/form/RadioButton",
    "dijit/form/NumberTextBox", "dijit/form/FilteringSelect", "dijit/form/SimpleTextarea", "bba/DataGrid"],
    function(dom, ready, parser, xhr, registry, bba) {

    ready(function(){
        if (dom.byId('meter')) {
            dom.byId('meter').focus();
        }
    });

    bba.Meter = {
        gridLayouts : {
            meter : [
                {field: 'meter_idMeter', width: '50px', name: 'Id'},
                {field: 'clientAd_addressName', width: '200px', name: 'Address Name'},
                {field: 'clientAd_postcode', width: '100px', name: 'Postcode'},
                {field: 'meter_status', width: '100px', name: 'Meter Status'},
                {field: 'meter_numberMain', width: '120px', name: 'Meter No'},
                {field: 'contract_type', width: '100px', name: 'Contract Type'},
                {field: 'contract_status', width: '80px', name: 'Contract Status'},
                {field: 'contract_dateEnd', width: '100px', name: 'End Date'},
                {field: '', width: 'auto', name: ''}
            ],
            meterUsage : [
                {field: 'usage_idUsage', width: '50px', name: 'Id'},
                {field: 'usage_dateBill', width: '100px', name: 'Bill Date'},
                {field: 'usage_dateReading', width: '100px', name: 'Reading Date'},
                {field: 'usage_type', width: '100px', name: 'type'},
                {field: 'usage_usageDay', width: '100px', name: 'Day'},
                {field: 'usage_usageNight', width: '100px', name: 'Night'},
                {field: 'usage_usageOther', width: '100px', name: 'Other'},
                {field: '', width: 'auto', name: ''}
            ],
            contract : [
                {field: 'contract_idContract', width: '50px', name: 'Id'},
                {field: 'contract_status', width: '70px', name: 'Status'},
                {field: 'contract_dateStart', width: '100px', name: 'Start Date'},
                {field: 'contract_dateEnd', width: '100px', name: 'End Date'},
                {field: 'contract_reference', width: '200px', name: 'Reference'},
                {field: 'contract_desc', width: '300px', name: 'Description'},
                {field: '', width: 'auto', name: ''}
            ]
        },

        meterGridRowClick : function(selectedIndex)
        {
            if (typeof(selectedIndex) != 'number') {
                selectedIndex = this.focus.rowIndex;
            }

            selectedItem = this.getItem(selectedIndex);
            id = this.store.getValue(selectedItem, 'meter_idMeter');

             bba.openTab({
                tabId : 'meter' + id,
                title : this.store.getValue(selectedItem, 'meter_numberMain'),
                url : '/meter/edit-meter',
                contentVars : {
                    type : 'details',
                    idMeter : id
                }
            });
        },

        editMeterButtonClick : function()
        {
            if (!dom.byId('meterForm')) {
                bba.openFormDialog({
                    url: '/meter/edit-meter',
                    content: {
                        type :  'edit',
                        idMeter : this.value
                    },
                    dialog: 'meterForm'
                });
            } else {
                meterForm.show();
            }
        },

        newMeterButtonClick : function()
        {
            if (!dom.byId('meterForm')) {
                bba.openFormDialog({
                    url: '/meter/add-meter',
                    content: {
                        type :  'add',
                        idSite : this.value
                    },
                    dialog: 'meterForm'
                });
            } else {
                meterForm.show();
            }
        },

        processMeterForm : function()
        {
            bba.closeDialog(meterForm);

            values = arguments[0];
            values.idMeter = values.meter_idMeter;
            values.idSite = values.meter_idSite;
            if (!values.idMeter) {
                values.type = 'add';
            }

            xhr.post({
                url: '/meter/save-meter',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    if (data.saved > 0) {
                        if (values.idMeter) {
                            registry.byId('meter' + values.idMeter).refresh();
                        } else {
                            registry.byId('meterGrid' + values.idSite)._refresh();
                        }
                        //bba.comfirmDialog();
                    } else {
                        dom.byId('dialog').innerHTML = data.html;
                        parser.parse('dialog');
                        bba.setupDialog(meterForm);
                        meterForm.show();
                    }
                }
            });
        }
    }

    return bba.Meter;

});
