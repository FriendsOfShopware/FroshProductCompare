<?php

declare(strict_types=1);

namespace Frosh\FroshProductCompare\Subscriber;

use Shopware\Core\Content\Product\Events\ProductGatewayCriteriaEvent;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FroshProductGatewayCriteriaSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SystemConfigService $systemConfigService,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            ProductGatewayCriteriaEvent::class => [
                ['handleCriteriaLoadedRequest', 201],
            ],
        ];
    }

    public function handleCriteriaLoadedRequest(ProductGatewayCriteriaEvent $event): void
    {
        $context = $event->getSalesChannelContext();

        if (!$this->systemConfigService->getBool('FroshProductCompare.config.active', $context->getSalesChannelId())) {
            return;
        }

        $criteria = $event->getCriteria();

        $criteria->addAssociation('prices')->addAssociation('manufacturer');
    }
}
