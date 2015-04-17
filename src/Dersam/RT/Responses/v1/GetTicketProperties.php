<?php
/**
 * RTPHPLib
 * User: Sam
 * Date: 4/16/2015
 */
namespace Dersam\RT\Responses\v1;

use Dersam\RT\Responses\V1Response;

class GetTicketProperties extends V1Response
{
    public function parse($code, $response)
    {
        parent::parse($code, $response);

        if ($code == 200) {
            $this->success = true;
        }
    }
}