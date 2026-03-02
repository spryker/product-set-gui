<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSetGui\Dependency\Facade;

use Generated\Shared\Transfer\ProductSetTransfer;

class ProductSetGuiToProductSetBridge implements ProductSetGuiToProductSetInterface
{
    /**
     * @var \Spryker\Zed\ProductSet\Business\ProductSetFacadeInterface
     */
    protected $productSetFacade;

    /**
     * @param \Spryker\Zed\ProductSet\Business\ProductSetFacadeInterface $productSetFacade
     */
    public function __construct($productSetFacade)
    {
        $this->productSetFacade = $productSetFacade;
    }

    public function createProductSet(ProductSetTransfer $productSetTransfer): ProductSetTransfer
    {
        return $this->productSetFacade->createProductSet($productSetTransfer);
    }

    public function findProductSet(ProductSetTransfer $productSetTransfer): ?ProductSetTransfer
    {
        return $this->productSetFacade->findProductSet($productSetTransfer);
    }

    public function updateProductSet(ProductSetTransfer $productSetTransfer): ProductSetTransfer
    {
        return $this->productSetFacade->updateProductSet($productSetTransfer);
    }

    public function deleteProductSet(ProductSetTransfer $productSetTransfer): void
    {
        $this->productSetFacade->deleteProductSet($productSetTransfer);
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductSetTransfer> $productSetTransfers
     *
     * @return void
     */
    public function reorderProductSets(array $productSetTransfers): void
    {
        $this->productSetFacade->reorderProductSets($productSetTransfers);
    }
}
