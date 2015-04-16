<?php
/**
 * RTPHPLib
 * User: Sam
 * Date: 4/15/2015
 */
namespace Dersam\RT\Clients;

use Dersam\RT\Client;
use Dersam\RT\Responses\v1\Base;

class V1Client extends Client
{
    /**
     * The default API response from the base url
     * @return \Dersam\RT\Responses\v1\Base
     */
    public function base()
    {
        $request = new \Dersam\RT\Requests\v1\Base();
    }
}