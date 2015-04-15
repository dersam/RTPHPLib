<?php
namespace Dersam\RT;


abstract class Response {
    protected $rawResponse    = '';
    protected $parsedResponse = null;

    abstract public function parse($code, $response);
}