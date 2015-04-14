<?php

class RequestTrackerTest extends PHPUnit_Framework_TestCase{
    protected $host = 'http://rt.easter-eggs.org/demos/4.2/';
    protected $user = 'admin';
    protected $password = 'admin';
    /** @var  RequestTracker */
    protected $client;

    public function setUp(){
       $this->client = new RequestTracker(
           $this->host,
           $this->user,
           $this->password
       );
    }

    public function tearDown(){
        $this->client = null;
    }

    public function testCreateTicket(){
        $ticketId = $this->client->createTicket(array(
            'Queue'=>'General',
            'Subject'=>'TestCreateTicket',
            'Text'=>'This is a test ticket.'
        ));

        $this->assertTrue(is_numeric($ticketId));
    }

    public function testFailedCreateTicket(){
        $failure = $this->client->createTicket(array(
            'Subject'=>'TestFailedCreateTicket',
            'Text'=>'This is a test ticket.'
        ));

        $this->assertFalse($failure);
        $this->assertTrue(is_array($this->client->getLastError()));
    }

    public function testEditTicket(){
        $ticketId = $this->client->createTicket(array(
            'Queue'=>'General',
            'Subject'=>'TestEditTicket',
            'Text'=>'This is a test ticket.'
        ));

        $this->assertTrue(is_numeric($ticketId));

        $response = $this->client->editTicket($ticketId, array(
            'Priority'=>3
        ));

        $this->assertTrue($response);
    }

    public function testFailedEditTicket(){
        $ticketId = $this->client->createTicket(array(
            'Queue'=>'General',
            'Subject'=>'TestFailedEditTicket',
            'Text'=>'This is a test ticket.'
        ));

        $this->assertTrue(is_numeric($ticketId));

        $response = $this->client->editTicket($ticketId, array(
            'Text'=>3
        ));

        $this->assertFalse($response);
    }

    public function testDoTicketReply(){
        $ticketId = $this->client->createTicket(array(
            'Queue'=>'General',
            'Subject'=>'TestDoTicketReply',
            'Text'=>'This is a test ticket.'
        ));

        $this->assertTrue(is_numeric($ticketId));

        $response = $this->client->doTicketReply($ticketId,array(
            'Text'=>'This is a ticket reply'
        ));

        $this->assertTrue($response);
    }

    public function testDoFailedTicketReply(){
        $ticketId = $this->client->createTicket(array(
            'Queue'=>'General',
            'Subject'=>'TestDoFailedTicketReply',
            'Text'=>'This is a test ticket.'
        ));

        $this->assertTrue(is_numeric($ticketId));

        $response = $this->client->doTicketReply($ticketId,array(
            'FakeField'=>'This is meaningless'
        ));

        $this->assertFalse($response);
    }

    public function testDoTicketComment(){
        $ticketId = $this->client->createTicket(array(
            'Queue'=>'General',
            'Subject'=>'TestDoTicketComment',
            'Text'=>'This is a test ticket.'
        ));

        $this->assertTrue(is_numeric($ticketId));

        $response = $this->client->doTicketComment($ticketId,array(
            'Text'=>'This is a ticket reply'
        ));

        $this->assertTrue($response);
    }

    public function testDoFailedTicketComment(){
        $ticketId = $this->client->createTicket(array(
            'Queue'=>'General',
            'Subject'=>'TestDoFailedTicketComment',
            'Text'=>'This is a test ticket.'
        ));

        $this->assertTrue(is_numeric($ticketId));

        $response = $this->client->doTicketComment($ticketId,array(
            'FakeField'=>'This is meaningless'
        ));

        $this->assertFalse($response);
    }
}