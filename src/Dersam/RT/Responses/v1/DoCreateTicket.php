<?php
/**
 * RTPHPLib
 * User: Sam
 * Date: 4/15/2015
 */
namespace Dersam\RT\Responses\v1;

use Dersam\RT\Responses\V1Response;

class DoCreateTicket extends V1Response
{
    public function parse($code, $response)
    {
        parent::parse($code, $response);

        if (isset($this->parsedResponse[0])) {
            preg_match(
                '/Ticket (\d+) created/i',
                $this->parsedResponse[0],
                $matches
            );

            if (!empty($matches)) {
                if (isset($matches[1])) {
                    $this->success = true;
                    $this->parsedResponse = array('id'=>$matches[1]);
                }
            }
        }
    }

}