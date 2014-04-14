<?php

namespace spec\Phpro\Apigility\Doctrine\Bulk\Event;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BulkEventSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Phpro\Apigility\Doctrine\Bulk\Event\BulkEvent');
    }

    public function it_is_an_event()
    {
        $this->shouldHaveType('Zend\EventManager\Event');
    }
}
