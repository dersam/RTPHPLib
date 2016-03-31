<?php
/**
Alternatively use [composer](http://getcomposer.org/) and just:

require 'vendor/autoload.php'

 */
require_once 'RequestTracker.php';

$url = "http://rt.easter-eggs.org/demos/4.2/";
$user = "admin";
$pass = "admin";

$rt = new RequestTracker($url, $user, $pass);

//Get the properties of an existing ticket, given the ID
/*
$response = $rt->getTicketProperties(158);
print_r($response);*/

//Create a new ticket- see http://requesttracker.wikia.com/wiki/REST#Ticket_Create for all fields
        /*
$content = array(
    'Queue'=>'fast_lane',
    'Requestor'=>'test@example.com',
    'Subject'=>'Lorem Ipsum',
    'Text'=>'dolor sit amet'
);
$response = $rt->createTicket($content);
print_r($response);
     */

//Edit a ticket
       /*
$content = array(
    'Priority'=>3
);
$response = $rt->editTicket(233, $content);
print_r($response);
       */

//Reply to a ticket
  /*
$content = array(
    'Text'=>'This is a test reply.'
);
$response = $rt->doTicketReply(233, $content);
print_r($response);

       */
//Comment on a ticket
/*
$content = array(
    'Text'=>"This is a test comment.\nNew Line"
);
$response = $rt->doTicketComment(168, $content);
print_r($response);
*/

//Ticket Links
         /*
$content = array(
    'DependsOn'=>1
);

$response = $rt->editTicketLinks(233, $content);
print_r($response);    */
/*
$response = $rt->getTicketLinks(233);
print_r($response);  */


//Attachment management
/*
$response = $rt->getTicketAttachments(3);
print_r($response);
$response = $rt->getAttachment(3, 13);
print_r($response);
$response = $rt->getAttachmentContent(3, 13);
print_r($response);
 */

// Adding attachment when replying. (Untested)
/*
// PHP > 5.5
$attachments = [
    new \CURLFile('/tmp/phpK5TNJc', 'text/plain', 'filename=2.txt'),
];
// PHP < 5.5
$attachments = array(
    '@/tmp/phpK5TNJc;type=text/plain;filename=2.txt',
);
$content = array(
    'Text' => 'This is a test reply.',
);
$rt->doTicketReply(233, $content, $attachments);
 */


//Ticket History
        /*
$response = $rt->getTicketHistory(233);
print_r($response);
$response = $rt->getTicketHistoryNode(233,3702);
print_r($response);
      */

//Search demonstration
//Unowned tickets ordered by Created date descending
            /*
$response = $rt->search("Owner='Nobody'",'-Created', 's');
print_r($response);   */
