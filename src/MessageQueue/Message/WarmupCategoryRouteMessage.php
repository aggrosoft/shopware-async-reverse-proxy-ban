<?php declare(strict_types=1);

namespace Aggrosoft\Shopware\AsyncReverseProxyBan\MessageQueue\Message;

use Shopware\Core\Framework\MessageQueue\AsyncMessageInterface;

class WarmupCategoryRouteMessage implements AsyncMessageInterface
{
    private string $categoryId;

    public function __construct(string $categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function getCategoryId(): string
    {
        return $this->categoryId;
    }
}