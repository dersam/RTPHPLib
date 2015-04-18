<?php
/**
 * RTPHPLib
 * User: Sam
 * Date: 4/15/2015
 */
namespace Dersam\RT\Responses;

use Dersam\RT\Response;

abstract class V1Response extends Response
{
    public function parse($code, $response)
    {
        $this->rawResponse = $response;
        $this->code = $code;
        $responseArray = array();
        $response = explode(chr(10), trim($response));
        array_shift($response); //skip RT status response
        array_shift($response); //skip blank line
        $lastkey = null;
        foreach ($response as $line) {
            //RT will always preface a multiline with at least one space
            if (substr($line, 0, 1)==' ') {
                $responseArray[$lastkey] .= "\n".trim($line);
                continue;
            }
            $parts = explode(':', $line);
            $key = array_shift($parts);
            $value = implode(':', $parts);

            // push comments to end of array, ignore blank keys
            if ($key !== '') {
                if (strpos($key, '#')===0) {
                    $responseArray[] = $key;
                } else {
                    $responseArray[$key] = trim($value);
                }

            }
        }

        $this->parsedResponse = $responseArray;

        if ($code == 200) {
            $this->success = true;
            $this->onParseSuccess();
        }
    }

    abstract protected function onParseSuccess();
}