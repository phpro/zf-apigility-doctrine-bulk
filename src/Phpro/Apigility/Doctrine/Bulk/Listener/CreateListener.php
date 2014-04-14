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

    const EVENT_NAME = 'create';
    const EVENT_PRIORITY = 1000;

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(self::EVENT_NAME, [$this, 'create'], self::EVENT_PRIORITY);
    }

    /**
     * @param BulkEvent $e
     *
     * @return object
     */
    protected function loadEntity(BulkEvent $e)
    {
        return new $this->className();
    }

    /**
     * @param BulkEvent $event
     *
     * @return object|string
     */
    public function create(BulkEvent $event)
    {
        $data = $event->getParams();
        $entity = $this->loadEntity($event);

        $this->hydrator->hydrate((array) $data, $entity);
        $this->saveEntity($entity);

        $event->stopPropagation(true);
        return $this->createResult(self::EVENT_NAME, $entity);
    }

}
