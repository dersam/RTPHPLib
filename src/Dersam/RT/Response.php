<?php
namespace Dersam\RT;

abstract class Response
{
    protected $rawResponse    = null;
    protected $code           = null;
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

    public function get($fieldName)
    {
        return isset($this->parsedResponse[$fieldName]) ?
            $this->parsedResponse[$fieldName] : null;
    }
}