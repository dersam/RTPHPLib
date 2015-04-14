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
        $response = $this->client->createTicket(array(
            'Queue'=>'General',
            'Text'=>'This is a test ticket.'
        ));
        print_r($response);
    }
}