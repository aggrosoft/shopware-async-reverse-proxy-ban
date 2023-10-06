<?php

namespace Aggrosoft\Shopware\AsyncReverseProxyBan\Storefront\Framework\Cache\ReverseProxy;

use Aggrosoft\Shopware\AsyncReverseProxyBan\MessageQueue\Message\InvalidateCacheTagMessage;
use Shopware\Core\Framework\Adapter\Cache\InvalidateCacheEvent;
use Shopware\Storefront\Framework\Cache\ReverseProxy\AbstractReverseProxyGateway;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\StoreInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ReverseProxyCache implements StoreInterface
{

    private StoreInterface $decorated;
    private MessageBusInterface $bus;
    private AbstractReverseProxyGateway $gateway;

    public function __construct(StoreInterface $decorated, MessageBusInterface $bus, AbstractReverseProxyGateway $gateway)
    {
        $this->decorated = $decorated;
        $this->bus = $bus;
        $this->gateway = $gateway;
    }

    public function __destruct()
    {
        $this->gateway->flush();
    }

    public function __invoke(InvalidateCacheEvent $event): void
    {
        $tags = $event->getKeys();
        foreach($tags as $tag){
            $this->bus->dispatch(new InvalidateCacheTagMessage($tag));
        }
    }

    public function lookup(Request $request): ?Response
    {
        return $this->decorated->lookup($request);
    }

    public function write(Request $request, Response $response): string
    {
        return $this->decorated->write($request, $response);
    }

    public function invalidate(Request $request): void
    {
        $this->decorated->invalidate($request);
    }

    public function lock(Request $request): bool
    {
        return $this->decorated->lock($request);
    }

    public function unlock(Request $request): bool
    {
        return $this->decorated->unlock($request);
    }

    public function isLocked(Request $request): bool
    {
        return $this->decorated->isLocked($request);
    }

    public function purge(string $url): bool
    {
        return $this->decorated->purge($url);
    }

    public function cleanup(): void
    {
        $this->decorated->cleanup();
    }
}