<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSetGui\Communication\Table;

use Generated\Shared\Transfer\LocaleTransfer;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;
use Spryker\Zed\ProductSetGui\Communication\Controller\AbstractProductSetController;
use Spryker\Zed\ProductSetGui\Communication\Table\Helper\ProductAbstractTableHelperInterface;
use Spryker\Zed\ProductSetGui\Dependency\Facade\ProductSetGuiToStoreFacadeInterface;
use Spryker\Zed\ProductSetGui\Persistence\ProductSetGuiQueryContainer;
use Spryker\Zed\ProductSetGui\Persistence\ProductSetGuiQueryContainerInterface;

class ProductAbstractSetViewTable extends AbstractTable
{
    /**
     * @var string
     */
    public const TABLE_IDENTIFIER = 'product-abstract-set-view-table';

    /**
     * @var string
     */
    public const COL_ID_PRODUCT_ABSTRACT = 'id_product_abstract';

    /**
     * @var string
     */
    public const COL_IMAGE = 'image';

    /**
     * @var string
     */
    public const COL_DETAILS = 'details';

    /**
     * @var string
     */
    public const COL_NAME = ProductSetGuiQueryContainer::COL_ALIAS_NAME;

    /**
     * @var string
     */
    public const COL_POSITION = ProductSetGuiQueryContainer::COL_ALIAS_POSITION;

    /**
     * @var \Spryker\Zed\ProductSetGui\Persistence\ProductSetGuiQueryContainerInterface
     */
    protected $productSetGuiQueryContainer;

    /**
     * @var \Spryker\Zed\ProductSetGui\Communication\Table\Helper\ProductAbstractTableHelperInterface
     */
    protected $productAbstractTableHelper;

    /**
     * @var int
     */
    protected $idProductSet;

    /**
     * @var \Generated\Shared\Transfer\LocaleTransfer
     */
    protected $localeTransfer;

    /**
     * @var \Spryker\Zed\ProductSetGui\Dependency\Facade\ProductSetGuiToStoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @param \Spryker\Zed\ProductSetGui\Persistence\ProductSetGuiQueryContainerInterface $productSetGuiQueryContainer
     * @param \Spryker\Zed\ProductSetGui\Communication\Table\Helper\ProductAbstractTableHelperInterface $productAbstractTableHelper
     * @param \Spryker\Zed\ProductSetGui\Dependency\Facade\ProductSetGuiToStoreFacadeInterface $storeFacade
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param int $idProductSet
     */
    public function __construct(
        ProductSetGuiQueryContainerInterface $productSetGuiQueryContainer,
        ProductAbstractTableHelperInterface $productAbstractTableHelper,
        ProductSetGuiToStoreFacadeInterface $storeFacade,
        LocaleTransfer $localeTransfer,
        $idProductSet
    ) {
        $this->productSetGuiQueryContainer = $productSetGuiQueryContainer;
        $this->localeTransfer = $localeTransfer;
        $this->idProductSet = $idProductSet;
        $this->productAbstractTableHelper = $productAbstractTableHelper;
        $this->storeFacade = $storeFacade;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $urlSuffix = sprintf('?%s=%d', AbstractProductSetController::PARAM_ID, $this->idProductSet);
        $this->defaultUrl = static::TABLE_IDENTIFIER . $urlSuffix;
        $this->setTableIdentifier(static::TABLE_IDENTIFIER);

        $this->disableSearch();

        $config->setHeader([
            static::COL_ID_PRODUCT_ABSTRACT => 'ID',
            static::COL_IMAGE => 'Preview',
            static::COL_DETAILS => 'Product details',
            static::COL_POSITION => 'Position',
        ]);

        $config->setSortable([
            static::COL_ID_PRODUCT_ABSTRACT,
            static::COL_POSITION,
        ]);

        $config->setRawColumns([
            static::COL_IMAGE,
            static::COL_DETAILS,
        ]);

        $config->setDefaultSortField(static::COL_POSITION, TableConfiguration::SORT_ASC);
        $config->setStateSave(false);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config): array
    {
        $query = $this->productSetGuiQueryContainer->queryProductAbstractByIdProductSet($this->idProductSet, $this->localeTransfer);

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
    protected function formatRow(SpyProductAbstract $productAbstractEntity): array
    {
        return [
            static::COL_ID_PRODUCT_ABSTRACT => $this->formatInt($productAbstractEntity->getIdProductAbstract()),
            static::COL_IMAGE => $this->productAbstractTableHelper->getProductPreview($productAbstractEntity),
            static::COL_DETAILS => $this->generateDetailsColumn($productAbstractEntity),
            static::COL_POSITION => $this->formatInt($productAbstractEntity->getVirtualColumn(static::COL_POSITION)),
        ];
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstract $productAbstractEntity
     *
     * @return string
     */
    protected function generateDetailsColumn(SpyProductAbstract $productAbstractEntity): string
    {
        $rawContentParts = [];
        $rawContentParts[] = sprintf(
            '<strong><a href="%s">%s</a></strong>',
            Url::generate('/product-management/view', ['id-product-abstract' => $productAbstractEntity->getIdProductAbstract()])->build(),
            $productAbstractEntity->getVirtualColumn(static::COL_NAME),
        );
        $rawContentParts[] = sprintf(
            '<small>SKU: %s</small><br/>',
            $productAbstractEntity->getSku(),
        );

        if (!$this->storeFacade->isDynamicStoreEnabled()) {
            $rawContentParts[] = sprintf(
                '<small>Price: %s</small>',
                $this->productAbstractTableHelper->getProductPrice($productAbstractEntity),
            );
        }

        return '<p>' . implode('<br>', $rawContentParts) . '</p>'
            . $this->productAbstractTableHelper->getAbstractProductStatusLabel($productAbstractEntity);
    }
}
