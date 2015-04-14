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

    public function testGetTicketProperties(){
        $ticketId = $this->client->createTicket(array(
            'Queue'=>'General',
            'Subject'=>'TestGetTicketProperties',
            'Text'=>'This is a test ticket.'
        ));

        $this->assertTrue(is_numeric($ticketId));

        $response = $this->client->getTicketProperties($ticketId);

        $this->assertTrue(is_array($response));

        foreach($response as $key=>$value){
            //Make sure all blank fields are trimmed
            $this->assertTrue($key!='');
        }
    }

    public function testFailedGetTicketProperties(){
        $response = $this->client->getTicketProperties(null);

        $this->assertFalse($response);
    }

    public function testGetTicketLinks(){
        $ticketId = $this->client->createTicket(array(
            'Queue'=>'General',
            'Subject'=>'TestGetTicketLinks',
            'Text'=>'This is a test ticket.'
        ));

        $this->assertTrue(is_numeric($ticketId));

        $response = $this->client->getTicketLinks($ticketId);
        $this->assertTrue(is_array($response));
        $this->assertTrue(isset($response['id']));
    }

    public function testFailedGetTicketLinks(){
        $response = $this->client->getTicketLinks(null);
        $this->assertFalse(is_array($response));
    }

    public function testGetTicketHistory(){

    }

    public function testFailedGetTicketHistory(){

    }

    public function testGetTicketHistoryNode(){

    }

    public function testFailedGetTicketHistoryNode(){

    }

    public function testSearchS(){

    }

    public function testSearchI(){

    }

    public function testSearchL(){

    }

    public function testGetUserProperties(){
        $user = $this->client->getUserProperties(1);
        $this->assertTrue(is_array($user));
        $this->assertTrue(isset($user['id']));
        $this->assertTrue(isset($user['Password']));
        $this->assertTrue(isset($user['Name']));
        $this->assertTrue(isset($user['RealName']));
        $this->assertTrue(isset($user['Disabled']));
    }

    public function testFailedGetUserProperties(){
        $user = $this->client->getUserProperties(null);
        $this->assertFalse($user);
    }

    public function testGetQueueProperties(){
        $queue = $this->client->getQueueProperties(1);

        $this->assertTrue(is_array($queue));
        $this->assertTrue(isset($queue['id']));
        $this->assertTrue(isset($queue['Name']));
        $this->assertTrue(isset($queue['Description']));
        $this->assertTrue(isset($queue['CorrespondAddress']));
        $this->assertTrue(isset($queue['CommentAddress']));
        $this->assertTrue(isset($queue['InitialPriority']));
        $this->assertTrue(isset($queue['FinalPriority']));
        $this->assertTrue(isset($queue['DefaultDueIn']));
        $this->assertTrue(isset($queue['Disabled']));
    }

    public function testFailedGetQueueProperties(){
        $queue = $this->client->getQueueProperties(null);
        $this->assertFalse($queue);
    }

    public function testGetLastError(){
        $this->assertTrue($this->client->getLastError()==null);
    }

    public function testSetVerifySslCertificates(){
        $this->client->setVerifySslCertificates(false);

        $this->assertFalse($this->client->getVerifySslCertificates());
    }

    public function testGetVerifySslCertificates(){
        $this->client->setVerifySslCertificates(false);

        $this->assertFalse($this->client->getVerifySslCertificates());
    }
}