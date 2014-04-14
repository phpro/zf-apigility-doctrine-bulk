<?php

namespace spec\Phpro\Apigility\Doctrine\Bulk\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ResultSpec extends ObjectBehavior
{

    public function let()
    {
        $this->beConstructedWith('command', 1);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Phpro\Apigility\Doctrine\Bulk\Model\Result');
    }

    public function it_is_json_serializable()
    {
        $this->shouldImplement('Zend\Stdlib\JsonSerializable');

        $result = $this->jsonSerialize();
        $result['command']->shouldBe('command');
        $result['id']->shouldBe(1);
        $result['isSuccess']->shouldBe(true);
    }

    public function it_should_be_able_to_add_parameters()
    {
        $this->addParams(['key' => 'value']);

        $result = $this->jsonSerialize();
        $result['params']['key']->shouldBe('value');
    }

    public function it_should_be_able_to_set_an_error()
    {
        $this->setError('error');

        $result = $this->jsonSerialize();
        $result['isError']->shouldBe(true);
        $result['isSuccess']->shouldBe(false);
        $result['error']->shouldBe('error');
    }

}
