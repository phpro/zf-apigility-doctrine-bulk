<?php

namespace Phpro\Apigility\Doctrine\Bulk\Controller;

use Phpro\Apigility\Doctrine\Bulk\Service\BulkService;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
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

        $serviceManager = $serviceLocator->getServiceLocator();
        if (!$serviceManager->has('Config')) {
            return false;
        }

        // Validate object is set
        $config = $serviceManager->get('Config');
        if (!isset($config['zf-apigility'][self::CONFIG_NAMESPACE])
            || !is_array($config['zf-apigility'][self::CONFIG_NAMESPACE])
            || !isset($config['zf-apigility'][self::CONFIG_NAMESPACE][$requestedName])) {
            $this->lookupCache[$requestedName] = false;
            return false;
        }

        $this->lookupCache[$requestedName] = true;
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        // Load configuration:
        $serviceManager = $serviceLocator->getServiceLocator();
        $config   = $serviceManager->get('Config');
        $config   = $config['zf-apigility'][self::CONFIG_NAMESPACE][$requestedName];

        // Load dependencies
        $className = $this->loadEntityClass($config);
        $objectManager = $this->loadObjectManager($serviceManager, $config);
        $hydrator = $this->loadHydrator($serviceManager, $config);
        $listeners = $this->loadListeners($serviceManager, $config);

        // Configure a bulk service
        $bulkService = new BulkService($objectManager, $className, $hydrator);
        if (count($listeners)) {
            foreach ($listeners as $listener) {
                $bulkService->getEventManager()->attach($listener);
            }
        }

        // Initialize controller
        $className = isset($config['entity']) ? $config['class'] : $requestedName;
        $className = $this->normalizeClassname($className);
        $controller = new $className($bulkService);
        return $controller;
    }

    /**
     * @param $className
     *
     * @return string
     */
    protected function normalizeClassname($className)
    {
        return '\\' . ltrim($className, '\\');
    }


    /**
     * @param $config
     *
     * @return mixed
     * @throws \Zend\ServiceManager\Exception\ServiceNotCreatedException
     */
    protected function loadEntityClass($config)
    {
        if (!isset($config['entity_class'])) {
            throw new ServiceNotCreatedException('The entity_class could not be found.');
        }
        return $config['entity_class'];
    }

    /**
     * @param ServiceLocatorInterface $serviceManager
     * @param                         $config
     *
     * @return array|object
     * @throws ServiceNotCreatedException
     */
    protected function loadObjectManager(ServiceLocatorInterface $serviceManager, $config)
    {
        if (!isset($config['object_manager']) || !$serviceManager->has($config['object_manager'])) {
            throw new ServiceNotCreatedException('The object_manager could not be found.');

        }

        $objectManager = $serviceManager->get($config['object_manager']);
        return $objectManager;
    }

    /**
     * @param ServiceLocatorInterface $serviceManager
     * @param                         $config
     *
     * @return HydratorInterface
     * @throws \Zend\ServiceManager\Exception\ServiceNotCreatedException
     */
    protected function loadHydrator(ServiceLocatorInterface $serviceManager, $config)
    {
        $hydratorManager = $serviceManager->get('HydratorManager');
        if (!isset($config['hydrator']) || !$hydratorManager->has($config['hydrator'])) {
            throw new ServiceNotCreatedException(sprintf('Invalid hydrator specified: %s', $config['hydrator']));
        }

        return $hydratorManager->get($config['hydrator']);
    }

    /**
     * @param ServiceLocatorInterface $serviceManager
     * @param                         $config
     *
     * @return array
     * @throws \Zend\ServiceManager\Exception\ServiceNotCreatedException
     */
    protected function loadListeners(ServiceLocatorInterface $serviceManager, $config)
    {
        $result = [];
        $listeners = isset($config['listeners']) ? $config['listeners'] : [];

        foreach ($listeners as $listener) {
            if (!$serviceManager->has($listener)) {
                throw new ServiceNotCreatedException(sprintf('Invalid bulk listener %s', $listener));
            }
            $result[] = $serviceManager->get($listener);
        }
        return $result;
    }

}
