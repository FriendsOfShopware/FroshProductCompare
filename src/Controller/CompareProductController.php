<?php declare(strict_types=1);

namespace Frosh\FroshProductCompare\Controller;

use Frosh\FroshProductCompare\Page\CompareProductPageLoader;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class CompareProductController extends StorefrontController
{
    private CompareProductPageLoader $compareProductPageLoader;

    private GenericPageLoader $genericPageLoader;

    public function __construct(
        CompareProductPageLoader $compareProductPageLoader,
        GenericPageLoader $genericPageLoader
    ) {
        $this->compareProductPageLoader = $compareProductPageLoader;
        $this->genericPageLoader = $genericPageLoader;
    }

    /**
     * @Route("/compare", name="frontend.compare.page", options={"seo"="false"}, methods={"GET"})
     */
    public function comparePage(Request $request, SalesChannelContext $context): Response
    {
        $page = $this->genericPageLoader->load($request, $context);
        return $this->renderStorefront('@FroshProductCompare/storefront/page/compare.html.twig', compact('page'));
    }

    /**
     * @Route("/compare/content", name="frontend.compare.content", options={"seo"="false"}, methods={"POST"}, defaults={"XmlHttpRequest"=true}))
     */
    public function comparePageContent(Request $request, SalesChannelContext $context): Response
    {
        $productIds = $request->request->get('productIds', []);

        $page = $this->compareProductPageLoader->load($productIds, $request, $context);

        return $this->renderStorefront('@FroshProductCompare/storefront/component/compare/content.html.twig', ['page' => $page]);
    }

    /**
     * @Route("/compare/offcanvas", name="frontend.compare.offcanvas", options={"seo"="false"}, methods={"POST"}, defaults={"XmlHttpRequest"=true})
     */
    public function offcanvas(Request $request, SalesChannelContext $context): Response
    {
        $productIds = $request->request->get('productIds', []);

        $page = $this->compareProductPageLoader->loadPreview($productIds, $request, $context);

        return $this->renderStorefront('@FroshProductCompare/storefront/component/compare/offcanvas-compare-list.html.twig', ['page' => $page]);
    }
}
