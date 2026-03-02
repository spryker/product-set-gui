<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSetGui\Dependency\QueryContainer;

use Orm\Zed\Product\Persistence\SpyProductAbstractQuery;

class ProductSetGuiToProductBridge implements ProductSetGuiToProductInterface
{
    /**
     * @var \Spryker\Zed\Product\Persistence\ProductQueryContainerInterface
     */
    protected $productQueryContainer;

    /**
     * @param \Spryker\Zed\Product\Persistence\ProductQueryContainerInterface $productQueryContainer
     */
    public function __construct($productQueryContainer)
    {
        $this->productQueryContainer = $productQueryContainer;
    }

    public function queryProductAbstract(): SpyProductAbstractQuery
    {
        return $this->productQueryContainer->queryProductAbstract();
    }
}
