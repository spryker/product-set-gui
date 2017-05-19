<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSetGui\Communication;

use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\ProductSetGui\Communication\Form\DataMapper\CreateFormDataToTransferMapper;
use Spryker\Zed\ProductSetGui\Communication\Form\DataMapper\UpdateFormDataToTransferMapper;
use Spryker\Zed\ProductSetGui\Communication\Form\DataProvider\CreateFormDataProvider;
use Spryker\Zed\ProductSetGui\Communication\Form\DataProvider\UpdateFormDataProvider;
use Spryker\Zed\ProductSetGui\Communication\Form\CreateProductSetFormType;
use Spryker\Zed\ProductSetGui\Communication\Form\UpdateProductSetFormType;
use Spryker\Zed\ProductSetGui\Communication\Table\ProductAbstractSetTable;
use Spryker\Zed\ProductSetGui\Communication\Table\ProductSetTable;
use Spryker\Zed\ProductSetGui\Communication\Table\ProductTable;
use Spryker\Zed\ProductSetGui\Communication\Tabs\ProductSetFormTabs;
use Spryker\Zed\ProductSetGui\ProductSetGuiDependencyProvider;

/**
 * @method \Spryker\Zed\ProductSetGui\ProductSetGuiConfig getConfig()
 * @method \Spryker\Zed\ProductSetGui\Persistence\ProductSetGuiQueryContainerInterface getQueryContainer()
 */
class ProductSetGuiCommunicationFactory extends AbstractCommunicationFactory
{

    /**
     * @return \Spryker\Zed\ProductSetGui\Communication\Form\DataProvider\CreateFormDataProvider
     */
    public function createCreateFormDataProvider()
    {
        return new CreateFormDataProvider($this->getLocaleFacade());
    }

    /**
     * @return \Spryker\Zed\ProductSetGui\Communication\Form\DataProvider\UpdateFormDataProvider
     */
    public function createUpdateFormDataProvider()
    {
        return new UpdateFormDataProvider($this->getProductSetFacade(), $this->getLocaleFacade());
    }

    /**
     * @param array $data
     * @param array $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createCreateProductSetForm(array $data = [], array $options = [])
    {
        return $this->getFormFactory()->create($this->createCreateProductSetFormType(), $data, $options);
    }

    /**
     * @param array $data
     * @param array $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createUpdateProductSetForm(array $data = [], array $options = [])
    {
        return $this->getFormFactory()->create($this->createUpdateProductSetFormType(), $data, $options);
    }

    /**
     * @return \Spryker\Zed\ProductSetGui\Communication\Form\DataMapper\CreateFormDataToTransferMapper
     */
    public function createCreateFormDataToTransferMapper()
    {
        return new CreateFormDataToTransferMapper($this->getLocaleFacade());
    }

    /**
     * @return \Spryker\Zed\ProductSetGui\Communication\Form\DataMapper\UpdateFormDataToTransferMapper
     */
    public function createUpdateFormDataToTransferMapper()
    {
        return new UpdateFormDataToTransferMapper($this->getLocaleFacade());
    }

    /**
     * @return \Spryker\Zed\Gui\Communication\Tabs\AbstractTabs
     */
    public function createProductSetFormTabs()
    {
        return new ProductSetFormTabs();
    }

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return \Spryker\Zed\ProductSetGui\Communication\Table\ProductSetTable
     */
    public function createProductSetTable(LocaleTransfer $localeTransfer)
    {
        return new ProductSetTable($this->getProductSetQueryContainer(), $localeTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param int|null $idProductSet
     *
     * @return \Spryker\Zed\ProductSetGui\Communication\Table\ProductTable
     */
    public function createProductTable(LocaleTransfer $localeTransfer, $idProductSet = null)
    {
        return new ProductTable($this->getQueryContainer(), $this->getUtilEncodingService(), $localeTransfer, $idProductSet);
    }

    /**
     * @param int $idProductSet
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return ProductAbstractSetTable
     */
    public function createProductAbstractSetTable(LocaleTransfer $localeTransfer, $idProductSet)
    {
        return new ProductAbstractSetTable($this->getQueryContainer(), $this->getUtilEncodingService(), $localeTransfer, $idProductSet);
    }

    /**
     * @return \Symfony\Component\Form\AbstractType
     */
    protected function createCreateProductSetFormType()
    {
        return new CreateProductSetFormType();
    }

    /**
     * @return \Symfony\Component\Form\AbstractType
     */
    protected function createUpdateProductSetFormType()
    {
        return new UpdateProductSetFormType();
    }

    /**
     * @return \Spryker\Zed\ProductSetGui\Dependency\Facade\ProductSetGuiToProductSetInterface
     */
    public function getProductSetFacade()
    {
        return $this->getProvidedDependency(ProductSetGuiDependencyProvider::FACADE_PRODUCT_SET);
    }

    /**
     * @return \Spryker\Zed\ProductSetGui\Dependency\Facade\ProductSetGuiToLocaleInterface
     */
    public function getLocaleFacade()
    {
        return $this->getProvidedDependency(ProductSetGuiDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return \Spryker\Zed\ProductSetGui\Dependency\Facade\ProductSetGuiToUrlInterface
     */
    public function getUrlFacade()
    {
        return $this->getProvidedDependency(ProductSetGuiDependencyProvider::FACADE_URL);
    }

    /**
     * @return \Spryker\Zed\ProductSetGui\Dependency\QueryContainer\ProductSetGuiToProductSetInterface
     */
    public function getProductSetQueryContainer()
    {
        return $this->getProvidedDependency(ProductSetGuiDependencyProvider::QUERY_CONTAINER_PRODUCT_SET);
    }

    /**
     * @return \Spryker\Zed\ProductSetGui\Dependency\Service\ProductSetGuiToUtilEncodingInterface
     */
    public function getUtilEncodingService()
    {
        return $this->getProvidedDependency(ProductSetGuiDependencyProvider::SERVICE_UTIL_ENCODING);
    }

}