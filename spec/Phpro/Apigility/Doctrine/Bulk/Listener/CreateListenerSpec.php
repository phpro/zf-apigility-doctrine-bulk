<?php

namespace spec\Phpro\Apigility\Doctrine\Bulk\Listener;

use Prophecy\Argument;

class CreateListenerSpec extends AbstractListenerSpec
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Phpro\Apigility\Doctrine\Bulk\Listener\CreateListener');
    }

    /**
     * @param \Zend\EventManager\EventManager $eventManager
     */
    public function it_should_attach_an_eventlistener($eventManager)
    {
        $eventManager->attach('create', Argument::containing('create'), 1000)->shouldBeCalled();
        $this->attach($eventManager);
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     * @param \Zend\Stdlib\Hydrator\HydratorInterface $hydrator
     * @param \Phpro\Apigility\Doctrine\Bulk\Event\BulkEvent $event
     */
    public function it_should_handle_create_events($objectManager, $hydrator, $event)
    {
        $this->mockSaveEntity($objectManager);
        $event->getParams()->willReturn([]);
        $hydrator->hydrate([], Argument::type('stdClass'))->shouldBeCalled();
        $event->stopPropagation(true)->shouldBeCalled();

        $result = $this->create($event);
        $result->shouldBeAnInstanceOf('Phpro\Apigility\Doctrine\Bulk\Model\Result');
    }
}
