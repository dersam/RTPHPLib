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
            'Subject'=>'TestFailCreateTicket',
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

        $response = $this->client->editTicket($ticketId, array(
            'Text'=>3
        ));

        $this->assertFalse($response);
    }
}