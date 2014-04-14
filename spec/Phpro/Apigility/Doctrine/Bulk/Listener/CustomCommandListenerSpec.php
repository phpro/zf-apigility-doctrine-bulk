<?php

namespace spec\Phpro\Apigility\Doctrine\Bulk\Listener;

use Prophecy\Argument;

class CustomCommandListenerSpec extends AbstractListenerSpec
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Phpro\Apigility\Doctrine\Bulk\Listener\CustomCommandListener');
    }

    /**
     * @param \Zend\EventManager\EventManager $eventManager
     */
    public function it_should_attach_an_eventlistener($eventManager)
    {
        $eventManager->attach('*', Argument::containing('handle'), 0)->shouldBeCalled();
        $this->attach($eventManager);
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     * @param \Zend\Stdlib\Hydrator\HydratorInterface $hydrator
     * @param \Phpro\Apigility\Doctrine\Bulk\Event\BulkEvent $event
     * @param stdClass $entity
     */
    public function it_should_handle_custom_events($objectManager, $hydrator, $event, $entity)
    {
        $this->stubLoadEntity($objectManager, $entity);
        $this->mockSaveEntity($objectManager);

        $event->getName()->willReturn('changeEmail');
        $event->getParams()->willReturn(['id' => 1, 'email' => 'test@test.com']);
        $event->stopPropagation(true)->shouldBeCalled();
        $entity->changeEmail('test@test.com')->shouldBeCalled();

        $result = $this->handle($event);
        $result->shouldBeAnInstanceOf('Phpro\Apigility\Doctrine\Bulk\Model\Result');
    }
}
