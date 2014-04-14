<?php
namespace Phpro\Apigility\Doctrine\Bulk;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;

/**
 * Class Module
 *
 * @package Tenant
 */
class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    BootstrapListenerInterface
{
    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../../config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../../' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     *
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        $application = $e->getApplication();
        $serviceManager = $application->getServiceManager();
        $eventManager = $application->getEventManager();
        $sharedEventManager = $eventManager->getSharedEventManager();

        $identifier = 'Phpro\Apigility\Doctrine\Bulk\Service\BulkService';
        $sharedEventManager->attach($identifier, $serviceManager->get('Phpro\Apigility\Doctrine\Bulk\Listener\CreateListener'));
        $sharedEventManager->attach($identifier, $serviceManager->get('Phpro\Apigility\Doctrine\Bulk\Listener\UpdateListener'));
        $sharedEventManager->attach($identifier, $serviceManager->get('Phpro\Apigility\Doctrine\Bulk\Listener\DeleteListener'));
        $sharedEventManager->attach($identifier, $serviceManager->get('Phpro\Apigility\Doctrine\Bulk\Listener\CustomCommandListener'));
    }


}
