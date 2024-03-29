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

    /**
     * @param \Generated\Shared\Transfer\ProductSetTransfer $productSetTransfer
     *
     * @return \Generated\Shared\Transfer\ProductSetTransfer
     */
    public function createProductSet(ProductSetTransfer $productSetTransfer): ProductSetTransfer
    {
        return $this->productSetFacade->createProductSet($productSetTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductSetTransfer $productSetTransfer
     *
     * @return \Generated\Shared\Transfer\ProductSetTransfer|null
     */
    public function findProductSet(ProductSetTransfer $productSetTransfer): ?ProductSetTransfer
    {
        return $this->productSetFacade->findProductSet($productSetTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductSetTransfer $productSetTransfer
     *
     * @return \Generated\Shared\Transfer\ProductSetTransfer
     */
    public function updateProductSet(ProductSetTransfer $productSetTransfer): ProductSetTransfer
    {
        return $this->productSetFacade->updateProductSet($productSetTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductSetTransfer $productSetTransfer
     *
     * @return void
     */
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
