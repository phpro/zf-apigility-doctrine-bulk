<?php

namespace Phpro\Apigility\Doctrine\Bulk\Service;
use Doctrine\Common\Persistence\ObjectManager;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Class BulkService
 *
 * @package Phpro\Apigility\Doctrine\Bulk\Service
 */
class BulkService
{

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

} 