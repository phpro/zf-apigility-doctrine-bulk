<?php

namespace Phpro\Apigility\Doctrine\Bulk\Listener;

use ReflectionMethod;
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

    const EVENT_PRIORITY = 0;

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('*', [$this, 'handle'], self::EVENT_PRIORITY);
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

        $response = $this->runCommand($command, $entity, $event->getParams());
        $this->saveEntity($entity);

        $event->stopPropagation(true);
        $result = $this->createResult($command, $entity);

        // Add params:
        if (is_array($response)) {
            $result->addParams($response);
        }

        return $result;
    }

    /**
     * @param $command
     * @param $entity
     * @param $methodParams
     *
     * @return mixed
     */
    protected function runCommand($command, $entity, $methodParams)
    {
        $rm = new ReflectionMethod($entity, $command);

        $args = [];
        foreach ($rm->getParameters() as $param) {
            if (array_key_exists(strtolower($param->getName()), $methodParams)) {
                $args[] = $methodParams[strtolower($param->getName())];
            } else {
                $args[] = $param->getDefaultValue();
            }
        }

        return $rm->invokeArgs($entity, $args);
    }

}
