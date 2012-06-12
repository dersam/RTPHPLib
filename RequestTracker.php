<?php
/**
 * RTPHPLib v1.0
 * Copyright (C) 2012 Samuel Schmidt

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do
so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
 *
 * Requires curl
 *
 * Standard request fields are documented at http://requesttracker.wikia.com/wiki/REST
 * Depending on your request type, this will determine how you create your array of values.
 * See the example script for a demonstration.
 * 
 */
class RequestTracker{
    /**
     * The location of the REST api
     * @var string
     */
    protected $url;

    /**
     * The location of the next request
     * @var string
     */
    protected $requestUrl;

    /**
     * Username with which to authenticate
     * @var string
     */
    protected $user;

    /**
     * Password to use
     * @var string
     */
    protected $pass;

    /**
     * Current set of fields to post to RT
     * @var array
     */
    protected $postFields;

    /**
     * Create a new instance for API requests
     * @param string $rootUrl
     *          The base URL to your request tracker installation. For example,
     *          if your RT is located at "http://rt.example.com", your rootUrl
     *          would be "http://rt.example.com".  There should be no trailing slash.
     * @param string $user The username to authenticate with.
     * @param string $pass The password to authenticate with.
     */
    function __construct($rootUrl, $user, $pass){
        $this->url = $rootUrl."/REST/1.0/";
        $this->user = $user;
        $this->pass = $pass;
    }

    /**
     * Sends a request to your RT.
     *
     * In general, this function should not be called directly- should only
     * be used by a subclass if there is custom functionality not covered
     * by the general API functions provided.
     */
    protected function send() {
        if(!empty($this->postFields))
            $fields = array('user'=>$this->user, 'pass'=>$this->pass, 'content'=>$this->parseArray($this->postFields));
        else
            $fields = array('user'=>$this->user, 'pass'=>$this->pass);

        $response = $this->post($fields);
        
        return $response;
    }

