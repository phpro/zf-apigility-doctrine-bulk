<?php

namespace Phpro\Apigility\Doctrine\Bulk\Listener;

use Phpro\Apigility\Doctrine\Bulk\Event\BulkEvent;
use Zend\EventManager\EventManagerInterface;

/**
 * Class CreateListener
 *
 * @package Phpro\Apigility\Doctrine\Bulk\Listener
 */
class CreateListener extends AbstractListener
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('create', [$this, 'create'], 1000);
    }

    /**
     * @param BulkEvent $event
     */
    public function create(BulkEvent $event)
    {

    }
}