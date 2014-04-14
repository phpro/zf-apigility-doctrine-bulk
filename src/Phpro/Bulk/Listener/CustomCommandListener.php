<?php

namespace Phpro\Apigility\Doctrine\Bulk\Listener;
use Phpro\Apigility\Doctrine\Bulk\Event\BulkEvent;
use Zend\EventManager\EventManagerInterface;

/**
 * Class CustomCommandListener
 *
 * @package Phpro\Apigility\Doctrine\Bulk\Listener
 */
class CustomCommandListener extends AbstractListener
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('*', [$this, 'handle'], 0);
    }

    /**
     * @param BulkEvent $event
     */
    public function handle(BulkEvent $event)
    {

    }
} 