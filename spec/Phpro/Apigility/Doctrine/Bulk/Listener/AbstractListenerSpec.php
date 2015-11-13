<?php

namespace spec\Phpro\Apigility\Doctrine\Bulk\Listener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Prophet;

abstract class AbstractListenerSpec extends ObjectBehavior
{

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     * @param \Zend\Stdlib\Hydrator\HydratorInterface $hydrator
     */
    public function let($objectManager, $hydrator)
    {
        $className = 'stdClass';
        $this->beConstructedWith($objectManager, $className, $hydrator);
        $this->stubMetaData($objectManager);
    }

    public function it_is_a_bulk_listener()
    {
        $this->shouldHaveType('Phpro\Apigility\Doctrine\Bulk\Listener\AbstractListener');
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    protected function stubMetaData($objectManager)
    {
        $prophet = new Prophet();
        $meta = $prophet->prophesize('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $meta->getIdentifierFieldNames()->willReturn(['id']);
        $meta->getIdentifierValues(Argument::any())->willReturn([1]);

        $objectManager->getClassMetadata('stdClass')->willReturn($meta);
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    protected function mockSaveEntity($objectManager)
    {
        $objectManager->persist(Argument::any())->shouldBeCalled();
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    protected function stubLoadEntity($objectManager, $entity)
    {
        $objectManager->find('stdClass', 1)->willReturn($entity);
    }
}
