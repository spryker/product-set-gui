<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSetGui\Dependency\QueryContainer;

use Orm\Zed\ProductSet\Persistence\SpyProductSetQuery;

interface ProductSetGuiToProductSetInterface
{
    public function queryProductSet(): SpyProductSetQuery;
}
