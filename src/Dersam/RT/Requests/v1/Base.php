<?php
/**
 * RTPHPLib
 * User: Sam
 * Date: 4/15/2015
 */
namespace Dersam\RT\Requests\v1;

use Dersam\RT\Requests\V1Request;
use Dersam\RT\Response;

class Base extends V1Request
{
    /**
     * @return Response
     */
    public function makeResponseInstance()
    {
        return new \Dersam\RT\Responses\v1\Base();
    }
}