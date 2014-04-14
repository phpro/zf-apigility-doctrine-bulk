<?php

namespace Phpro\Apigility\Doctrine\Bulk\Listener;

use Doctrine\Common\Persistence\ObjectManager;
use Phpro\Apigility\Doctrine\Bulk\Event\BulkEvent;
use Phpro\Apigility\Doctrine\Bulk\Model\Result;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\Stdlib\Hydrator\HydratorInterface;
use ZF\ApiProblem\ApiProblem;

/**
 * Class AbstractListener
 *
 * @package Phpro\Apigility\Doctrine\Bulk\Listener
 */
abstract class AbstractListener extends AbstractListenerAggregate
{

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
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
     * @param BulkEvent $e
     *
     * @return object
     */
    protected function loadEntity(BulkEvent $e)
    {
        $params = $e->getParams();
        $id = isset($params['id']) ? $params['id'] : 0;
        $entity = $this->objectManager->find($this->className, $id);

        if (!$entity) {
            return new ApiProblem(404, 'Entity with id ' . $id . ' was not found');
        }

        return $entity;
    }

    /**
     * @param $entity
     */
    protected function saveEntity($entity)
    {
        $this->objectManager->persist($entity);
        $this->objectManager->flush();
    }

    /**
     * @param $command
     * @param $entity
     *
     * @return Result
     */
    protected function createResult($command, $entity)
    {
        $meta = $this->objectManager->getClassMetadata($this->className);
        $identifiers = $meta->getIdentifierValues($entity);

        return new Result($command, current($identifiers));
    }

}
