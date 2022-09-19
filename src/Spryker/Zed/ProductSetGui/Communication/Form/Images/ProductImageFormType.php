<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSetGui\Communication\Form\Images;

use Spryker\Zed\Gui\Communication\Form\Type\FormattedNumberType;
use Spryker\Zed\Gui\Communication\Form\Type\ImageType;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @method \Spryker\Zed\ProductSetGui\Communication\ProductSetGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductSetGui\Persistence\ProductSetGuiQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\ProductSetGui\ProductSetGuiConfig getConfig()
 */
class ProductImageFormType extends AbstractType
{
    /**
     * @var string
     */
    public const FIELD_ID_PRODUCT_IMAGE = 'id_product_image';

    /**
     * @var string
     */
    public const FIELD_IMAGE_SMALL = 'external_url_small';

    /**
     * @var string
     */
    public const FIELD_IMAGE_LARGE = 'external_url_large';

    /**
     * @var string
     */
    public const FIELD_SORT_ORDER = 'sort_order';

    /**
     * @var string
     */
    public const FIELD_IMAGE_PREVIEW = 'image_preview';

    /**
     * @var string
     */
    public const FIELD_IMAGE_PREVIEW_LARGE_URL = 'image_preview_large_url';

    /**
     * @var string
     */
    public const FIELD_FK_IMAGE_SET_ID = 'fk_image_set_id';

    /**
     * @var string
     */
    public const OPTION_IMAGE_PREVIEW_LARGE_URL = 'option_image_preview_large_url';

    /**
     * @var int
     */
    protected const MAX_SORT_ORDER_VALUE = 2147483647; // 32 bit integer

    /**
     * @var int
     */
    protected const MIN_SORT_ORDER_VALUE = 0;

    /**
     * @var int
     */
    protected const DEFAULT_SORT_ORDER_VALUE = 0;

    /**
     * @uses \Spryker\Zed\Gui\Communication\Form\Type\ImageType::OPTION_IMAGE_WIDTH
     *
     * @var string
     */
    protected const OPTION_IMAGE_WIDTH = 'image_width';

    /**
     * @var string
     */
    protected const OPTION_LOCALE = 'locale';

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'product_image_collection';
    }

    /**
     * @deprecated Use {@link getBlockPrefix()} instead.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this
            ->addProductImageIdHiddenField($builder)
            ->addProductImageLargeUrlField($builder)
            ->addImageSetIdHiddenField($builder)
            ->addImagePreviewField($builder)
            ->addImageSmallField($builder)
            ->addImageBigField($builder)
            ->addSortOrderField($builder, $options);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            static::OPTION_LOCALE => null,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addProductImageIdHiddenField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ID_PRODUCT_IMAGE, HiddenType::class);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addProductImageLargeUrlField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_IMAGE_PREVIEW_LARGE_URL, ImageType::class, [
            'label' => false,
            static::OPTION_IMAGE_WIDTH => 150,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addImageSetIdHiddenField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_FK_IMAGE_SET_ID, HiddenType::class);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addImagePreviewField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_IMAGE_PREVIEW, ImageType::class, [
            'label' => false,
            static::OPTION_IMAGE_WIDTH => 150,
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addImageSmallField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_IMAGE_SMALL, TextType::class, [
            'label' => 'Small Image URL',
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new Length([
                    'min' => 0,
                    'max' => 2048,
                ]),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addImageBigField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_IMAGE_LARGE, TextType::class, [
            'label' => 'Large Image URL',
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new Length([
                    'min' => 0,
                    'max' => 2048,
                ]),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    protected function addSortOrderField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_SORT_ORDER, FormattedNumberType::class, [
            'locale' => $options[static::OPTION_LOCALE],
            'constraints' => [
                new NotBlank(),
                new LessThanOrEqual([
                    'value' => static::MAX_SORT_ORDER_VALUE,
                ]),
                new GreaterThanOrEqual([
                    'value' => static::MIN_SORT_ORDER_VALUE,
                ]),
            ],
            'attr' => [
                'data-sort-order' => static::DEFAULT_SORT_ORDER_VALUE,
            ],
        ]);

        return $this;
    }
}
