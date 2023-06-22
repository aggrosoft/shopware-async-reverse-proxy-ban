<?php

namespace Aggrosoft\Shopware\AsyncReverseProxyBan\MessageQueue\Handler;

use Aggrosoft\Shopware\AsyncReverseProxyBan\MessageQueue\Message\InvalidateCacheTagMessage;
use Shopware\Core\Framework\MessageQueue\Handler\AbstractMessageHandler;
use Shopware\Storefront\Framework\Cache\ReverseProxy\AbstractReverseProxyGateway;

class InvalidateCacheTagHandler extends AbstractMessageHandler
{

    public function __construct(AbstractReverseProxyGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param InvalidateCacheTagMessage $message
     */
    public function handle($message): void
    {
        $this->gateway->invalidate([$message->getTag()]);
    }

    public static function getHandledMessages(): iterable
    {
        return [InvalidateCacheTagMessage::class];
    }
}