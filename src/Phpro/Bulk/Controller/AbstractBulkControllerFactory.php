<?php

namespace Phpro\Apigility\Doctrine\Bulk\Controller;

use Phpro\Apigility\Doctrine\Bulk\Service\BulkService;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Class AbstractBulkControllerFactory
 *
 * @package Phpro\Apigility\Doctrine\Bulk\Controller
 */
class AbstractBulkControllerFactory implements AbstractFactoryInterface
{

    const CONFIG_NAMESPACE = 'doctrine-bulk-handlers';

    /**
     * Cache of canCreateServiceWithName lookups
     * @var array
     */
    protected $lookupCache = array();

    /**
     * {@inheritDoc}
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {

        if (array_key_exists($requestedName, $this->lookupCache)) {
            return $this->lookupCache[$requestedName];
        }

        if (!$serviceLocator->has('Config')) {
            return false;
        }

        // Validate object is set
        $config = $serviceLocator->get('Config');
        if (!isset($config['zf-apigility'][self::CONFIG_NAMESPACE])
            || !is_array($config['zf-apigility'][self::CONFIG_NAMESPACE])
            || !isset($config['zf-apigility'][self::CONFIG_NAMESPACE][$requestedName])) {
            $this->lookupCache[$requestedName] = false;

            return false;
        }

        // Validate object manager
        $config = $config['zf-apigility'][self::CONFIG_NAMESPACE];
        if (!isset($config[$requestedName]) || !isset($config[$requestedName]['object_manager'])) {
            throw new ServiceNotFoundException(sprintf(
                '%s requires that a valid "object_manager" is specified for listener %s; no service found',
                __METHOD__,
                $requestedName
            ));
        }

        $this->lookupCache[$requestedName] = true;
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {

        $config   = $serviceLocator->get('Config');
        $config   = $config['zf-apigility'][self::CONFIG_NAMESPACE][$requestedName];

        $className = $config['class'];
        $listeners = $config['listeners'] ? $config['listeners'] : [];
        $objectManager = $this->loadObjectManager($serviceLocator, $config);
        $hydrator = $this->loadHydrator($serviceLocator, $config);

        $bulkService = new BulkService($objectManager, $className, $hydrator);
        foreach ($listeners as $listener) {
            if (!$serviceLocator->has($listener)) {
                throw new ServiceNotCreatedException(sprintf('Invalid bulk listener %s', $listener));
            }
            $bulkService->getEventManager()->attach($serviceLocator->get($listener));
        }

        $controller = new BulkController($bulkService);
        return $controller;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $config
     *
     * @return array|object
     * @throws ServiceNotCreatedException
     */
    protected function loadObjectManager(ServiceLocatorInterface $serviceLocator, $config)
    {
        if ($serviceLocator->has($config['object_manager'])) {
            $objectManager = $serviceLocator->get($config['object_manager']);
        } else {
            throw new ServiceNotCreatedException('The object_manager could not be found.');
        }
        return $objectManager;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $config
     *
     * @return HydratorInterface
     * @throws \Zend\ServiceManager\Exception\ServiceNotCreatedException
     */
    protected function loadHydrator(ServiceLocatorInterface $serviceLocator, $config)
    {
        $hydratorManager = $serviceLocator->has('HydratorManager');
        if (!isset($config['hydrator']) || !$hydratorManager->has($config['hydrator'])) {
            throw new ServiceNotCreatedException(sprintf('Invalid hydrator specified: %s', $config['hydrator']);
        }

        return $hydratorManager->get($config['hydrator']);
    }

}