<?php
/**
 * RTPHPLib v0.1
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
 * Requires installation of the pecl_http module.
 * http://php.net/manual/en/book.http.php
 *
 * Standard request fields are documented at http://requesttracker.wikia.com/wiki/REST
 * Depending on your request type, this will determine how you create your array of values.
 * See the example script for a demonstration.
 * 
 */
class RequestTracker extends HttpRequest{
    /**
     * The location of the REST api
     * @var string
     */
    private $url;

    /**
     * Username to use
     * @var string
     */
    private $user;

    /**
     * Password to use
     * @var string
     */
    private $pass;

    /**
     * If true, RT will authenticate once and obtain a session token.
     * Otherwise, each request will re-auth with username and password.
     * @var boolean
     */
    private $sessionEnabled;

    /**
     * Create a new instance for API requests
     * @param string $rootUrl
     *          The base URL to your request tracker installation. For example,
     *          if your RT is located at "http://rt.example.com", your rootUrl
     *          would be "http://rt.example.com".  There should be no trailing slash.
     * @param string $user The username to authenticate with.
     * @param string $pass The password to authenticate with.
     * @param boolean $sessionEnabled Whether to enable session-based requests. Defaults to true (faster).
     * @throws AuthenticationException if sessions are enabled and the session token cannot be obtained
     */
    function __construct($rootUrl, $user, $pass, $sessionEnabled=true){
        parent::__construct($rootUrl, HTTP_METH_POST);
        if($sessionEnabled)
            $this->getSessionCookie($user, $pass);
        
        $this->sessionEnabled = $sessionEnabled;
        $this->url = $rootUrl."/REST/1.0/";
        $this->user = $user;
        $this->pass = $pass;
    }

    /**
     * Explicitly logs out of RT to destroy the session when disposing
     * of the object instance.
     */
    function __destruct() {
        $this->logout();
    }

    /**
     * Obtains the session cookie. Called by constructor if sessions are enabled.
     * @param string $user username to use
     * @param string $pass password to use.
     */
    private function getSessionCookie($user, $pass){
        $this->enableCookies();
        $this->addPostFields(array('user'=>$user, 'pass'=>$pass));
        $result = parent::send();
        
        if($result->getResponseCode() != 200){
            throw new AuthenticationException("Couldn't authenticate to Request Tracker :: ".$result->getBody());
        }
        
        $this->addPostFields(array());
    }

    /**
     * Overrides HttpRequest::send().
     * Sends a request to your RT.
     *
     * In general, this function should not be called directly- should only
     * be used if there is custom functionality not covered by the general
     * API functions provided.
     *
     * @return HttpMessage the response from RT
     */
    public function send() {
       if(!$this->sessionEnabled){
            $this->addPostFields(array('user'=>$this->user, 'pass'=>$this->pass));
        }
        
        $response = parent::send();
        $this->setPostFields(array());
        
        return $response;
    }

    /**
     * Create a ticket
     * @param array $content
     * @return HttpResponse
     */
    public function createTicket($content){
        $url = $this->url."ticket/new";
        $this->setUrl($url);
        $this->setPostFields(array('content'=>$this->parseArray($content)));
        $response = $this->send();
        return $response;
    }

    /**
     * Edit ticket
     * @param int $ticketId
     * @param array $content
     * @return HttpMessage
     */
    public function editTicket($ticketId, $content){
        $url = $this->url."ticket/$ticketId/edit";
        $this->setUrl($url);
        $this->setPostFields(array('content'=>$this->parseArray($content)));
        $response = $this->send();
        return $response;
    }
    
    /**
     * Reply to a ticket
     * @param int $ticketId
     * @param array $content
     * @return HttpMessage
     */
    public function doTicketReply($ticketId, $content){
        $content['Action'] = 'correspond';
        $url = $this->url."ticket/$ticketId/comment";
        $this->setUrl($url);
        $this->setPostFields(array('content'=>$this->parseArray($content)));
        $response = $this->send();
        return $response;
    }

    /**
     * Comment on a ticket
     * @param int $ticketId
     * @param array $content
     * @return HttpMessage
     */
    public function doTicketComment($ticketId, $content){
        $content['Action'] = 'comment';
        $url = $this->url."ticket/$ticketId/comment";
        $this->setUrl($url);
        $this->setPostFields(array('content'=>$this->parseArray($content)));
        $response = $this->send();
        return $response;
    }

