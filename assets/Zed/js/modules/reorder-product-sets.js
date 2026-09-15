/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

var initFormattedNumber = require('ZedGuiModules/libs/formatted-number-input');
var tableAccess = require('ZedGuiModules/libs/table/table-access');

$(document).ready(function () {
    var $productSetWeightsField = $('#reorder_product_sets_form_product_set_weights');
    var productSetWeights = getProductSetWeights();
    var productSetTable = document.querySelector('#product-set-reorder-table');

    if (!productSetTable) {
        return;
    }

    tableAccess.requestTable(productSetTable, function (handle) {
        handle.on('draw', function () {
            initFormattedNumber();

            $('.product_set_weight').off('change').on('change', onProductSetWeightChange);

            setProductSetWeightFieldsOnTableDraw(handle.rowsData());
        });
    });

    /**
     * @returns {Object}
     */
    function getProductSetWeights() {
        if ($productSetWeightsField.attr('value')) {
            return $.parseJSON($productSetWeightsField.attr('value'));
        }

        return {};
    }

    /**
     * @returns {void}
     */
    function onProductSetWeightChange() {
        var $input = $(this);
        var id = $.parseJSON($input.attr('data-id'));
        var unformattedInputClassName = $input.attr('data-target');

        if (unformattedInputClassName) {
            var $unformattedInput = $('.' + unformattedInputClassName);
            productSetWeights[id] = $unformattedInput.val();
        } else {
            productSetWeights[id] = $input.val();
        }

        $productSetWeightsField.attr('value', JSON.stringify(productSetWeights));
    }

    /**
     * @param {Array} rows - Rows of the table, each one an array of its cells.
     *
     * @returns {void}
     */
    function setProductSetWeightFieldsOnTableDraw(rows) {
        for (var i = 0; i < rows.length; i++) {
            var product = rows[i];
            var idProduct = parseInt(product[0]);

            if (productSetWeights.hasOwnProperty(idProduct)) {
                $('#product_set_weight_' + idProduct).val(parseInt(productSetWeights[idProduct]) || 0);
            }
        }
    }
});
