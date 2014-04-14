<?php

namespace spec\Phpro\Apigility\Doctrine\Bulk\Service;

use Phpro\Apigility\Doctrine\Bulk\Event\BulkEvent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BulkServiceSpec extends ObjectBehavior
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     * @param \Zend\Stdlib\Hydrator\HydratorInterface $hydrator
     */
    public function let($objectManager, $hydrator)
    {
        $className = 'Entity';
        $this->beConstructedWith($objectManager, $className, $hydrator);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Phpro\Apigility\Doctrine\Bulk\Service\BulkService');
    }

    public function it_has_an_eventmanager_with_default_listeners()
    {
        /** @var \Zend\EventManager\EventManager $eventManager */
        $eventManager = $this->getEventManager();
        $eventManager->getIdentifiers()->shouldContain('Phpro\Apigility\Doctrine\Bulk\Listener');

        $eventManager->getListeners('create')->shouldHaveCount(1);
        $eventManager->getListeners('update')->shouldHaveCount(1);
        $eventManager->getListeners('delete')->shouldHaveCount(1);
        $eventManager->getListeners('*')->shouldHaveCount(1);
    }

    /**
     * @param \Zend\EventManager\EventManager $eventManager
     * @param \Zend\EventManager\ResponseCollection $responseCollection
     * @param \Phpro\Apigility\Doctrine\Bulk\Model\Result $result
     */
    public function it_should_hanle_bulk_commands($eventManager, $responseCollection, $result)
    {
        $params = ['name' => 'test'];
        $commands = [
            ['create' => $params],
        ];

        // Mock EventManager
        $this->setEventManager($eventManager);
        $eventManager->trigger(Argument::that(function ($argument) use ($params) {
            return $argument instanceof BulkEvent
                && $argument->getName() == 'create'
                && $argument->getParams() == $params;
        }), Argument::cetera())->willReturn($responseCollection);

        $responseCollection->stopped()->willReturn(true);
        $responseCollection->last()->willReturn($result);

        $response = $this->bulk($commands);
        $response->shouldHaveCount(1);
        $response[0]->shouldBe($result);
    }

}
