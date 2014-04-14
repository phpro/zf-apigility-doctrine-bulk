<?php

namespace Phpro\Apigility\Doctrine\Bulk\Service;
use Doctrine\Common\Persistence\ObjectManager;
use Phpro\Apigility\Doctrine\Bulk\Event\BulkEvent;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class BulkService
 *
 * @package Phpro\Apigility\Doctrine\Bulk\Service
 */
class BulkService
    implements EventManagerAwareInterface
{

    use EventManagerAwareTrait;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var String
     */
    protected $className;

    /**
     * @param $objectManager
     * @param $className
     */
    public function __construct($objectManager, $className)
    {
        $this->objectManager = $objectManager;
        $this->className = $className;
    }

    /**
     * @param $actions
     *
     * @return array
     */
    public function bulk($actions)
    {
        $results = [];
        foreach ($actions as $action) {
            $command = current(array_keys($action));
            $params = $action[$command];
            $target = $this->loadObject($params);

            $event = new BulkEvent($command, $target, $params);
            $response = $this->getEventManager()->trigger($event);
            $results[] = $response->last();
        }
        return [];
    }

    /**
     * @param $params
     *
     * @return null|Object
     */
    protected function loadObject($params)
    {
        if (!isset($params['id'])) {
            return null;
        }

        $this->objectManager->find($this->className, $params['id']);
    }

}
