<?php
/**
 * RTPHPLib
 * User: Sam
 * Date: 4/15/2015
 */
namespace Dersam\RT\Clients;

use Dersam\RT\Client;
use Dersam\RT\Requests\v1\CreateTicket;
use Dersam\RT\Responses\v1\Base;

class V1Client extends Client
{
    public function __construct($baseUrl, $user, $pass)
    {
        parent::__construct($baseUrl, $user, $pass);
        $this->url .= "/REST/1.0";
    }

    public function doCreateTicket()
    {
        $request = new CreateTicket();
        $response = $this->send($request);
        print_r($response);
        return $response;
    }

    public function doEditTicket()
    {

    }

    public function doReplyTicket()
    {

    }

    public function doCommentTicket()
    {

    }

    public function getTicketProperties()
    {

    }

    public function getTicketLinks()
    {

    }

    public function doEditTicketLinks()
    {

    }

    public function getTicketAttachments()
    {

    }

    public function getAttachmentMetadata()
    {

    }

    public function getAttachmentContent()
    {

    }

    public function getTicketHistory()
    {

    }

    public function getTicketHistoryNode()
    {

    }

    public function searchI()
    {

    }

    public function searchS()
    {

    }

    public function searchL()
    {

    }

    public function getUserProperties()
    {

    }

    public function getQueueProperties()
    {

    }
}