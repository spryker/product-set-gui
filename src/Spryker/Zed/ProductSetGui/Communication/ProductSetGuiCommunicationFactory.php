<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSetGui\Communication;

use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\ProductSetGui\Communication\Form\ActivateProductSetForm;
use Spryker\Zed\ProductSetGui\Communication\Form\CreateProductSetFormType;
use Spryker\Zed\ProductSetGui\Communication\Form\DataMapper\CreateFormDataToTransferMapper;
use Spryker\Zed\ProductSetGui\Communication\Form\DataMapper\ReorderFormDataToTransferMapper;
use Spryker\Zed\ProductSetGui\Communication\Form\DataMapper\UpdateFormDataToTransferMapper;
use Spryker\Zed\ProductSetGui\Communication\Form\DataProvider\CreateFormDataProvider;
use Spryker\Zed\ProductSetGui\Communication\Form\DataProvider\ReorderProductSetsFormDataProvider;
use Spryker\Zed\ProductSetGui\Communication\Form\DataProvider\UpdateFormDataProvider;
use Spryker\Zed\ProductSetGui\Communication\Form\DeactivateProductSetForm;
use Spryker\Zed\ProductSetGui\Communication\Form\DeleteProductSetForm;
use Spryker\Zed\ProductSetGui\Communication\Form\ReorderProductSetsFormType;
use Spryker\Zed\ProductSetGui\Communication\Form\UpdateProductSetFormType;
use Spryker\Zed\ProductSetGui\Communication\Table\Helper\ProductAbstractTableHelper;
use Spryker\Zed\ProductSetGui\Communication\Table\Helper\ProductAbstractTableHelperInterface;
use Spryker\Zed\ProductSetGui\Communication\Table\ProductAbstractSetUpdateTable;
use Spryker\Zed\ProductSetGui\Communication\Table\ProductAbstractSetViewTable;
use Spryker\Zed\ProductSetGui\Communication\Table\ProductSetReorderTable;
use Spryker\Zed\ProductSetGui\Communication\Table\ProductSetTable;
use Spryker\Zed\ProductSetGui\Communication\Table\ProductTable;
use Spryker\Zed\ProductSetGui\Communication\Tabs\ProductSetFormTabs;
use Spryker\Zed\ProductSetGui\Dependency\Facade\ProductSetGuiToLocaleInterface;
use Spryker\Zed\ProductSetGui\Dependency\Facade\ProductSetGuiToMoneyInterface;
use Spryker\Zed\ProductSetGui\Dependency\Facade\ProductSetGuiToPriceProductFacadeInterface;
use Spryker\Zed\ProductSetGui\Dependency\Facade\ProductSetGuiToProductImageInterface;
use Spryker\Zed\ProductSetGui\Dependency\Facade\ProductSetGuiToProductSetInterface;
use Spryker\Zed\ProductSetGui\Dependency\Facade\ProductSetGuiToUrlInterface;
use Spryker\Zed\ProductSetGui\Dependency\QueryContainer\ProductSetGuiToProductSetInterface as QueryContainerProductSetGuiToProductSetInterface;
use Spryker\Zed\ProductSetGui\Dependency\Service\ProductSetGuiToUtilEncodingInterface;
use Spryker\Zed\ProductSetGui\ProductSetGuiDependencyProvider;
use Symfony\Component\Form\FormInterface;

/**
 * @method \Spryker\Zed\ProductSetGui\ProductSetGuiConfig getConfig()
 * @method \Spryker\Zed\ProductSetGui\Persistence\ProductSetGuiQueryContainerInterface getQueryContainer()
 */
class ProductSetGuiCommunicationFactory extends AbstractCommunicationFactory
{
    public function createCreateFormDataProvider(): CreateFormDataProvider
    {
        return new CreateFormDataProvider($this->getLocaleFacade(), $this->getConfig());
    }

    public function createUpdateFormDataProvider(): UpdateFormDataProvider
    {
        return new UpdateFormDataProvider($this->getProductSetFacade(), $this->getLocaleFacade(), $this->getConfig());
    }

