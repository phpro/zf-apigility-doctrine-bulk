<?php

namespace spec\Phpro\Apigility\Doctrine\Bulk\Controller;

use Phpro\Apigility\Doctrine\Bulk\Controller\AbstractBulkControllerFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Prophet;

class AbstractBulkControllerFactorySpec extends ObjectBehavior
{

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     */
    protected function mockConfiguration($serviceLocator)
    {
        $serviceLocator->has('Config')->willReturn(true);
        $serviceLocator->get('Config')->willReturn([
            'zf-apigility' => [
                AbstractBulkControllerFactory::CONFIG_NAMESPACE => [
                    'Phpro\Apigility\Doctrine\Bulk\Controller\BulkController' => [
                        'entity_class' => 'stdClass',
                        'object_manager' => 'doctrine-object-manager',
                        'hydrator' => 'Zend\Hydrator',
                        'listeners' => [
                            'ListenerAggregate',
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     * @param \Zend\Stdlib\Hydrator\HydratorInterface $hydrator
     * @param \Phpro\Apigility\Doctrine\Bulk\Listener\CreateListener $listener
     */
    protected function mockServices($serviceLocator, $objectManager, $hydrator, $listener)
    {
        $serviceLocator->has('doctrine-object-manager')->willReturn(true);
        $serviceLocator->get('doctrine-object-manager')->willReturn($objectManager);

        $serviceLocator->has('HydratorManager')->willReturn(true);
        $serviceLocator->get('HydratorManager')->willReturn($serviceLocator);

        $serviceLocator->has('Zend\Hydrator')->willReturn(true);
        $serviceLocator->get('Zend\Hydrator')->willReturn($hydrator);

        $serviceLocator->has('ListenerAggregate')->willReturn(true);
        $serviceLocator->get('ListenerAggregate')->willReturn($listener);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Phpro\Apigility\Doctrine\Bulk\Controller\AbstractBulkControllerFactory');
    }

    public function it_is_an_abstract_service_factory()
    {
        $this->shouldImplement('Zend\ServiceManager\AbstractFactoryInterface');
    }

    /**
     * @param \Zend\ServiceManager\AbstractPluginManager $serviceLocator
     */
    public function it_should_be_able_to_create_rpc_bulk_controllers($serviceLocator)
    {
        $serviceLocator->getServiceLocator()->willReturn($serviceLocator);
        $this->mockConfiguration($serviceLocator);

        $result = $this->canCreateServiceWithName($serviceLocator, 'PhproApigilityDoctrineBulkControllerBulkController', 'Phpro\Apigility\Doctrine\Bulk\Controller\BulkController');
        $result->shouldBe(true);
    }

    /**
     * @param \Zend\ServiceManager\AbstractPluginManager $serviceLocator
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     * @param \Zend\Stdlib\Hydrator\HydratorInterface $hydrator
     * @param \Zend\EventManager\AbstractListenerAggregate $listener
     */
    public function it_should_create_rpc_bulk_controllers($serviceLocator, $objectManager, $hydrator, $listener)
    {
        $serviceLocator->getServiceLocator()->willReturn($serviceLocator);
        $this->mockConfiguration($serviceLocator);
        $this->mockServices($serviceLocator, $objectManager, $hydrator, $listener);

        $controller = $this->createServiceWithName($serviceLocator, 'PhproApigilityDoctrineBulkControllerBulkController', 'Phpro\Apigility\Doctrine\Bulk\Controller\BulkController');
        $controller->shouldBeAnInstanceOf('Phpro\Apigility\Doctrine\Bulk\Controller\BulkController');
    }

}
