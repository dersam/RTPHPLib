<?php
namespace Dersam\RT;

abstract class Response
{
    protected $rawResponse    = '';
    protected $parsedResponse = null;
    protected $success = false;

    abstract public function parse($code, $response);

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return array
     */
    public function getResponseFields()
    {
        return $this->parsedResponse;
    }
}