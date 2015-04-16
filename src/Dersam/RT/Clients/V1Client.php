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
    public function getBaseRequest()
    {
        $request = new \Dersam\RT\Requests\v1\Base();
    }

    public function doCreateTicket()
    {

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