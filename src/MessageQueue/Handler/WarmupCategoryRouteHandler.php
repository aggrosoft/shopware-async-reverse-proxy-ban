<?php

namespace Aggrosoft\Shopware\AsyncReverseProxyBan\MessageQueue\Handler;

use Aggrosoft\Shopware\AsyncReverseProxyBan\MessageQueue\Message\WarmupCategoryRouteMessage;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler]
class WarmupCategoryRouteHandler
{

    public function __construct(
        private readonly EntityRepository $categoryRepository,
        private readonly EntityRepository $salesChannelRepository,
        private readonly HttpClientInterface $client
    )
    {
    }

    /**
     * @param WarmupCategoryRouteMessage $message
     */
    public function __invoke(WarmupCategoryRouteMessage $message): void
    {
        $categoryId = $message->getCategoryId();
        $navigationSalesChannels = $this->getNavigationSalesChannels($categoryId);

        foreach($navigationSalesChannels as $salesChannel) {
            $domains = $salesChannel->getDomains();

            if (!$domains) {
                continue;
            }

            foreach($domains as $domain) {
                $url = $domain->getUrl() . '/navigation/' . $categoryId;
                $this->client->request(
                    'GET',
                    $url
                );
            }
        }
    }

    private function getNavigationSalesChannels(string $categoryId): EntitySearchResult
    {
        $criteria = new Criteria([$categoryId]);
        /** @var CategoryEntity $category */
        $category = $this->categoryRepository->search($criteria, Context::createDefaultContext())->first();

        $criteria = new Criteria();
        $criteria->addAssociation('domains');
        $salesChannels = $this->salesChannelRepository->search(new Criteria(), Context::createDefaultContext());

        return $salesChannels->filter(
            function(SalesChannelEntity $salesChannel) use ($category) {
                return
                    stristr($category->getPath(), $salesChannel->getNavigationCategoryId()) !== false &&
                    stristr($category->getPath(), $salesChannel->getFooterCategoryId()) !== false &&
                    stristr($category->getPath(), $salesChannel->getServiceCategoryId()) !== false;
            }
        );
    }

    public static function getHandledMessages(): iterable
    {
        return [WarmupCategoryRouteMessage::class];
    }
}