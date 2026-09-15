/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

var initFormattedNumber = require('ZedGuiModules/libs/formatted-number-input');
var tableAccess = require('ZedGuiModules/libs/table/table-access');

var productPosition = {};
var allProductsTable;
var productAbstractSetTable;

function removeActionHandler() {
    var $link = $(this);
    var tableHandler = $link.data('action') === 'select' ? allProductsTable : productAbstractSetTable;

    if (tableHandler) {
        tableHandler.removeSelectedProduct($link.data('id'));
    }

    return false;
}

function ProductSelector() {
    var productSelector = {};
    var selectedProducts = {};

    /**
     * @param {number} idProduct - ID of the product.
     * @param {Array} row - Row the product is shown with in the table of the selection.
     */
    productSelector.addProductToSelection = function (idProduct, row) {
        selectedProducts[idProduct] = row;
    };

    productSelector.removeProductFromSelection = function (idProduct) {
        delete selectedProducts[idProduct];
    };

    productSelector.isProductSelected = function (idProduct) {
        return selectedProducts.hasOwnProperty(idProduct);
    };

    productSelector.clearAllSelections = function () {
        selectedProducts = {};
    };

    productSelector.getSelected = function () {
        return selectedProducts;
    };

    /**
     * @returns {Array} Rows of every selected product, the table of the selection is built from them.
     */
    productSelector.getRows = function () {
        return Object.keys(selectedProducts).map(function (idProduct) {
            return selectedProducts[idProduct];
        });
    };

    return productSelector;
}

function TableHandler(sourceTable, destinationTable, checkBoxNamePrefix, labelCaption, labelId, action, formFieldId) {
    var tableHandler = {
        checkBoxNamePrefix: checkBoxNamePrefix,
        labelId: labelId,
        labelCaption: labelCaption,
        action: action,
        formFieldId: formFieldId,
        sourceTable: sourceTable,
        destinationTable: destinationTable,
    };

    var destinationTableProductSelector = new ProductSelector();
    var sourceHandle = null;
    var destinationHandle = null;

    tableHandler.selectAll = function () {
        if (!sourceHandle) {
            return;
        }

        var api = sourceHandle.raw();

        $('input[type="checkbox"]', api.rows().nodes().toArray()).prop('checked', true);

        api.rows()
            .data()
            .each(function (data) {
                tableHandler.addSelectedProduct(data[0], data);
            });
    };

    tableHandler.deSelectAll = function () {
        if (!sourceHandle) {
            return;
        }

        var api = sourceHandle.raw();

        $('input[type="checkbox"]', api.rows().nodes().toArray()).prop('checked', false);

        api.rows()
            .data()
            .each(function (data) {
                tableHandler.removeSelectedProduct(data[0]);
            });
    };

    tableHandler.addSelectedProduct = function (idProduct, data) {
        if (destinationTableProductSelector.isProductSelected(idProduct)) {
            return;
        }

        destinationTableProductSelector.addProductToSelection(
            idProduct,
            tableHandler.buildSelectionRow(idProduct, data),
        );

        tableHandler.renderSelection();
        tableHandler.updateSelectedProductsLabelCount();
    };

    tableHandler.removeSelectedProduct = function (idProduct) {
        if (destinationTableProductSelector.isProductSelected(idProduct)) {
            destinationTableProductSelector.removeProductFromSelection(idProduct);
            tableHandler.renderSelection();
            $('#' + tableHandler.getCheckBoxNamePrefix() + idProduct).prop('checked', false);
        }

        tableHandler.updateSelectedProductsLabelCount();
    };

    /**
     * @param {number} idProduct - ID of the product.
     * @param {Array} data - Row of the source table, its last cell holding the checkbox of the selection.
     *
     * @returns {Array} Row of the table of the selection, its last cell holding the button which undoes it.
     */
    tableHandler.buildSelectionRow = function (idProduct, data) {
        var row = data.slice();

        row[row.length - 1] =
            '<div><a data-id="' +
            idProduct +
            '" data-action="' +
            tableHandler.getAction() +
            '" href="#" class="btn btn-xs remove-item">Remove</a></div>';

        return row;
    };

    tableHandler.renderSelection = function () {
        if (!destinationHandle) {
            return;
        }

        destinationHandle.raw().clear().rows.add(destinationTableProductSelector.getRows()).draw();
    };

    tableHandler.getSelector = function () {
        return destinationTableProductSelector;
    };

    tableHandler.updateSelectedProductsLabelCount = function () {
        $('#' + tableHandler.getLabelId()).text(
            labelCaption + ' (' + Object.keys(this.getSelector().getSelected()).length + ')',
        );
        var productIds = Object.keys(this.getSelector().getSelected());
        var s = productIds.join(',');
        var field = $('#' + tableHandler.getFormFieldId());
        field.attr('value', s);
    };

    tableHandler.getCheckBoxNamePrefix = function () {
        return tableHandler.checkBoxNamePrefix;
    };

    tableHandler.getLabelId = function () {
        return tableHandler.labelId;
    };

    tableHandler.getAction = function () {
        return tableHandler.action;
    };

    tableHandler.getLabelCaption = function () {
        return tableHandler.labelCaption;
    };

    tableHandler.getFormFieldId = function () {
        return tableHandler.formFieldId;
    };

    tableHandler.getSourceTable = function () {
        return tableHandler.sourceTable;
    };

    tableHandler.getDestinationTable = function () {
        return tableHandler.destinationTable;
    };

    /**
     * @returns {Object|null} Handle of the source table, absent while the plugin is still to create it.
     */
    tableHandler.getSourceHandle = function () {
        return sourceHandle;
    };

    tableHandler.getRowDataByElement = function (elementInRow) {
        return sourceHandle.raw().row($(elementInRow).closest('tr')).data();
    };

    tableAccess.requestTable(sourceTable[0], function (handle) {
        sourceHandle = handle;
    });

    tableAccess.requestTable(destinationTable[0], function (handle) {
        destinationHandle = handle;

        handle.created().then(function () {
            tableHandler.renderSelection();
        });
    });

    return tableHandler;
}

