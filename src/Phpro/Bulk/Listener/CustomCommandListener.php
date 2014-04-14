<?php

namespace Phpro\Apigility\Doctrine\Bulk\Listener;
use Phpro\Apigility\Doctrine\Bulk\Event\BulkEvent;
use Phpro\Apigility\Doctrine\Bulk\Model\Result;
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
     *
     * @return bool|Result
     */
    public function handle(BulkEvent $event)
    {
        $entity = $this->loadEntity($event);
        $command = $event->getName();

        if (!method_exists($entity, $command)) {
            return false;
        }

        $this->saveEntity($entity);
        $event->stopPropagation(true);
        return $this->createResult($command, $entity);
    }

} 