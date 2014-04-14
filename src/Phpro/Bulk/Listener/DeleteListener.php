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
    const EVENT_NAME = 'delete';
    const EVENT_PRIORITY = 1000;


    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(self::EVENT_NAME, [$this, 'delete'], self::EVENT_PRIORITY);
    }

    /**
     * @param BulkEvent $event
     *
     * @return \Phpro\Apigility\Doctrine\Bulk\Model\Result
     */
    public function delete(BulkEvent $event)
    {
        $entity = $this->loadEntity($event);
        $this->objectManager->remove($entity);
        $this->objectManager->flush($entity);

        $event->stopPropagation(true);
        return $this->createResult(self::EVENT_NAME, $entity);
    }
} 