$(document).ready(function () {
    var rawProductPosition = $('#product_set_form_products_form_product_position').attr('value');
    var $allProducts = $('#product-table');
    var $productAbstractSet = $('#product-abstract-set-table');

    if (!$allProducts.length) {
        return;
    }

    if (rawProductPosition) {
        productPosition = $.parseJSON(rawProductPosition);
    }

    allProductsTable = new TableHandler(
        $allProducts,
        $('#selectedProductsTable'),
        'all_products_checkbox_',
        'Products to be assigned',
        'assigned-tab-label',
        'select',
        'product_set_form_products_form_assign_id_product_abstracts',
    );

    $('#selectedProductsTable, #deselectedProductsTable').on('click', '.remove-item', removeActionHandler);

    $allProducts.on('change', '.all-products-checkbox', function () {
        var $checkbox = $(this);
        var id = $.parseJSON($checkbox.attr('data-id'));

        if ($checkbox.prop('checked')) {
            allProductsTable.addSelectedProduct(id, allProductsTable.getRowDataByElement(this));

            return;
        }

        allProductsTable.removeSelectedProduct(id);
    });

    tableAccess.requestTable($allProducts[0], function (handle) {
        handle.on('draw', function () {
            var selector = allProductsTable.getSelector();

            handle
                .raw()
                .rows()
                .data()
                .each(function (data) {
                    var idProduct = parseInt(data[0]);

                    if (selector.isProductSelected(idProduct)) {
                        $('#' + allProductsTable.getCheckBoxNamePrefix() + idProduct).prop('checked', true);
                    }
                });
        });
    });

    $('.prcat-select-all a').on('click', function () {
        allProductsTable.selectAll();

        return false;
    });

    if (!$productAbstractSet.length) {
        return;
    }

    productAbstractSetTable = new TableHandler(
        $productAbstractSet,
        $('#deselectedProductsTable'),
        'product_checkbox_',
        'Products to be deassigned',
        'deassigned-tab-label',
        'deselect',
        'product_set_form_products_form_deassign_id_product_abstracts',
    );

    /**
     * Deassignment is the mirror image of assignment: every product in the set starts out checked, so
     * clearing a checkbox stages a removal rather than undoing a selection.
     */
    productAbstractSetTable.deSelectAll = function () {
        var sourceHandle = productAbstractSetTable.getSourceHandle();

        if (!sourceHandle) {
            return;
        }

        var api = sourceHandle.raw();

        $('input[type="checkbox"]', api.rows().nodes().toArray()).prop('checked', false);

        api.rows()
            .data()
            .each(function (data) {
                productAbstractSetTable.addSelectedProduct(data[0], data);
            });
    };

    productAbstractSetTable.removeSelectedProduct = function (idProduct) {
        var selector = productAbstractSetTable.getSelector();

        if (selector.isProductSelected(idProduct)) {
            selector.removeProductFromSelection(idProduct);
            productAbstractSetTable.renderSelection();
            $('#' + productAbstractSetTable.getCheckBoxNamePrefix() + idProduct).prop('checked', true);
        }

        productAbstractSetTable.updateSelectedProductsLabelCount();
    };

    $productAbstractSet.on('change', '.product_checkbox', function () {
        var $checkbox = $(this);
        var id = $.parseJSON($checkbox.attr('data-id'));

        if ($checkbox.prop('checked')) {
            productAbstractSetTable.removeSelectedProduct(id);
            allProductsTable.removeSelectedProduct(id);

            return;
        }

        productAbstractSetTable.addSelectedProduct(id, productAbstractSetTable.getRowDataByElement(this));
    });

    $productAbstractSet.on('change', '.product_position', function () {
        var $input = $(this);
        var id = $.parseJSON($input.attr('data-id'));
        var unformattedInputClassName = $input.attr('data-target');

        if (unformattedInputClassName) {
            productPosition[id] = $('.' + unformattedInputClassName).val();
        } else {
            productPosition[id] = $input.val();
        }

        $('#product_set_form_products_form_product_position').attr('value', JSON.stringify(productPosition));
    });

    tableAccess.requestTable($productAbstractSet[0], function (handle) {
        handle.on('draw', function () {
            var selector = productAbstractSetTable.getSelector();

            initFormattedNumber($productAbstractSet[0]);

            handle
                .raw()
                .rows()
                .data()
                .each(function (data) {
                    var idProduct = parseInt(data[0]);

                    if (selector.isProductSelected(idProduct)) {
                        $('#' + productAbstractSetTable.getCheckBoxNamePrefix() + idProduct).prop('checked', false);
                    }

                    if (productPosition.hasOwnProperty(idProduct)) {
                        $('#product_position_' + idProduct).val(parseInt(productPosition[idProduct]) || 0);
                    }
                });
        });
    });

    $('.prcat-deselect-all a').on('click', function () {
        productAbstractSetTable.deSelectAll();

        return false;
    });
});
