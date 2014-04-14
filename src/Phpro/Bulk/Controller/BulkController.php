<?php

namespace Phpro\Apigility\Doctrine\Bulk\Controller;

/**
 * Class BulkController
 *
 * @package Phpro\Apigility\Doctrine\Bulk\Controller
 */
class BulkController
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


    public function bulkAction()
    {

    }
} 