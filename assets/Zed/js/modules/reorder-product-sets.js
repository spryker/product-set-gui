/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

var initFormattedNumber = require('ZedGuiModules/libs/formatted-number-input');

$(document).ready(function () {
    var $productSetWeightsField = $('#reorder_product_sets_form_product_set_weights');
    var productSetWeights = getProductSetWeights();

    $('#product-set-reorder-table')
        .DataTable()
        .on('draw', function (event, settings) {
            initFormattedNumber();

            $('.product_set_weight').off('change').on('change', onProductSetWeightChange);

            setProductSetWeightFieldsOnTableDraw(settings);
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
     * @returns {void}
     */
    function setProductSetWeightFieldsOnTableDraw(settings) {
        for (var i = 0; i < settings.json.data.length; i++) {
            var product = settings.json.data[i];
            var idProduct = parseInt(product[0]);

            if (productSetWeights.hasOwnProperty(idProduct)) {
                $('#product_set_weight_' + idProduct).val(parseInt(productSetWeights[idProduct]) || 0);
            }
        }
    }
});
