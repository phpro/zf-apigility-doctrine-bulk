<?php

namespace Phpro\Apigility\Doctrine\Bulk\Model;
use Zend\Stdlib\JsonSerializable;

/**
 * Class Result
 *
 * @package Phpro\Apigility\Doctrine\Bulk\Model
 */
class Result
    implements JsonSerializable
{

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var string
     */
    protected $error = null;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @param $command
     * @param $id
     */
    public function __construct($command, $id)
    {
        $this->command = $command;
        $this->id = $id;
    }

    /**
     * @param $params
     */
    public function addParams($params)
    {
        foreach ($params as $key => $value) {
            $this->params[$key] = $value;
        }
    }

    /**
     * @param $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return [
            'command' => $this->command,
            'id' => $this->id,
            'params' => $this->params,
            'isSuccess' => !$this->error,
            'isError' => !!$this->error,
            'error' => $this->error,
        ];
    }

}
