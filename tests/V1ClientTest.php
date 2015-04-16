<?php
/**
 * RTPHPLib
 * User: Sam
 * Date: 4/15/2015
 */

namespace Dersam\RT;

use Dersam\RT\Clients\V1Client;
use Dersam\RT\Requests\v1\Base;

class V1ClientTest extends \PHPUnit_Framework_TestCase
{
    protected static $host = 'http://rt.easter-eggs.org/demos/4.2';
    protected static $user = 'admin';
    protected static $password = 'admin';
    /** @var  V1Client */
    protected static $client;

    /**
     * This ensures we are testing the client in cases where multiple requests
     * are sent with one client. Since the client is just injecting itself
     * into requests and does not change state, it should not be polluted
     * across requests.
     */
    public static function setUpBeforeClass()
    {
        self::$client = new V1Client(
            self::$host,
            self::$user,
            self::$password
        );
    }

    public function testDoCreateTicket()
    {
        $response = self::$client->doCreateTicket();
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