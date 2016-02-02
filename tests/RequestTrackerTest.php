<?php

/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-02-01
 */
class RequestTrackerTest extends PHPUnit_Framework_TestCase
{
    private function getHost()
    {
        $host = getenv('RT_ENDPOINT');

        if (!empty($host)) {
            return "http://$host:8080";
        }

        return 'http://localhost:8080';
    }

    public function getRequestTracker()
    {
        $host = $this->getHost();

        return new RequestTracker(
            "$host/",
            'root',
            'password'
        );
    }

    public function testCreateTicket()
    {
        $rt = $this->getRequestTracker();
        $content = array(
            'Queue'=>'General',
            'Requestor'=>'test@example.com',
            'Subject'=>'Lorem Ipsum',
            'Text'=>'dolor sit amet'
        );
        $response = $rt->createTicket($content);

        $this->assertRegExp('/^# Ticket\b \d+\b created\.$/', key($response));
    }

    private function getTicketIdFromCreateResponse($resp)
    {
        $matches = array();
        preg_match(
            '/^# Ticket\b (\d+)\b created\.$/',
            key($resp),
            $matches
        );

        return array_pop($matches);
    }

    public function testEditTicket()
    {
        $rt = $this->getRequestTracker();
        $content = array(
            'Queue'=>'General',
            'Requestor'=>'test@example.com',
            'Subject'=>'Lorem Ipsum',
            'Text'=>'dolor sit amet',
            'Priority'=>1
        );

        $response = $rt->createTicket($content);
        $ticketId = $this->getTicketIdFromCreateResponse($response);

        $response = $rt->editTicket($ticketId, array('Priority'=>22));
        $this->assertRegExp('/^# Ticket\b \d+\b updated\.$/', key($response));

        $response = $rt->getTicketProperties($ticketId);
        $this->assertEquals(22, $response['Priority']);
    }

    public function testTicketReply()
    {
        $rt = $this->getRequestTracker();
        $content = array(
            'Queue'=>'General',
            'Requestor'=>'test@example.com',
            'Subject'=>'Lorem Ipsum',
            'Text'=>'dolor sit amet',
            'Priority'=>1
        );

        $response = $rt->createTicket($content);
        $ticketId = $this->getTicketIdFromCreateResponse($response);

        $response = $rt->doTicketReply($ticketId, array(
            'Text'=>'This is a test reply.'
        ));

        $this->assertEquals('# Correspondence added', key($response));

        $history = $rt->getTicketHistory($ticketId);

        $node = $history[2];
        $this->assertEquals($ticketId, $node['Ticket']);
        $this->assertEquals('This is a test reply.', $node['Content']);
        $this->assertEquals('Correspond', $node['Type']);

        $node = $rt->getTicketHistoryNode($ticketId, $node['id']);
        $this->assertEquals($ticketId, $node['Ticket']);
        $this->assertEquals('This is a test reply.', $node['Content']);
        $this->assertEquals('Correspond', $node['Type']);
    }

    public function testTicketComment()
    {
        $rt = $this->getRequestTracker();
        $content = array(
            'Queue'=>'General',
            'Requestor'=>'test@example.com',
            'Subject'=>'Lorem Ipsum',
            'Text'=>'dolor sit amet',
            'Priority'=>1
        );

        $response = $rt->createTicket($content);
        $ticketId = $this->getTicketIdFromCreateResponse($response);

        $response = $rt->doTicketComment($ticketId, array(
            'Text'=>'This is a test comment.\nNew Line'
        ));

        $this->assertEquals('# Comments added', key($response));

        $history = $rt->getTicketHistory($ticketId);

        $node = $history[2];
        $this->assertEquals($ticketId, $node['Ticket']);
        $this->assertEquals('This is a test comment.\nNew Line', $node['Content']);
        $this->assertEquals('Comment', $node['Type']);
    }
}
