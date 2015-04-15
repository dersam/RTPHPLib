<?php
namespace Dersam\RT;


abstract class Response {
    protected $rawResponse    = '';
    protected $parsedResponse = null;

    public function parse($response){

    }
}