<?php

namespace Aggrosoft\Shopware\AsyncReverseProxyBan\MessageQueue\Handler;

use Aggrosoft\Shopware\AsyncReverseProxyBan\MessageQueue\Message\InvalidateCacheTagMessage;
use Shopware\Storefront\Framework\Cache\ReverseProxy\AbstractReverseProxyGateway;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class InvalidateCacheTagHandler
{

    public function __construct(AbstractReverseProxyGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param InvalidateCacheTagMessage $message
     */
    public function __invoke(InvalidateCacheTagMessage $message): void
    {
        $this->gateway?->invalidate([$message->getTag()]);
    }

    public static function getHandledMessages(): iterable
    {
        return [InvalidateCacheTagMessage::class];
    }
}