<?php

namespace Aggrosoft\Shopware\AsyncReverseProxyBan\Subscriber;

use Aggrosoft\Shopware\AsyncReverseProxyBan\MessageQueue\Message\WarmupCategoryRouteMessage;
use Aggrosoft\Shopware\AsyncReverseProxyBan\MessageQueue\Message\WarmupProductDetailRouteMessage;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Adapter\Cache\InvalidateCacheEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class InvalidateCacheEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MessageBusInterface $bus)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            InvalidateCacheEvent::class => 'onInvalidateCache'
        ];
    }

    public function onInvalidateCache(InvalidateCacheEvent $event)
    {
        $keys = $event->getKeys();

        foreach($keys as $key) {
            if (str_starts_with($key, 'product-detail-route-')) {
                $productId = str_replace('product-detail-route-', '', $key);
                $this->bus->dispatch(new WarmupProductDetailRouteMessage($productId));
            }elseif (str_starts_with($key, 'category-route-')) {
                $categoryId = str_replace('category-route-', '', $key);
                $this->bus->dispatch(new WarmupCategoryRouteMessage($categoryId));
            }
        }
    }
}