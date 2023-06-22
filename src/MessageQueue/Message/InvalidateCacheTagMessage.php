<?php declare(strict_types=1);

namespace Aggrosoft\Shopware\AsyncReverseProxyBan\MessageQueue\Message;

class InvalidateCacheTagMessage
{
    private string $tag;

    public function __construct(string $tag)
    {
        $this->tag = $tag;
    }

    public function getTag(): string
    {
        return $this->tag;
    }
}