    /**
     * @deprecated Use {@link getCreateProductSetForm()} instead.
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createCreateProductSetForm(array $data = [], array $options = []): FormInterface
    {
        return $this->getFormFactory()->create($this->createCreateProductSetFormType(), $data, $options);
    }

    public function createActivateProductSetForm(): FormInterface
    {
        return $this->getFormFactory()->create(ActivateProductSetForm::class);
    }

    public function createDeactivateProductSetForm(): FormInterface
    {
        return $this->getFormFactory()->create(DeactivateProductSetForm::class);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getCreateProductSetForm(array $data = [], array $options = []): FormInterface
    {
        return $this->createCreateProductSetForm($data, $options);
    }

    /**
     * @deprecated Use {@link getUpdateProductSetForm()} instead.
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createUpdateProductSetForm(array $data = [], array $options = []): FormInterface
    {
        return $this->getFormFactory()->create($this->createUpdateProductSetFormType(), $data, $options);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getUpdateProductSetForm(array $data = [], array $options = []): FormInterface
    {
        return $this->createUpdateProductSetForm($data, $options);
    }

    public function createCreateFormDataToTransferMapper(): CreateFormDataToTransferMapper
    {
        return new CreateFormDataToTransferMapper($this->getLocaleFacade());
    }

    public function createUpdateFormDataToTransferMapper(): UpdateFormDataToTransferMapper
    {
        return new UpdateFormDataToTransferMapper($this->getLocaleFacade());
    }

    public function createReorderFormDataToTransferMapper(): ReorderFormDataToTransferMapper
    {
        return new ReorderFormDataToTransferMapper();
    }

    public function createProductSetFormTabs(): ProductSetFormTabs
    {
        return new ProductSetFormTabs();
    }

    public function createProductSetTable(LocaleTransfer $localeTransfer): ProductSetTable
    {
        return new ProductSetTable($this->getQueryContainer(), $localeTransfer);
    }

    public function createProductSetReorderTable(LocaleTransfer $localeTransfer): ProductSetReorderTable
    {
        return new ProductSetReorderTable($this->getQueryContainer(), $localeTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param int|null $idProductSet
     *
     * @return \Spryker\Zed\ProductSetGui\Communication\Table\ProductTable
     */
    public function createProductTable(LocaleTransfer $localeTransfer, $idProductSet = null): ProductTable
    {
        return new ProductTable(
            $this->getQueryContainer(),
            $this->createProductAbstractTableHelper(),
            $localeTransfer,
            $idProductSet,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param int $idProductSet
     *
     * @return \Spryker\Zed\ProductSetGui\Communication\Table\ProductAbstractSetUpdateTable
     */
    public function createProductAbstractSetUpdateTable(LocaleTransfer $localeTransfer, $idProductSet): ProductAbstractSetUpdateTable
    {
        return new ProductAbstractSetUpdateTable(
            $this->getQueryContainer(),
            $this->createProductAbstractTableHelper(),
            $localeTransfer,
            $idProductSet,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param int $idProductSet
     *
     * @return \Spryker\Zed\ProductSetGui\Communication\Table\ProductAbstractSetViewTable
     */
    public function createProductAbstractSetViewTable(LocaleTransfer $localeTransfer, $idProductSet): ProductAbstractSetViewTable
    {
        return new ProductAbstractSetViewTable(
            $this->getQueryContainer(),
            $this->createProductAbstractTableHelper(),
            $localeTransfer,
            $idProductSet,
        );
    }

    /**
     * @deprecated Use the FQCN directly.
     *
     * @return string
     */
    public function createCreateProductSetFormType(): string
    {
        return CreateProductSetFormType::class;
    }

    /**
     * @deprecated Use the FQCN directly.
     *
     * @return string
     */
    public function createUpdateProductSetFormType(): string
    {
        return UpdateProductSetFormType::class;
    }

    /**
     * @deprecated Use {@link getReorderProductSetsForm()} instead.
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createReorderProductSetsForm(array $data = [], $options = []): FormInterface
    {
        return $this->getFormFactory()->create($this->createReorderProductSetsFormType(), $data, $options);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getReorderProductSetsForm(array $data = [], $options = []): FormInterface
    {
        return $this->createReorderProductSetsForm($data, $options);
    }

    /**
     * @deprecated Use the FQCN directly.
     *
     * @return string
     */
    public function createReorderProductSetsFormType(): string
    {
        return ReorderProductSetsFormType::class;
    }

    public function createReorderProductSetsFormDataProvider(): ReorderProductSetsFormDataProvider
    {
        return new ReorderProductSetsFormDataProvider($this->getQueryContainer());
    }

    public function createProductAbstractTableHelper(): ProductAbstractTableHelperInterface
    {
        return new ProductAbstractTableHelper(
            $this->getProductImageFacade(),
        );
    }

    public function createDeleteProductSetForm(): FormInterface
    {
        return $this->getFormFactory()->create(DeleteProductSetForm::class, null, ['fields' => []]);
    }

    public function getProductSetFacade(): ProductSetGuiToProductSetInterface
    {
        return $this->getProvidedDependency(ProductSetGuiDependencyProvider::FACADE_PRODUCT_SET);
    }

    public function getLocaleFacade(): ProductSetGuiToLocaleInterface
    {
        return $this->getProvidedDependency(ProductSetGuiDependencyProvider::FACADE_LOCALE);
    }

    public function getUrlFacade(): ProductSetGuiToUrlInterface
    {
        return $this->getProvidedDependency(ProductSetGuiDependencyProvider::FACADE_URL);
    }

    public function getUtilEncodingService(): ProductSetGuiToUtilEncodingInterface
    {
        return $this->getProvidedDependency(ProductSetGuiDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    public function getProductImageFacade(): ProductSetGuiToProductImageInterface
    {
        return $this->getProvidedDependency(ProductSetGuiDependencyProvider::FACADE_PRODUCT_IMAGE);
    }

    public function getPriceProductFacade(): ProductSetGuiToPriceProductFacadeInterface
    {
        return $this->getProvidedDependency(ProductSetGuiDependencyProvider::FACADE_PRICE_PRODUCT);
    }

    public function getMoneyFacade(): ProductSetGuiToMoneyInterface
    {
        return $this->getProvidedDependency(ProductSetGuiDependencyProvider::FACADE_MONEY);
    }

    public function getProductSetQueryContainer(): QueryContainerProductSetGuiToProductSetInterface
    {
        return $this->getProvidedDependency(ProductSetGuiDependencyProvider::QUERY_CONTAINER_PRODUCT_SET);
    }
}
