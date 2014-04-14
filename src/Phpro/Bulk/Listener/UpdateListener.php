<?php

namespace Phpro\Apigility\Doctrine\Bulk\Listener;
use Phpro\Apigility\Doctrine\Bulk\Event\BulkEvent;
use Zend\EventManager\EventManagerInterface;

/**
 * Class UpdateListener
 *
 * @package Phpro\Apigility\Doctrine\Bulk\Listener
 */
class UpdateListener extends AbstractListener
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('update', [$this, 'update'], 1000);
    }

    /**
     * @param BulkEvent $event
     */
    public function update(BulkEvent $event)
    {

    }
} 