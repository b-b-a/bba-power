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
dojo.provide('bba');
dojo.provide('bba.Core');

bba = {
    gridLayouts : {
        client : [
            {field: 'client_idClient', width : '50px', name : 'Id'},
            {field: 'client_name', width : '300px', name : 'Client'},
            {field: 'client_desc', width : '200px', name : 'Description'},
            {field: 'clientAd_address1', width : '300px', name : 'Address 1'},
            {field: 'clientAd_postcode', width : '100px', name : 'Postcode'},
            {field: '', width : 'auto', name : ''}
        ],
        meterContract : [
            {field: 'meter_idMeter', width : '50px', name : 'Id'},
            {field: 'meter_numberMain', width : '150px', name : 'Number Main'},
            {field: 'meter_type', width : '100px', name : 'meter_type'},
            {field: 'meterContract_kvaNominated', width : '120px', name : 'Expected Yearly Consumption', editable : true},
            {field: 'contract_idContract', width : '100px', name : 'Contract Id'},
            {field: 'contract_type', width : '100px', name : 'Contract Type'},
            {field: 'contract_status', width : '100px', name : 'Status'},
            {field: 'contract_dateStart', width : '100px', name : 'Start Date'},
            {field: 'contract_dateEnd', width : '100px', name : 'End Date'},
            {field: '', width : 'auto', name : ''}
        ],
        meterUsage : [
            {field: 'usage_idUsage', width : '50px', name : 'Id'},
            {field: 'usage_dateBill', width : '100px', name : 'Bill Date'},
            {field: 'usage_dateReading', width : '100px', name : 'Reading Date'},
            {field: 'usage_type', width : '100px', name : 'type'},
            {field: 'usage_usageDay', width : '100px', name : 'Day'},
            {field: 'usage_usageNight', width : '100px', name : 'Night'},
            {field: 'usage_usageOther', width : '100px', name : 'Other'},
            {field: '', width : 'auto', name : ''}
        ]
    }

};

dojo.declare(
    'bba.Core',
    null,
    {
    }
);

//dojo.require('bba.Core');

dojo.addOnLoad(function() {
    //bbaCore = new bba.Core();

    var loader = dojo.byId("loader");
    loader.style.display = "none";
});

