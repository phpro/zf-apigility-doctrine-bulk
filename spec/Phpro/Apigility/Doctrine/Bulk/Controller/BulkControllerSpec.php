<?php

namespace spec\Phpro\Apigility\Doctrine\Bulk\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BulkControllerSpec extends ObjectBehavior
{

    /**
     * @param \Phpro\Apigility\Doctrine\Bulk\Service\BulkService $bulkService
     * @param \Zend\Mvc\Controller\PluginManager $pluginManager
     */
    public function let($bulkService, $pluginManager)
    {
        $this->beConstructedWith($bulkService);

        $this->setPluginManager($pluginManager);
        $pluginManager->setController($this)->willReturn(null);
        $pluginManager->get('bodyParams', Argument::any())->willReturn([
            ['create' => ['name' => 'test']],
        ]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Phpro\Apigility\Doctrine\Bulk\Controller\BulkController');
    }

    public function it_is_a_controller()
    {
        $this->shouldHaveType('Zend\Mvc\Controller\AbstractActionController');
    }

    /**
     * @param \Phpro\Apigility\Doctrine\Bulk\Service\BulkService $bulkService
     * @param \Phpro\Apigility\Doctrine\Bulk\Model\Result $result;
     */
    public function it_should_handle_bulk_actions($bulkService, $result)
    {
        $bulkService->bulk(Argument::any())->willReturn([$result]);

        $response = $this->bulkAction();
        $response->shouldBeAnInstanceOf('Zend\View\Model\JsonModel');
        $response->getVariable(0)->shouldBe($result);
    }

}