    /**
     * Create a ticket
     * @param array $content the ticket fields as fieldname=>fieldvalue array
     * @return array key=>value response pair array
     */
    public function createTicket($content){
        $content['id'] = 'ticket/new';
        $url = $this->url."ticket/new";
        $this->setRequestUrl($url);
        $this->setPostFields($content);
        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Edit ticket
     * @param int $ticketId
     * @param array $content the ticket fields as fieldname=>fieldvalue array
     * @return array key=>value response pair array
     */
    public function editTicket($ticketId, $content){
        $url = $this->url."ticket/$ticketId/edit";
        $this->setRequestUrl($url);
        $this->setPostFields($content);
        $response = $this->send();
        return $this->parseResponse($response);
    }
    
    /**
     * Reply to a ticket
     * @param int $ticketId
     * @param array $content the ticket fields as fieldname=>fieldvalue array
     * @return array key=>value response pair array
     */
    public function doTicketReply($ticketId, $content){
        $content['Action'] = 'correspond';
        $content['id'] = $ticketId;
        $url = $this->url."ticket/$ticketId/comment";
        $this->setRequestUrl($url);
        $this->setPostFields($content);
        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Comment on a ticket
     * @param int $ticketId
     * @param array $content
     * @return array key=>value response pair array
     */
    public function doTicketComment($ticketId, $content){
        $content['Action'] = 'comment';
        $url = $this->url."ticket/$ticketId/comment";
        $this->setRequestUrl($url);
        $this->setPostFields($content);
        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Get ticket metadata
     * @param int $ticketId
     */
    public function getTicketProperties($ticketId){
        $url = $this->url."ticket/$ticketId/show";
        $this->setRequestUrl($url);
        
        $response = $this->send();
        $response = $this->parseResponse($response);
        return $this->parseResponse($response);
    }

    /**
     * Get ticket links
     * @param int $ticketId
     * @return array key=>value response pair array
     */
    public function getTicketLinks($ticketId){
        $url = $this->url."ticket/$ticketId/links/show";
        $this->setRequestUrl($url);
        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Modify links on a ticket
     * @param int $ticketId
     * @param array $content
     * @return array key=>value response pair array
     */
    public function editTicketLinks($ticketId, $content){
        $url = $this->url."ticket/$ticketId/links";
        $this->setRequestUrl($url);
        $this->setPostFields($content);
        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Get a list of attachments on a ticket
     * @param int $ticketId
     * @return array key=>value response pair array
     */
    public function getTicketAttachments($ticketId){
        $url = $this->url."ticket/$ticketId/attachments";
        $this->setRequestUrl($url);
        
        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Get a specific attachment's metadata on a ticket
     * @param int $ticketId
     * @param int $attachmentId
     * @return array key=>value response pair array
     */
    public function getAttachment($ticketId, $attachmentId){
        $url = $this->url."ticket/$ticketId/attachments/$attachmentId";
        $this->setRequestUrl($url);
        
        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Get the content of an attachment
     * @param int $ticketId
     * @param int $attachmentId
     * @return array key=>value response pair array
     */
    public function getAttachmentContent($ticketId, $attachmentId){
        $url = $this->url."ticket/$ticketId/attachments/$attachmentId/content";
        $this->setRequestUrl($url);
        
        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Get the history of a ticket
     * @param int $ticketId
     * @param boolean $longFormat Whether to return all data of each history node
     * @return array key=>value response pair array
     */
    public function getTicketHistory($ticketId, $longFormat=true){
        if($longFormat)
            $url = $this->url."ticket/$ticketId/history?format=1";
        else
            $url = $this->url."ticket/$ticketId/history";
            
        $this->setRequestUrl($url);
        
        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Get the long form data of a specific ticket history node
     * @param int $ticketId
     * @param int $historyId
     * @return array key=>value response pair array
     */
    public function getTicketHistoryNode($ticketId, $historyId){
        $url = $this->url."ticket/$ticketId/history/id/$historyId";
            
        $this->setRequestUrl($url);
        
        $response = $this->send();
        return $this->parseResponse($response);
    }
    
    /**
     * Search for tickets based on a query
     *
     * Extend the Request Tracker class and implement custom search functions there
     * by passing $query and $orderBy to this function
     * @param string $query the query to run
     * @param string $orderBy how to order the query
     * @param string $format the format type (i,s,l)
     *
     * @return array
     *      's' = ticket-id=>ticket-subject
     *      'i' = not implemented
     *      'l' = not implemented
     */
    public function search($query, $orderBy, $format='s'){
        $url = $this->url."search/ticket?query=$query&orderby=$orderBy&format=$format";
            
        $this->setRequestUrl($url);
        
        $response = $this->send();

        $responseArray = array();

        if($format='s'){
            $responseArray = $this->parseResponse($response);
        }
        else if($format='i'){
            return $response['body'];
        }
        else if($format='l'){
            return $response['body'];
        }

        return $responseArray;
    }

    private function parseResponse($response, $delimiter=':'){
        $responseArray = array();
        $response = explode(chr(10), $response['body']);
        array_shift($response);
        array_shift($response);
        foreach($response as $line){
            $parts = explode($delimiter, $line);
            $key = array_shift($parts);
            $value = implode($delimiter, $parts);
            $responseArray[$key] = $value;
        }

        return $responseArray;
    }
    
    private function parseArray($contentArray){
        $content = "";
        foreach($contentArray as $key=>$value){
            $content .= "$key: $value".chr(10);
        }       
        return $content;
    }
    
    /**
     * Get metadata for a user
     * @param int|string $userId either the user id or the user login
     * @return array key=>value response pair array
     */
    public function getUserProperties($userId){
        $url = $this->url."user/$userId";
            
        $this->setRequestUrl($url);
        
        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Get metadata of a queue
     * @param int $queueId
     * @return array key=>value response pair array
     */
    public function getQueueProperties($queueId){
        $url = $this->url."queue/$queueId";
            
        $this->setRequestUrl($url);
        
        $response = $this->send();
        return $this->parseResponse($response);
    }

    private function setRequestUrl($url){
        $this->requestUrl = $url;
    }

    private function setPostFields($data){
        $this->postFields = $data;
    }

    private function post($data, $contentType=null){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->requestUrl);
        curl_setopt($ch, CURLOPT_POST, 1);

        if(!empty($contentType)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: $contentType"));
        }
        array_unshift($data, "");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($response === false){
            throw new RequestTrackerException("A fatal unexpected error occurred when communicating with RT.");
        }

        if($code == 401){
            throw new AuthenticationException("The user credentials were refused.");
        }

        if($code != 200){
            throw new HttpException("An error occurred : [$code] :: $response");
        }

        $response =  array('code'=>$code, 'body'=>$response);
        return $response;
    }
}

class RequestTrackerException extends Exception {}
class AuthenticationException extends RequestTrackerException {}
class HttpException extends RequestTrackerException {}
?>
