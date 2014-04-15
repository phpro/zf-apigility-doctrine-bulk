<?php

namespace spec\Phpro\Apigility\Doctrine\Bulk\Listener;

use Prophecy\Argument;

class DeleteListenerSpec extends AbstractListenerSpec
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Phpro\Apigility\Doctrine\Bulk\Listener\DeleteListener');
    }

    /**
     * @param \Zend\EventManager\EventManager $eventManager
     */
    public function it_should_attach_an_eventlistener($eventManager)
    {
        $eventManager->attach('delete', Argument::containing('delete'), 1000)->shouldBeCalled();
        $this->attach($eventManager);
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     * @param \Zend\Stdlib\Hydrator\HydratorInterface $hydrator
     * @param \Phpro\Apigility\Doctrine\Bulk\Event\BulkEvent $event
     * @param \stdClass $entity
     */
    public function it_should_handle_delete_events($objectManager, $hydrator, $event, $entity)
    {
        $this->stubLoadEntity($objectManager, $entity);
        $objectManager->remove($entity)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $event->getParams()->willReturn(['id' => 1]);
        $event->stopPropagation(true)->shouldBeCalled();

        $result = $this->delete($event);
        $result->shouldBeAnInstanceOf('Phpro\Apigility\Doctrine\Bulk\Model\Result');
    }
}
