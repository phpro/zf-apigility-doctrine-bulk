<?php

namespace Phpro\Apigility\Doctrine\Bulk\Service;
use Doctrine\Common\Persistence\ObjectManager;
use Phpro\Apigility\Doctrine\Bulk\Event\BulkEvent;
use Phpro\Apigility\Doctrine\Bulk\Listener;
use Phpro\Apigility\Doctrine\Bulk\Model\Result;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Stdlib\Hydrator\HydratorInterface;

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
     * @var string
     */
    protected $eventIdentifier = 'Phpro\Apigility\Doctrine\Bulk\Listener';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var String
     */
    protected $className;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @param $objectManager
     * @param $className
     * @param $hydrator
     */
    public function __construct($objectManager, $className, $hydrator)
    {
        $this->objectManager = $objectManager;
        $this->className = $className;
        $this->hydrator = $hydrator;
    }

    /**
     * Attach default event listeners
     */
    protected function attachDefaultListeners()
    {
        $events = $this->getEventManager();
        $events->attach(new Listener\CreateListener($this->objectManager, $this->className, $this->hydrator));
        $events->attach(new Listener\UpdateListener($this->objectManager, $this->className, $this->hydrator));
        $events->attach(new Listener\DeleteListener($this->objectManager, $this->className, $this->hydrator));
        $events->attach(new Listener\CustomCommandListener($this->objectManager, $this->className, $this->hydrator));
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

            $results[] = $this->handleCommand($command, $params);
        }
        return [];
    }

    /**
     * @param $command
     * @param $params
     *
     * @return Result
     */
    protected function handleCommand($command, $params)
    {
        try {
            $event = new BulkEvent($command, $this, $params);
            $response = $this->getEventManager()->trigger($event, function($result) {
                return $result instanceof Result;
            });

            $result = $response->last();
            if (!$response->stopped()) {
                throw new \Exception(sprintf('Unknown bulk command %s', $command));
            }

        } catch (\Exception $e) {
            $id = isset($params['id']) ? $params['id'] : 'unknown';
            $result = new Result($command, $id);
            $result->setError($e->getMessage());
        }

        return $result;
    }

}
