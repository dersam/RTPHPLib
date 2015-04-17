<?php
/**
 * RTPHPLib
 * User: Sam
 * Date: 4/16/2015
 */
namespace Dersam\RT\Requests\v1;

use Dersam\RT\Client;
use Dersam\RT\Requests\V1Request;
use Dersam\RT\Response;

class GetTicketProperties extends V1Request
{
    public function send(Client $client)
    {
        $this->setRequestUri('/ticket/'.$this->getField('id').'/show');

        return parent::send($client);
    }

    /**
     * @return \Dersam\RT\Responses\v1\GetTicketProperties
     */
    public function makeResponseInstance()
    {
        return new \Dersam\RT\Responses\v1\GetTicketProperties();
    }
}