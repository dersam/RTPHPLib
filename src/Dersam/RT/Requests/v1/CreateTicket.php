<?php
/**
 * RTPHPLib
 * User: Sam
 * Date: 4/15/2015
 */
namespace Dersam\RT\Requests\v1;

use Dersam\RT\Client;
use Dersam\RT\Requests\V1Request;
use Dersam\RT\Response;

class CreateTicket extends V1Request
{
    public function send(Client $client)
    {
        $this->setRequestUri('/ticket/new');

        $this->setField('id', 'ticket/new');

        return parent::send($client);
    }

    /**
     * @return Response
     */
    public function makeResponseInstance()
    {
        return new \Dersam\RT\Responses\v1\CreateTicket();
    }
}