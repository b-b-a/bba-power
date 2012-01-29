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
    ["dojo/dom","dojo/ready", "bba/Core", "dijit/form/RadioButton", "dijit/form/NumberSpinner",
    "dijit/form/FilteringSelect", "bba/DataGrid"],
    function(dom, ready, bba) {

    ready(function(){
        if (dom.byId('meter')) {
            dom.byId('meter').focus();
        }
    });

    bba.Meter = {
        gridLayouts : {
            meter : [
                {field: 'meter_idMeter', width: '50px', name: 'Id'},
                {field: 'client_name', width: '200px', name: 'Client'},
                {field: 'clientAd_addressName', width: '200px', name: 'Address Name'},
                {field: 'clientAd_postcode', width: '100px', name: 'Postcode'},
                {field: 'meter_type', width: '100px', name: 'Meter Type'},
                {field: 'meter_status', width: '100px', name: 'Meter Status'},
                {field: 'meter_numberMain', width: '120px', name: 'Meter No'},
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
            ]
        }
    }

    return bba.Meter;

});
