<?php
/**
 * RTPHPLib
 * User: Sam
 * Date: 4/15/2015
 */

namespace Dersam\RT;

use Dersam\RT\Clients\V1Client;

class V1ClientTest extends \PHPUnit_Framework_TestCase
{
    protected $host = 'http://rt.easter-eggs.org/demos/4.2/';
    protected $user = 'admin';
    protected $password = 'admin';
    /** @var  V1Client */
    protected $client;

    /**
     * This ensures we are testing the client in cases where multiple requests
     * are sent with one client. Since the client is just injecting itself
     * into requests and does not change state, it should not be polluted
     * across requests.
     */
    public function setUpBeforeClass()
    {
        $this->client = new V1Client(
            $this->host,
            $this->user,
            $this->password
        );
    }

    public function testGetBaseRequest()
    {

    }

    public function testDoCreateTicket()
    {

    }

    public function testDoEditTicket()
    {

    }

    public function testDoReplyTicket()
    {

    }

    public function testDoCommentTicket()
    {

    }

    public function testGetTicketProperties()
    {

    }

    public function testGetTicketLinks()
    {

    }

    public function testDoEditTicketLinks()
    {

    }

    public function testGetTicketAttachments()
    {

    }

    public function testGetAttachmentMetadata()
    {

    }

    public function testGetAttachmentContent()
    {

    }

    public function testGetTicketHistory()
    {

    }

    public function testGetTicketHistoryNode()
    {

    }

    public function testSearchI()
    {

    }

    public function testSearchS()
    {

    }

    public function testSearchL()
    {

    }

    public function testGetUserProperties()
    {

    }

    public function testGetQueueProperties()
    {

    }
}