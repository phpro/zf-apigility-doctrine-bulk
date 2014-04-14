<?php

namespace Phpro\Apigility\Doctrine\Bulk\Listener;

use Doctrine\Common\Persistence\ObjectManager;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\Stdlib\Hydrator\HydratorInterface;

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
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @param $objectManager
     * @param $hydrator
     */
    public function __construct($objectManager, $hydrator)
    {
        $this->objectManager = $objectManager;
        $this->hydrator = $hydrator;
    }

} 