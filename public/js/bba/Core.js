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

/**
 * Core.
 *
 * @category   BBA
 * @package    JavaScript
 * @subpackage Core
 * @copyright  Copyright (c) 2011 Shaun Freeman. (http://www.shaunfreeman.co.uk)
 * @license    http://www.gnu.org/licenses GNU General Public License
 * @author     Shaun Freeman <shaun@shaunfreeman.co.uk>
 */
define("bba/Core",
    ["dojo/dom","dojo/ready", "dijit/layout/BorderContainer","dijit/layout/TabContainer",
    "bba/ContentPane", "bba/StackContainer", "dojox/data/QueryReadStore",
    "dijit/form/Form", "dijit/form/Button"],
    function(dom, ready) {

    ready(function(){
        loader = dom.byId("loader");
        loader.style.display = "none";
    });

    bba = {
        gridLayouts : {
            siteMeter : [
                {field: 'meter_idMeter', width: '50px', name: 'Id'},
                {field: 'meter_type', width: '150px', name: 'Type'},
                {field: 'meter_numberSerial', width: '200px', name: 'Number Serial'},
                {field: 'meter_numberMain', width: '200px', name: 'Number Main'},
                {field: '', width: 'auto', name: ''}
            ],
            meterContract : [
                {field: 'meter_idMeter', width: '50px', name: 'Id'},
                {field: 'meter_numberMain', width : '150px', name: 'Number Main'},
                {field: 'meter_type', width : '100px', name: 'Meter Type'},
                {field: 'meterContract_kvaNominated', width: '120px', name: 'Expected Yearly Consumption', editable: true},
                {field: 'contract_idContract', width: '100px', name: 'Contract Id'},
                {field: 'contract_type', width: '100px', name: 'Contract Type'},
                {field: 'contract_status', width: '100px', name: 'Status'},
                {field: 'contract_dateStart', width: '100px', name: 'Start Date'},
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
            supplierContract : [
                {field: 'contract_idContract', width: '50px', name: 'Id'},
                {field: 'client_name', width: '300px', name: 'Client'},
                {field: 'contract_type', width: '150px', name: 'Type'},
                {field: 'contract_status', width: '100px', name: 'Status'},
                {field: 'contract_dateEnd', width: '100px', name: 'End Date'},
                {field: 'contract_reference', width: '200px', name: 'Reference'},
                {field: '', width: 'auto', name: ''}
            ],
            supplierContact : [
                {field: 'supplierCo_idSupplierContact', width: '50px', name: 'Id'},
                {field: 'supplierCo_name', width: '150px', name: 'Name'},
                {field: 'supplierCo_phone', width: '100px', name: 'Phone'},
                {field: 'supplierCo_email', width: '300px', name: 'Email'},
                {field: 'supplierCo_address1', width: '200px', name: 'Address 1'},
                {field: 'supplierCo_postcode', width: '100px', name: 'Postcode'},
                {field: '', width: 'auto', name: ''}
            ]
        }

    };

    return bba;

});

