<?php

namespace Phpro\Apigility\Doctrine\Bulk\Controller;
use Phpro\Apigility\Doctrine\Bulk\Service\BulkService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use ZF\ApiProblem\ApiProblem;

/**
 * Class BulkController
 *
 * @package Phpro\Apigility\Doctrine\Bulk\Controller
 */
class BulkController
    extends AbstractActionController
{

    /**
     * @var BulkService
     */
    protected $bulkService;

    /**
     * @param $bulkService
     */
    public function __construct($bulkService)
    {
        $this->bulkService = $bulkService;
    }

    /**
     * @throws ApiProblem
     */
    public function bulkAction()
    {
        $data = $this->bodyParams();
        if (!is_array($data)) {
            throw new ApiProblem(500, 'Invalid body');
        }

        $result = $this->bulkService->bulk($data);
        $response = new JsonModel($result);
        return $response;
    }
}