    /**
     * Get ticket metadata
     * @param int $ticketId
     * @return HttpMessage
     */
    public function getTicketProperties($ticketId){
        $url = $this->url."ticket/$ticketId/show";
        $this->setUrl($url);
        
        $response = $this->send();
        return $response;
    }

    /**
     * Get ticket links
     * @param int $ticketId
     * @return HttpMessage
     */
    public function getTicketLinks($ticketId){
        $url = $this->url."ticket/$ticketId/links/show";
        $this->setUrl($url);
        $response = $this->send();
        return $response;
    }

    /**
     * Modify links on a ticket
     * @param int $ticketId
     * @param array $content
     * @return HttpMessage
     */
    public function editTicketLinks($ticketId, $content){
        $url = $this->url."ticket/$ticketId/links";
        $this->setUrl($url);
        $this->setPostFields(array('content'=>$this->parseArray($content)));
        $response = $this->send();
        return $response;
    }

    /**
     * Get a list of attachments on a ticket
     * @param int $ticketId
     * @return HttpMessage
     */
    public function getTicketAttachments($ticketId){
        $url = $this->url."ticket/$ticketId/attachments";
        $this->setUrl($url);
        
        $response = $this->send();
        return $response;
    }

    /**
     * Get a specific attachment's metadata on a ticket
     * @param int $ticketId
     * @param int $attachmentId
     * @return HttpMessage
     */
    public function getAttachment($ticketId, $attachmentId){
        $url = $this->url."ticket/$ticketId/attachments/$attachmentId";
        $this->setUrl($url);
        
        $response = $this->send();
        return $response;
    }

    /**
     * Get the content of an attachment
     * @param int $ticketId
     * @param int $attachmentId
     * @return HttpMessage
     */
    public function getAttachmentContent($ticketId, $attachmentId){
        $url = $this->url."ticket/$ticketId/attachments/$attachmentId/content";
        $this->setUrl($url);
        
        $response = $this->send();
        return $response;
    }

    /**
     * Get the history of a ticket
     * @param int $ticketId
     * @param boolean $longFormat Whether to return all data of each history node
     * @return HttpMessage
     */
    public function getTicketHistory($ticketId, $longFormat=true){
        if($longFormat)
            $url = $this->url."ticket/$ticketId/history?format=1";
        else
            $url = $this->url."ticket/$ticketId/history";
            
        $this->setUrl($url);
        
        $response = $this->send();
        return $response;
    }

    /**
     * Get the long form data of a specific ticket history node
     * @param int $ticketId
     * @param int $historyId
     * @return HttpMessage
     */
    public function getTicketHistoryNode($ticketId, $historyId){
        $url = $this->url."ticket/$ticketId/history/id/$historyId";
            
        $this->setUrl($url);
        
        $response = $this->send();
        return $response;
    }
    
    /**
     * Search for tickets based on a query
     *
     * Extend the Request Tracker class and implement custom search functions there
     * by passing $query and $orderBy to this function
     * @param type $query
     * @param type $orderBy
     * @param type $format
     * @return HttpMessage
     */
    public function search($query, $orderBy, $format='s'){
        $url = $this->url."/REST/1.0/search/ticket?query=$query&orderby=$orderBy&format=$format";
            
        $this->setUrl($url);
        
        $response = $this->send();
        return $response;
    }
    
    private function parseArray($contentArray){
        $content = "";
        foreach($contentArray as $key=>$value){
            $content .= "$key: $value".PHP_EOL;
        }       
        return $content;
    }
    
    /**
     * Get metadata for a user
     * @param int|string $userId either the user id or the user login
     * @return HttpMessage
     */
    public function getUserProperties($userId){
        $url = $this->url."user/$userId";
            
        $this->setUrl($url);
        
        $response = $this->send();
        return $response;
    }

    /**
     * Get metadata of a queue
     * @param int $queueId
     * @return HttpMessage
     */
    public function getQueueProperties($queueId){
        $url = $this->url."queue/$queueId";
            
        $this->setUrl($url);
        
        $response = $this->send();
        return $response;
    }

    /**
     * Log out of RT and destroy current session
     * @return HttpMessage
     */
    public function logout(){
        $url = $this->url."logout";
            
        $this->setUrl($url);
        
        $response = $this->send();
        return $response;
    }
}

class RequestTrackerException extends Exception {}
class AuthenticationException extends RequestTrackerException {}
?>
