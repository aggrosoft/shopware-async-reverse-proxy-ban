<?php declare(strict_types=1);

namespace Aggrosoft\Shopware\AsyncReverseProxyBan\MessageQueue\Message;

use Shopware\Core\Framework\MessageQueue\AsyncMessageInterface;

class WarmupProductDetailRouteMessage implements AsyncMessageInterface
{
    private string $productId;

    public function __construct(string $productId)
    {
        $this->productId = $productId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }
}