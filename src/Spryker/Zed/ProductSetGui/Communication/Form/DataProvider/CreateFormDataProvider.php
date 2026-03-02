<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSetGui\Communication\Form\DataProvider;

use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Zed\ProductSetGui\Communication\Form\CreateProductSetFormType;
use Spryker\Zed\ProductSetGui\Communication\Form\General\GeneralFormType;
use Spryker\Zed\ProductSetGui\Communication\Form\General\LocalizedGeneralFormType;
use Spryker\Zed\ProductSetGui\Communication\Form\Images\ImagesFormType;
use Spryker\Zed\ProductSetGui\Communication\Form\Images\LocalizedProductImageSetFormType;
use Spryker\Zed\ProductSetGui\Communication\Form\Seo\SeoFormType;
use Spryker\Zed\ProductSetGui\Dependency\Facade\ProductSetGuiToLocaleInterface;
use Spryker\Zed\ProductSetGui\ProductSetGuiConfig;

class CreateFormDataProvider extends AbstractProductSetFormDataProvider
{
    /**
     * @var \Spryker\Zed\ProductSetGui\Dependency\Facade\ProductSetGuiToLocaleInterface
     */
    protected $localeFacade;

    public function __construct(ProductSetGuiToLocaleInterface $localeFacade, ProductSetGuiConfig $productSetGuiConfig)
    {
        parent::__construct($productSetGuiConfig);

        $this->localeFacade = $localeFacade;
    }

    public function getData(): array
    {
        return [
            CreateProductSetFormType::FIELD_GENERAL_FORM => $this->getGeneralFormData(),
            CreateProductSetFormType::FIELD_SEO_FORM => $this->getSeoFormData(),
            CreateProductSetFormType::FIELD_IMAGES_FORM => $this->getImagesFormData(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return [
            CreateProductSetFormType::OPTION_LOCALE => $this->localeFacade->getCurrentLocaleName(),
        ];
    }

    protected function getGeneralFormData(): array
    {
        return [
            GeneralFormType::FIELD_LOCALIZED_GENERAL_FORM_COLLECTION => $this->getLocalizedFormCollectionData(),
        ];
    }

    protected function getSeoFormData(): array
    {
        return [
            SeoFormType::FIELD_LOCALIZED_SEO_FORM_COLLECTION => $this->getLocalizedFormCollectionData(),
        ];
    }

    protected function getImagesFormData(): array
    {
        $results = [];
        $results[ImagesFormType::getImageSetFormName()][] = $this->getImagesDefaultFields();

        $availableLocales = $this->localeFacade->getLocaleCollection();
        foreach ($availableLocales as $localeTransfer) {
            $results[ImagesFormType::getImageSetFormName($localeTransfer->getLocaleName())][] = $this->getImagesDefaultFields($localeTransfer);
        }

        return $results;
    }

    protected function getLocalizedFormCollectionData(): array
    {
        $results = [];
        $localeCollection = $this->localeFacade->getLocaleCollection();

        foreach ($localeCollection as $localeTransfer) {
            $results[] = [
                LocalizedGeneralFormType::FIELD_URL_PREFIX => $this->getUrlPrefix($localeTransfer),
                LocalizedGeneralFormType::FIELD_FK_LOCALE => $localeTransfer->getIdLocale(),
            ];
        }

        return $results;
    }

    protected function getImagesDefaultFields(?LocaleTransfer $localeTransfer = null): array
    {
        return [
            LocalizedProductImageSetFormType::FIELD_FK_LOCALE => ($localeTransfer ? $localeTransfer->getIdLocale() : null),
            LocalizedProductImageSetFormType::FIELD_PRODUCT_IMAGE_COLLECTION => [[]],
        ];
    }
}
