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
[
    "dojo/dom",
    "dojo/parser",
    "dojo/_base/xhr",
    "dojo/_base/connect",
    "dijit/registry",
    "dijit/Dialog",
    "bba/Core",
    "dojo/text!./html/meterNumberEmptyMessage.html",
    "dojo/text!./html/serialNumberEmptyMessage.html",
    "bba/Contract",
    "bba/Invoice",
    "dijit/form/RadioButton",
    "dijit/form/NumberTextBox",
    "dijit/form/FilteringSelect",
    "dijit/form/SimpleTextarea"
],
    function(dom, parser, xhr, connect, registry, Dialog, core, NumberEmptyMessage, SerialEmptyMessage) {

    bba.Meter = {
        gridLayouts : {
            meter : [
                {field: 'meter_idMeter', width: '50px', name: 'Id'},
                {field: 'clientAd_addressName', width: '150px', name: 'Address Name'},
                {field: 'clientAd_postcode', width: '80px', name: 'Postcode'},
                {field: 'meter_type', width: '80px', name: 'Meter Type'},
                {field: 'meter_status', width: '110px', name: 'Meter Status'},
                {field: 'meter_numberTop', width: '90px', name: 'Number Top'},
                {field: 'meter_numberMain', width: '120px', name: 'Meter No'},
                {field: 'contract_type', width: '100px', name: 'Contract Type'},
                {field: 'contract_status', width: '110px', name: 'Contract Status'},
                {field: 'contract_dateEnd', width: '80px', name: 'End Date'},
                {field: '', width: 'auto', name: ''}
            ],
            meterUsage : [
                {field: 'usage_idUsage', width: '50px', name: 'Id'},
                {field: 'usage_dateReading', width: '110px', name: 'Reading Date'},
                {field: 'usage_type', width: '100px', name: 'Type'},
                {field: 'usage_usageDay', width: '100px', name: 'Day'},
                {field: 'usage_usageNight', width: '100px', name: 'Night'},
                {field: 'usage_usageOther', width: '100px', name: 'Other'},
                {field: 'usage_usageTotal', width: '100px', name: 'Total Usage'},
                {field: 'usage_dateBill', width: '100px', name: 'Bill Date'},
                {field: 'invoiceUsage_idInvoiceLine', width: '100px', name: 'Invoice Line Id'},
                {field: '', width: 'auto', name: ''}
            ],
            contract : [
                {field: 'contract_idContract', width: '50px', name: 'Id'},
                {field: 'contract_status', width: '110px', name: 'Status'},
                {field: 'contract_type', width: '110px', name: 'Type'},
                {field: 'contract_dateStart', width: '90px', name: 'Start Date'},
                {field: 'contract_dateEnd', width: '90px', name: 'End Date'},
                {field: 'meterContract_kvaNominated', width: '70px', name: 'Peak kVA'},
                {field: 'meterContract_eac', width: '70px', name: 'EAC'},
                {field: 'contract_reference', width: '200px', name: 'Reference'},
                {field: 'contract_desc', width: '300px', name: 'Description'},
                {field: '', width: 'auto', name: ''}
            ],
            invoiceLines : [
                {field: 'invoiceLine_idInvoiceLine', width: '50px', name: 'Id'},
                {field: 'invoice_numberInvoice', width: '100px', name: 'Invoice No.'},
                {field: 'contract_idContract', width: '100px', name: 'Contract Id'},
                {field: 'invoiceLine_dateStart', width: '150px', name: 'Start Date'},
                {field: 'invoiceLine_dateEnd', width: '150px', name: 'End Date'},
                {field: 'invoiceLine_fee', width: '50px', name: 'Fee'},
                {field: 'invoiceLine_commission', width: '100px', name: 'Commission'},
                {field: 'invoiceLine_consumption', width: '110px', name: 'Consumption'},
                {field: 'invoiceLine_amount', width: '100px', name: 'Amount'},
                {field: 'invoiceLine_proportionInvoiced', width: '50px', name: 'Claim'},
                {field: 'invoiceLine_reference', width: '100px', name: 'Invoice Line Ref'},
                {field: 'invoiceLine_dateCreated', width: '150px', name: 'Date Created'},
                {field: '', width: 'auto', name: ''}
            ]
        },
        
        init : function()
        {
            core.addDataStore('meterStore', core.storeUrls.meter);

            core.addGrid({
                id : 'meterGrid',
                store : core.dataStores.meterStore,
                structure : bba.Meter.gridLayouts.meter,
                sortInfo : '2',
                onRowClick : function() {
                     bba.Meter.meterGridRowClick();
                }
            });
        },

        meterGridRowClick : function(grid)
        {
            grid = (grid) ? grid : core.grids.meterGrid;
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);
            id = grid.store.getValue(selectedItem, 'meter_idMeter');
            tabTitle = grid.store.getValue(selectedItem, 'meter_numberMain');

            this.showMeterTab(id, tabTitle);
        },

        showMeterTab : function(id, tabTitle)
        {
            bba.openTab({
                tabId : 'meter' + id,
                title :  (tabTitle) ? tabTitle : 'Meter',
                url : './meter/edit-meter',
                content : {
                    type : 'details',
                    meter_idMeter : id
                }
            });
        },

        usageGridRowClick : function(grid)
        {
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);
            id = grid.store.getValue(selectedItem, 'usage_idUsage');

            if (!dom.byId('usageForm')) {
                bba.openFormDialog({
                    url: './meter/edit-usage',
                    content: {
                        type :  'edit',
                        usage_idUsage : id
                    },
                    dialog: 'usageForm'
                });
            } else {
                usageForm.show();
            }
        },

        printMeterButtonClick : function(contentVars)
        {
           newWin = window.open('', 'print meter', "height=200,width=200,modal=yes,alwaysRaised=yes,scrollbars=yes");

           xhr.post({
                url: '/meter/print-meter',
                content: dojo.mixin({type : 'print'}, contentVars),
                handleAs: 'text',
                preventCache: true,
                load: function(data) {
                    //dom.byId('dialog').innerHTML = data.html;
                    newWin.document.write(data);
                },
                error: function(data) {
                	bba.showXhrError(data.xhr.responseText);
                }
            });
        },

        editMeterButtonClick : function(contentVars)
        {
            if (!dom.byId('meterForm')) {
                bba.openFormDialog({
                    url: './meter/edit-meter',
                    content: dojo.mixin({type :  'edit'}, contentVars),
                    dialog: 'meterForm'
                });
            } else {
                meterForm.show();
            }
        },

        newMeterButtonClick : function(contentVars)
        {
            if (!dom.byId('meterForm')) {
                bba.openFormDialog({
                    url: './meter/add-meter',
                    content: dojo.mixin({type :  'add'}, contentVars),
                    dialog: 'meterForm'
                });
            } else {
                meterForm.show();
            }
        },

        newUsageButtonClick : function(contentVars)
        {
            if (!dom.byId('usageForm')) {
                bba.openFormDialog({
                    url: './meter/add-usage',
                    content: dojo.mixin({type :  'add'}, contentVars),
                    dialog: 'usageForm'
                });
            } else {
                usageForm.show();
            }
        },
        
        meterFormValidate : function()
        {
        	meterFormStandby.show();
        	formValues = meterForm.getValues();
        	
        	if ((formValues.meter_type == 'electric' 
        			&& formValues.meter_numberMain)
        			|| (formValues.meter_type == 'gas' 
        				&& formValues.meter_numberMain)
        			|| (formValues.meter_type == 'water' 
        				&& formValues.meter_numberSerial)) {
        		return meterForm.validate();	
        	}
        	
        	submitForm = false;
        	
        	meterFormEmpty = new Dialog({
                title: "Meter Form Warning",
                content: (formValues.meter_type == 'water') ? SerialEmptyMessage : NumberEmptyMessage,
                style: "width: 300px",
                onShow : function(){
                    connect.connect(clientYesButton, 'onClick', function(){
                    	if (meterForm.validate()) {
                    		bba.Meter.processMeterForm(formValues);
                    	}
                    	meterFormEmpty.hide();
                        
                    });
                    connect.connect(clientNoButton, 'onClick', function(){
                    	meterFormStandby.hide();
                    	meterFormEmpty.hide();
                    });
                },
                onHide : function() {
                    bba.closeDialog(meterFormEmpty);
                }
            });
        	meterFormEmpty.show();
        	
        	return false;
        },

        processMeterForm : function()
        {
        	meterFormStandby.hide();
        	bba.closeDialog(meterForm);
        	bba.pageStandby.show();

            values = arguments[0];
            values.type = (values.meter_idMeter) ? 'edit' : 'add';

            xhr.post({
                url: './meter/save-meter',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    dom.byId('dialog').innerHTML = data.html;
                    parser.parse('dialog');
                    bba.pageStandby.hide();

                    if (data.error) {
                        error.show();
                    } else if (data.saved > 0) {
                        if (values.meter_idMeter) {
                            registry.byId('meter' + values.meter_idMeter).refresh();
                        } else {
                            registry.byId('meterGrid' + values.meter_idSite)._refresh();
                        }

                        if (bba.config.confirmBox) {
                            confirm.show();
                        }
                    } else {
                        bba.setupDialog(meterForm);
                        meterForm.show();
                    }
                },
                error: function(data) {
                	bba.showXhrError(data.xhr.responseText);
                }
            });
        },

        processUsageForm : function()
        {
            //bba.closeDialog(usageForm);
        	bba.pageStandby.show();
            values = arguments[0];
            values.type = (values.usage_idUsage) ? 'edit' : 'add';

            xhr.post({
                url: './meter/save-usage',
                content: values,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    dom.byId('dialog').innerHTML = data.html;
                    parser.parse('dialog');
                    bba.pageStandby.hide();

                    if (data.error) {
                        error.show();
                    } else if (data.saved > 0) {
                        registry.byId('usageGrid' + values.usage_idMeter)._refresh();

                        if (bba.config.confirmBox) {
                            confirm.show();
                        }
                    } else {
                        bba.setupDialog(usageForm);
                        usageForm.show();
                    }
                },
                error: function(data) {
                	bba.showXhrError(data.xhr.responseText);
                }
            });
        }
    }

    return bba.Meter;

});
