<?php

namespace Aggrosoft\Shopware\AsyncReverseProxyBan\MessageQueue\Handler;

use Aggrosoft\Shopware\AsyncReverseProxyBan\MessageQueue\Message\WarmupProductDetailRouteMessage;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler]
class WarmupProductDetailRouteHandler
{

    public function __construct(
        private readonly EntityRepository $visibilityRepository,
        private readonly HttpClientInterface $client
    )
    {
    }

    /**
     * @param WarmupProductDetailRouteMessage $message
     */
    public function __invoke(WarmupProductDetailRouteMessage $message): void
    {
        $productId = $message->getProductId();
        $visibilities = $this->getSalesChannelVisibilities($productId);

        foreach($visibilities as $visibility) {
            $salesChannel = $visibility->getSalesChannel();
            $domains = $salesChannel->getDomains();

            foreach($domains as $domain) {
                $url = $domain->getUrl() . '/detail/' . $productId;
                $this->client->request(
                    'GET',
                    $url
                );
            }
        }
    }

    private function getSalesChannelVisibilities(string $productId): EntitySearchResult
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $productId));
        $criteria->addAssociation('salesChannel.domains');

        /** @var EntitySearchResult $visibilities */
        $visibilities = $this->visibilityRepository->search($criteria, Context::createDefaultContext());
        return $visibilities;
    }

    public static function getHandledMessages(): iterable
    {
        return [WarmupProductDetailRouteMessage::class];
    }
}