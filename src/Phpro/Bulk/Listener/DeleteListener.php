<?php

namespace Phpro\Apigility\Doctrine\Bulk\Listener;
use Phpro\Apigility\Doctrine\Bulk\Event\BulkEvent;
use Zend\EventManager\EventManagerInterface;

/**
 * Class DeleteListener
 *
 * @package Phpro\Apigility\Doctrine\Bulk\Listener
 */
class DeleteListener extends AbstractListener
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('delete', [$this, 'delete'], 1000);
    }

    /**
     * @param BulkEvent $event
     */
    public function delete(BulkEvent $event)
    {

    }
} 