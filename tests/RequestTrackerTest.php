<?php

/**
 *
 *
 * @author Sam Schmidt <samuel@dersam.net>
 * @since 2016-02-01
 */
class RequestTrackerTest extends PHPUnit_Framework_TestCase
{
    public function getRequestTracker()
    {
        return new RequestTracker(
            'http://192.168.99.100:8080/',
            'root',
            'password'
        );
    }

    public function testCreateTicket()
    {
        $rt = $this->getRequestTracker();
        $content = array(
            'Queue'=>'General',
            'Requestor'=>'root@localhost.com',
            'Subject'=>'Lorem Ipsum',
            'Text'=>'dolor sit amet'
        );
        $response = $rt->createTicket($content);

        $this->assertRegExp('/^# Ticket\b \d+\b created\.$/', key($response));
    }
}
