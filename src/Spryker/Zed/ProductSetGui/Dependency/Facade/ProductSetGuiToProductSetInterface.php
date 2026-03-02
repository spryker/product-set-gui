<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSetGui\Dependency\Facade;

use Generated\Shared\Transfer\ProductSetTransfer;

interface ProductSetGuiToProductSetInterface
{
    public function createProductSet(ProductSetTransfer $productSetTransfer): ProductSetTransfer;

    public function findProductSet(ProductSetTransfer $productSetTransfer): ?ProductSetTransfer;

    public function updateProductSet(ProductSetTransfer $productSetTransfer): ProductSetTransfer;

    public function deleteProductSet(ProductSetTransfer $productSetTransfer): void;

    /**
     * @param array<\Generated\Shared\Transfer\ProductSetTransfer> $productSetTransfers
     *
     * @return void
     */
    public function reorderProductSets(array $productSetTransfers): void;
}
