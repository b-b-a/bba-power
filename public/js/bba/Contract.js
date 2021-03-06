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
[
    "dojo/dom",
    "dojo/query",
    "dojo/parser",
    "dojo/_base/connect",
    "dojo/_base/xhr",
    "dojo/_base/array",
    "dijit/registry",
    "dijit/Dialog",
    "dojo/data/ItemFileReadStore",
    "bba/Core",
    "dojo/text!./html/contractAddEditMeterMessage.html",
    "bba/Meter",
    "bba/Invoice",
    "bba/Supplier",
    "dijit/form/ValidationTextBox",
    "dojo/data/ItemFileReadStore",
    "dijit/form/FilteringSelect",
    "dijit/form/SimpleTextarea",
    "dojo/data/ItemFileWriteStore",
    "dojox/grid/_CheckBoxSelector",
    "dojox/form/Uploader",
    "dojox/form/uploader/plugins/IFrame"
],
    function(dom, query, parser, connect, xhr, array, registry, Dialog, ItemFileReadStore, core, contractAddEditMeterMessage) {

	function zeroFill(num) {
		return (num) ? num : 0;
	}
	
    bba.Contract = {
        gridLayouts : {
            contract : [
                {field: 'contract_idContract', width: '50px', name: 'Id'},
                {field: 'client_name', width: '250px', name: 'Client'},
                {field: 'contract_type', width: '110px', name: 'Type'},
                {field: 'contract_status', width: '110px', name: 'Status'},
                {field: 'contract_dateStart', width: '90px', name: 'Start Date'},
                {field: 'contract_dateEnd', width: '90px', name: 'End Date'},
                {field: 'meter_count', width: '100px', name: 'No. Meters'},
                {field: 'supplier_nameShort', width: '100px', name: 'Supplier'},
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
                    {field: 'meter_numberMain', width : '125px', name: 'Number Main'},
                    {field: 'meter_status', width : '115px', name: 'Meter Status'},
                    {field: 'meterContract_kvaNominated', width: '75px', name: 'Peak kVA', editable: true, formatter: zeroFill},
                    {field: 'meterContract_eac', width: '75px', name: 'EAC', editable: true, formatter: zeroFill},
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
                {field: 'meter_type', width: '95px', name: 'Meter Type'},
                {field: 'meter_status', width: '115px', name: 'Meter Status'},
                {field: 'meter_numberTop', width: '100px', name: 'Number Top'},
                {field: 'meter_numberMain', width: '120px', name: 'Number Main'},
                {field: 'meterContract_kvaNominated', width: '70px', name: 'Peak kVA'},
                {field: 'meterContract_eac', width: '70px', name: 'EAC'},
                {field: 'clientAd_addressName', width : '150px', name: 'Address Name'},
                {field: 'clientAd_address1', width : '150px', name: 'Address Line 1'},
                {field: 'clientAd_address2', width: '150px', name: 'Address Line 2'},
                {field: 'clientAd_address3', width: '150px', name: 'Address Line 3'},
                {field: 'clientAd_postcode', width: '85px', name: 'Postcode'},
                {field: '', width: 'auto', name: ''}
            ],
            tender : [
                {field: 'tender_idTender', width: '50px', name: 'Id'},
                {field: 'supplier_nameShort', width : '80px', name: 'Supplier'},
                {field: 'supplierPers_name', width : '150px', name: 'Supplier Liaison'},
                {field: 'tender_reference', width: '160px', name: 'Reference'},
                {field: 'tender_periodContract', width: '100px', name: 'Contract Period'},
                {field: 'tender_dateExpiresQuote', width: '100px', name: 'Quote Expires'},
                {field: 'tender_chargeStanding', width: '100px', name: 'Standing Charge'},
                {field: 'tender_priceUnitDay', width: '100px', name: 'Day Rate'},
                {field: 'tender_priceUnitNight', width: '100px', name: 'Night Rate'},
                {field: 'tender_priceUnitOther', width: '100px', name: 'Other Rate'},
                {field: 'tender_desc', width: '200px', name: 'Desc.'},
                {field: '', width: 'auto', name: ''}
            ],
            invoiceLines : [
                {field: 'invoiceLine_idInvoiceLine', width: '50px', name: 'Id'},
                {field: 'meter_numberMain', width: '120px', name: 'Meter No'},
                {field: 'invoice_numberInvoice', width: '100px', name: 'Invoice No.'},
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
            core.addDataStore('contractStore', core.storeUrls.contract);

            core.addGrid({
                id : 'contractGrid',
                store : core.dataStores.contractStore,
                structure : bba.Contract.gridLayouts.contract,
                sortInfo : '-5',
                onRowClick : function() {
                     bba.Contract.contractGridRowClick();
                }
            });
        },

        numberComparison : function (a, b) {
            a = Number(a);
            b = Number(b);
            if (a < b){
                return -1;
            } else if (a > b) {
                return 1;
            } else {
                return 0;
            }
        },

        preselectMeters : function(grid, id, items)
        {
            contractMeterStore.comparatorMap = {};
            contractMeterStore.comparatorMap["meter_idMeter"] = bba.Contract.numberComparison;
            contractMeterStore.comparatorMap["contract_idContract"] = bba.Contract.numberComparison;

            array.forEach(items, function(item){
                if (item.contract_idContract == id) {
                    grid.selection.addToSelection(item);
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
        
        validateAddMeterToContract : function(grid, meterContract)
        {
        	confirmAddEditMeters = new Dialog({
                title: "Confirm Add/Edit Meters",
                content: contractAddEditMeterMessage,
                style: "width: 300px",
                onShow : function(){
                    connect.connect(proceedButton, 'onClick', function(){
                        bba.Contract.addMeterToContract(grid, meterContract);
                        confirmAddEditMeters.hide();
                    });
                    connect.connect(cancelButton, 'onClick', function(){
                    	registry.byId('addMeterContractDialog').hide();
                    	confirmAddEditMeters.hide();
                    });
                },
                onHide : function() {
                    bba.closeDialog(confirmAddEditMeters);
                }
            });
        	confirmAddEditMeters.show();
        },

        addMeterToContract : function(grid, meterContract)
        {
            var items = grid.selection.getSelected();
            //console.log(items.length);

            var data = {type: 'insert', contract : meterContract, meters : []};

            if (items.length) {
                items.forEach(function(selectedItem){
                	//console.log(selectedItem);
                	id = selectedItem.meter_idMeter[0];
                    kva = (!selectedItem.meterContract_kvaNominated) ? 0 : selectedItem.meterContract_kvaNominated[0]
                    eac = (!selectedItem.meterContract_eac) ? 0 : selectedItem.meterContract_eac[0];
                    
                    if (!kva) kva = 0;
                    if (!eac) eac = 0;

                    data.meters.push({
                        id : id,
                        kva : kva,
                        eac : eac
                    });
                });
            }
            
            console.log(data);

            xhr.post({
                url: './contract/save-meter-contract',
                content: {jsonData : dojo.toJson(data)},
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                    dom.byId('dialog').innerHTML = data.html;
                    parser.parse('dialog');

                    if (data.error) {
                        error.show();
                    } else if (data.saved) {
                        registry.byId('contract' + meterContract).refresh();
                        registry.byId('addMeterContractDialog').hide();
                        if (bba.config.confirmBox) {
                            confirm.show();
                        }
                    }
                },
                error: function(data) {
                	bba.showXhrError(data.xhr.responseText);
                }
            });
        },

        addMeterButtonClick : function(contentVars)
        {
            if (!dom.byId('addMeterContractDialog')) {

                addMeterContractDialog = new Dialog({
                    id: 'addMeterContractDialog',
                    title: 'Add/Edit Meters on Contract',
                    ioArgs: {
                        content: dojo.mixin({type : 'add'}, contentVars)
                    },
                    ioMethod: dojo.xhrPost,
                    href: './contract/add-meter-contract',
                    onHide: function() {
                        bba.closeDialog(this);
                    }
                });
            }

            addMeterContractDialog.show();
        },

        contractGridRowClick : function(grid)
        {
            grid = (grid) ? grid : core.grids.contractGrid;
            selectedIndex = grid.focus.rowIndex;
            selectedItem = grid.getItem(selectedIndex);
            id = grid.store.getValue(selectedItem, 'contract_idContract');
            tabTitle = grid.store.getValue(selectedItem, 'contract_idContract')
            	+ '-' + grid.store.getValue(selectedItem, 'client_name');

            this.showContractTab(id, tabTitle);
        },

        showContractTab : function(id, tabTitle)
        {
            bba.openTab({
                tabId : 'contract' + id,
                title : (tabTitle) ? tabTitle : 'Contract',
                url : './contract/edit-contract',
                content : {
                    type : 'details',
                    contract_idContract : id
                }
            });
        },
        
        tenderGridRowCellClick : function(grid, row, item)
        {
        	rowIndex = row.rowIndex;
            selectedItem = grid.getItem(rowIndex);
            
            switch (item) {
            	case 'supplier_nameShort':
            	case 'supplierPers_name':
            		bba.Supplier.showSupplierTab(
            			grid.store.getValue(selectedItem, 'supplier_idSupplier'),
                        grid.store.getValue(selectedItem, 'supplier_name')
            		);
            		break;
            	default:
            		this.showTenderTab(
                        grid.store.getValue(selectedItem, 'tender_idTender'),
                        grid.store.getValue(selectedItem, 'supplier_name')
                    );
            		break;
            }
        },
        
        showTenderTab : function(id, tabTitle, contentVars)
        {
        	bba.openTab({
                tabId : 'tender' + id,
                title : (tabTitle) ? tabTitle : 'Tender',
                url : './contract/edit-tender',
                content : dojo.mixin({
                    type :  'details',
                    tender_idTender : id
                }, contentVars)
            });
        },

        editContractButtonClick : function(contentVars)
        {
            if (!dom.byId('contractForm')) {
                bba.openFormDialog({
                    url: './contract/edit-contract',
                    content: dojo.mixin({type :  'edit'}, contentVars),
                    dialog: 'contractForm',
                    deferredFunction: function() {
                        bba.Contract.setupDocEvents();
                        this.vals = contractForm.getValues();
                    }.bind(this)
                });
            } else {
                contractForm.show();
            }
        },

        editTenderButtonClick : function(contentVars)
        {
            if (!dom.byId('tenderForm')) {
                bba.openFormDialog({
                    url: './contract/edit-tender',
                    content: dojo.mixin({type :  'edit'}, contentVars),
                    dialog: 'tenderForm'
                });
            } else {
                tenderForm.show();
            }
        },

        newContractButtonClick : function(contentVars)
        {
            if (!dom.byId('contractForm')) {
                bba.openFormDialog({
                    url: './contract/add-contract',
                    content: dojo.mixin({type :  'add'}, contentVars),
                    dialog: 'contractForm',
                    deferredFunction: function() {
                        bba.Contract.setupDocEvents();
                    }
                });
            } else {
                contractForm.show();
            }
        },

        newTenderButtonClick : function(contentVars)
        {
            if (!dom.byId('tenderForm')) {
                bba.openFormDialog({
                    url: './contract/add-tender',
                    content: dojo.mixin({type :  'add'}, contentVars),
                    dialog: 'tenderForm',
                    deferredFunction: function() {
                        bba.Contract.tenderStore = new ItemFileReadStore({
                            url:'./supplier/data-store/type/supplierList'
                        });

                        bba.Contract.tenderStore.fetch({
                            onError: function(error, request) {
                                bba.dataStoreError(request.store.url, null);
                            }
                        });

                        registry.byId("tender_idSupplier").set('store', bba.Contract.tenderStore);
                        registry.byId("tender_idSupplier").set('value', '0');
                        bba.Contract.changeSupplierContact('');
                    }
                });
            } else {
                tenderForm.show();
            }
        },

        changeSupplierPersonnel : function(val)
        {
            registry.byId('tender_idSupplierPersonnel').set('value', '');

            this.supplierPersonnelStore = new ItemFileReadStore({
                url:'./supplier/data-store/type/supplierPersonnel/supplierId/' + val
            });

            this.supplierPersonnelStore.fetch({
                onError: function(error, request) {
                    bba.dataStoreError(request.store.url, null);
                }
            });

            registry.byId("tender_idSupplierPersonnel").set('store', this.supplierPersonnelStore);
            registry.byId('tender_idSupplierPersonnel').set('value', 0);
        },
        
        validateContractForm : function()
        {	
        	contractFormStandby.show();
        	formValues = contractForm.getValues();
        	
        	if (formValues.contract_idClient == '0') {
        	    registry.byId('contract_idClient').set('value', ' ');
        	}
        	
        	if (formValues.contract_type == null) {
        	
        		var conType = registry.toArray();
        	    
        		 array.forEach(conType, function(item){
         	    	if (item.id.slice(0, 13) == 'contract_type') {
         	    		registry.byId(item).attr('style', 'border: 1px solid red;');
             	        connect.connect(registry.byId(item), 'onChange', function(){
             	            array.forEach(conType, function(item){
             	            	if (item.id.slice(0, 13) == 'contract_type') {
             	            		registry.byId(item).attr('style', 'border: 0px;');
             	            	}
             	            });
             	            
             	        });
         	    	}
         	    });
         	    contractFormStandby.hide();
         	    return false;
         	}
        	
        	// first check form for errors.
        	if (!contractForm.validate()) {
        		contractFormStandby.hide();
        		return false;
        	}
        	
        	//contractFormStandby.show();
        	
        	// forms are not setting the 'edit' value on type so
        	// check if form is not a 'add' type instead.
        	if (formValues.type != 'add' && this.vals.contract_dateStart == formValues.contract_dateStart &&
        			this.vals.contract_reference == formValues.contract_reference) {
        		return true;
        	}
        	
        	//contractFormStandby.hide();
        	
        	// check for duplicate contract
        	xhr.post({
                url: './contract/check-contract-duplicates',
                content: formValues,
                handleAs: 'json',
                preventCache: true,
                load: function(data) {
                	if (data.dups) {
                		
                		console.log(data);
                		
                		dom.byId('dialog').innerHTML = data.html;
                        parser.parse('dialog');
                        
                        bba.setupDialog(contractDuplicates);
                        
                        connect.connect(dupsCloseButton, 'onClick', function(){
                        	contractDuplicates.hide();
                        	contractFormStandby.hide();
                        });
                        
                        connect.connect(dupsContinueButton, 'onClick', function(){
                        	bba.pageStandby.show();
                        	contractDuplicates.hide();
                        	contract_docTermination.submit();
                        });
                		
                		contractDuplicates.show();
                	} else {
                		bba.pageStandby.show();
                		contract_docTermination.submit();
                	}
                },
                error: function(data) {
                	bba.showXhrError(data.xhr.responseText);
                }
        	});
        	
        	return false;
        },

        processContractForm : function()
        {
        	contractFormStandby.hide();
        	bba.pageStandby.hide();
        	bba.closeDialog(contractForm);
        	console.log(arguments[0]);
        	
            data = arguments[0];

            dom.byId('dialog').innerHTML = data.html;
            parser.parse('dialog');

            if (data.error) {
                error.show();
            } else if (data.saved.id > 0) {
                if (data.contract_idContract) {
                    registry.byId('contract' + data.contract_idContract).refresh();
                }

                if (registry.byId('contractGrid')) registry.byId('contractGrid')._refresh();

                if (bba.config.confirmBox) {
                    confirm.show();
                }

                if (data.client_name) {
                    bba.Contract.showContractTab(data.saved.id,  data.contract_idContract + '-' . data.client_name);
                }
                
                if (data.saved.warning) {
                	contractWarning.show();
                }
                
            } else {
                bba.setupDialog(contractForm);
                bba.Contract.setupDocEvents();
                contractForm.show();
            }
        },

        processTenderForm : function()
        {
            //bba.closeDialog(tenderForm);
        	bba.pageStandby.show();
            values = arguments[0];
            values.type = (values.tender_idTender) ? 'edit' : 'add';

            xhr.post({
                url: './contract/save-tender',
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
                        if (values.tender_idTender) {
                            registry.byId('tender' + values.tender_idTender).refresh();
                        } else if (registry.byId('tenderGrid' + values.tender_idContract)) {
                            registry.byId('tenderGrid' + values.tender_idContract)._refresh();
                        }

                        if (bba.config.confirmBox) {
                            confirm.show();
                        }
                    } else {
                        bba.setupDialog(tenderForm);
                        tenderForm.show();
                    }
                },
                error: function(data) {
                	bba.showXhrError(data.xhr.responseText);
                }
            });
        },

        setupDocEvents : function()
        {
            var docs = [
              'contract_docAnalysis',
              'contract_docContractSearchable',
              'contract_docContractSignedClient',
              'contract_docContractSignedBoth',
              'contract_docTermination'
            ];

            array.forEach(docs, function(item, idx){
                if (registry.byId(item)) {
                	//connect.connect(item, "onSubmit", function(){return false;});
                	if (idx < 4) {
                		registry.byId(item).submit = function(){return false;};
                	}
                	
                	// IE9 Does not allow this method of uploading.
                    /*connect.connect(dom.byId(item + '_file'), "onclick", function(){
                        query('input[name=' + item + ']')[0].click();
                    });*/

                    connect.connect(registry.byId(item), "onChange", function(fileArray){
                        bba.docFileList(fileArray, item + '_file');
                    });
                }
            });
            
            connect.connect(contract_docTermination, "onComplete", bba.Contract.processContractForm);
            connect.connect(contract_docTermination, "onError", bba.Contract.processContractForm);
            
            connect.connect(contractForm, "onKeyPress", function(evt){
            	if (evt.keyCode == 13) dojo.stopEvent(evt);
            });
        }
    };

    return bba.Contract;
});