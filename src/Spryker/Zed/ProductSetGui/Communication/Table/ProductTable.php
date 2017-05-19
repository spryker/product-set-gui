<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSetGui\Communication\Table;

use Generated\Shared\Transfer\LocaleTransfer;
use Orm\Zed\Product\Persistence\Map\SpyProductAbstractLocalizedAttributesTableMap;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;
use Spryker\Zed\ProductSetGui\Communication\Controller\AbstractProductSetController;
use Spryker\Zed\ProductSetGui\Dependency\Service\ProductSetGuiToUtilEncodingInterface;
use Spryker\Zed\ProductSetGui\Persistence\ProductSetGuiQueryContainerInterface;

class ProductTable extends AbstractTable
{

    const TABLE_IDENTIFIER = 'product-table';
    const COL_ID_PRODUCT_ABSTRACT = 'id_product_abstract';
    const COL_SKU = 'sku';
    const COL_NAME = 'name';
    const COL_PRICE = 'price';
    const COL_STATUS = 'status';
    const COL_CHECKBOX = 'checkbox';

    /**
     * @var \Spryker\Zed\ProductSetGui\Persistence\ProductSetGuiQueryContainerInterface
     */
    protected $productSetGuiQueryContainer;

    /**
     * @var \Spryker\Zed\ProductSetGui\Dependency\Service\ProductSetGuiToUtilEncodingInterface
     */
    protected $utilEncodingService;

    /**
     * @var int
     */
    protected $idProductSetGuiGroup;

    /**
     * @var \Generated\Shared\Transfer\LocaleTransfer
     */
    protected $localeTransfer;

    /**
     * @param \Spryker\Zed\ProductSetGui\Persistence\ProductSetGuiQueryContainerInterface $productSetGuiQueryContainer
     * @param \Spryker\Zed\ProductSetGui\Dependency\Service\ProductSetGuiToUtilEncodingInterface $utilEncodingService
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param int|null $idProductSetGuiGroup
     */
    public function __construct(
        ProductSetGuiQueryContainerInterface $productSetGuiQueryContainer,
        ProductSetGuiToUtilEncodingInterface $utilEncodingService,
        LocaleTransfer $localeTransfer,
        $idProductSetGuiGroup = null
    ) {
        $this->productSetGuiQueryContainer = $productSetGuiQueryContainer;
        $this->utilEncodingService = $utilEncodingService;
        $this->idProductSetGuiGroup = (int)$idProductSetGuiGroup;
        $this->localeTransfer = $localeTransfer;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $urlSuffix = $this->idProductSetGuiGroup ? sprintf('?%s=%d', AbstractProductSetController::PARAM_ID, $this->idProductSetGuiGroup) : null;
        $this->defaultUrl = self::TABLE_IDENTIFIER . $urlSuffix;
        $this->setTableIdentifier(self::TABLE_IDENTIFIER);

        $config->setHeader([
            self::COL_ID_PRODUCT_ABSTRACT => 'ID',
            self::COL_SKU => 'SKU',
            self::COL_NAME => 'Name',
            self::COL_PRICE => 'Price',
            self::COL_STATUS => 'Status',
            self::COL_CHECKBOX => 'Selected',
        ]);

        $config->setSortable([
            self::COL_ID_PRODUCT_ABSTRACT,
            self::COL_SKU,
            self::COL_NAME,
            self::COL_PRICE,
        ]);

        $config->setSearchable([
            self::COL_ID_PRODUCT_ABSTRACT,
            self::COL_SKU,
            self::COL_NAME,
        ]);

        $config->setRawColumns([
            self::COL_CHECKBOX,
        ]);

        $config->setDefaultSortField(self::COL_ID_PRODUCT_ABSTRACT, TableConfiguration::SORT_ASC);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this->productSetGuiQueryContainer
            ->queryProductAbstract($this->localeTransfer)
            ->withColumn(SpyProductAbstractLocalizedAttributesTableMap::COL_NAME, self::COL_NAME);

        $queryResults = $this->runQuery($query, $config, true);

        $results = [];
        foreach ($queryResults as $productAbstractEntity) {
            $results[] = $this->formatRow($productAbstractEntity);
        }

        return $results;
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstract $productAbstractEntity
     *
     * @return array
     */
    protected function formatRow(SpyProductAbstract $productAbstractEntity)
    {
        return [
            self::COL_ID_PRODUCT_ABSTRACT => $productAbstractEntity->getIdProductAbstract(),
            self::COL_SKU => $productAbstractEntity->getSku(),
            self::COL_NAME => $productAbstractEntity->getVirtualColumn(self::COL_NAME),
            self::COL_PRICE => null, // TODO
            self::COL_STATUS => null, // TODO
            self::COL_CHECKBOX => $this->getSelectField($productAbstractEntity),
        ];
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstract $productAbstractEntity
     *
     * @return string
     */
    protected function getSelectField(SpyProductAbstract $productAbstractEntity)
    {
        $info = [
            'id' => $productAbstractEntity->getIdProductAbstract(),
            self::COL_SKU => $productAbstractEntity->getSku(),
            self::COL_NAME => urlencode($productAbstractEntity->getVirtualColumn(self::COL_NAME)),
        ];

        $checkbox_html = sprintf(
            "<input id='all_products_checkbox_%d' class='all-products-checkbox' type='checkbox' data-info='%s'>",
            $productAbstractEntity->getIdProductAbstract(),
            $this->utilEncodingService->encodeJson($info)
        );

        return $checkbox_html;
    }

}