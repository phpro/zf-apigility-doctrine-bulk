<?php

namespace spec\Phpro\Apigility\Doctrine\Bulk\Listener;

use Prophecy\Argument;

class UpdateListenerSpec extends AbstractListenerSpec
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Phpro\Apigility\Doctrine\Bulk\Listener\UpdateListener');
    }

    /**
     * @param \Zend\EventManager\EventManager $eventManager
     */
    public function it_should_attach_an_eventlistener($eventManager)
    {
        $eventManager->attach('update', Argument::containing('update'), 1000)->shouldBeCalled();
        $this->attach($eventManager);
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     * @param \Zend\Stdlib\Hydrator\HydratorInterface $hydrator
     * @param \Phpro\Apigility\Doctrine\Bulk\Event\BulkEvent $event
     * @param \stdClass $entity
     */
    public function it_should_handle_update_events($objectManager, $hydrator, $event, $entity)
    {
        $this->stubLoadEntity($objectManager, $entity);
        $this->mockSaveEntity($objectManager);

        $event->getParams()->willReturn(['id' => 1]);
        $hydrator->extract($entity)->willReturn(['merged' => true]);
        $hydrator->hydrate(['id' => 1, 'merged' => true], Argument::type('stdClass'))->shouldBeCalled();
        $event->stopPropagation(true)->shouldBeCalled();

        $result = $this->update($event);
        $result->shouldBeAnInstanceOf('Phpro\Apigility\Doctrine\Bulk\Model\Result');
    }
}
