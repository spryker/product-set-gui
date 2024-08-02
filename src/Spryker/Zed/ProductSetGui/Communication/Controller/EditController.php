<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductSetGui\Communication\Controller;

use Generated\Shared\Transfer\ProductSetTransfer;
use Spryker\Service\UtilText\Model\Url\Url;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\ProductSetGui\Communication\ProductSetGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductSetGui\Persistence\ProductSetGuiQueryContainerInterface getQueryContainer()
 */
class EditController extends AbstractProductSetController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function indexAction(Request $request)
    {
        $idProductSet = $this->castId($request->query->get(static::PARAM_ID));
        $dataProvider = $this->getFactory()->createUpdateFormDataProvider();

        $productSetForm = $this->getFactory()
            ->getUpdateProductSetForm(
                $dataProvider->getData($idProductSet),
                $dataProvider->getOptions(),
            )
            ->handleRequest($request);

        if ($productSetForm->isSubmitted() && $productSetForm->isValid()) {
            $productSetTransfer = $this->getFactory()
                ->createUpdateFormDataToTransferMapper()
                ->mapData($productSetForm);

            $productSetTransfer = $this->getFactory()
                ->getProductSetFacade()
                ->updateProductSet($productSetTransfer);

            $this->addSuccessMessage('Product Set "%s" updated successfully.', [
                '%s' => $productSetTransfer->getLocalizedData()[0]->getProductSetData()->getName(),
            ]);

            return $this->redirectResponse(
                Url::generate('/product-set-gui/view', [
                    static::PARAM_ID => $idProductSet,
                ])->build(),
            );
        }

        $localeTransfer = $this->getFactory()->getLocaleFacade()->getCurrentLocale();

        return $this->viewResponse([
            'productSetForm' => $productSetForm->createView(),
            'productSetFormTabs' => $this->getFactory()->createProductSetFormTabs()->createView(),
            'localeCollection' => $this->getFactory()->getLocaleFacade()->getLocaleCollection(),
            'productTable' => $this->getFactory()->createProductTable($localeTransfer, $idProductSet)->render(),
            'productAbstractSetTable' => $this->getFactory()->createProductAbstractSetUpdateTable($localeTransfer, $idProductSet)->render(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function activateAction(Request $request)
    {
        $form = $this->getFactory()->createActivateProductSetForm()->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addErrorMessage('CSRF token is not valid.');

            return $this->redirectResponse(
                Url::generate('/product-set-gui')->build(),
            );
        }

        $idProductSet = $this->castId($request->query->get(static::PARAM_ID));

        $productSetTransfer = new ProductSetTransfer();
        $productSetTransfer
            ->setIdProductSet($idProductSet)
            ->setIsActive(true);

        $this->getFactory()
            ->getProductSetFacade()
            ->updateProductSet($productSetTransfer);

        $this->addSuccessMessage('Product Set #%d activated successfully.', [
            '%d' => $productSetTransfer->getIdProductSet(),
        ]);

        return $this->redirectResponse(
            Url::generate('/product-set-gui')->build(),
        );
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deactivateAction(Request $request)
    {
        $form = $this->getFactory()->createDeactivateProductSetForm()->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addErrorMessage('CSRF token is not valid.');

            return $this->redirectResponse(
                Url::generate('/product-set-gui')->build(),
            );
        }

        $idProductSet = $this->castId($request->query->get(static::PARAM_ID));

        $productSetTransfer = new ProductSetTransfer();
        $productSetTransfer
            ->setIdProductSet($idProductSet)
            ->setIsActive(false);

        $this->getFactory()
            ->getProductSetFacade()
            ->updateProductSet($productSetTransfer);

        $this->addSuccessMessage('Product Set #%d deactivated successfully.', [
            '%d' => $productSetTransfer->getIdProductSet(),
        ]);

        return $this->redirectResponse(
            Url::generate('/product-set-gui')->build(),
        );
    }
}
