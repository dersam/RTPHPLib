<?php
/**
 * RTPHPLib v1.2.0
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
class RequestTracker
{
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
     * If false, will disable verification of SSL certificates.
     * This is not recommended for production use.  If SSL is not
     * working and the RT host's cert is valid, you should verify that
     * your curl installation has a CA cert bundle installed.
     * @var bool
     */
    protected $enableSslVerification = true;

    /**
     * Create a new instance for API requests
     * @param string $rootUrl
     *          The base URL to your request tracker installation. For example,
     *          if your RT is located at "http://rt.example.com", your rootUrl
     *          would be "http://rt.example.com".  There should be no trailing slash.
     * @param string $user The username to authenticate with.
     * @param string $pass The password to authenticate with.
     */
    public function __construct($rootUrl, $user, $pass)
    {
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
     *
     * @param boolean $doNotUseContentField - the normal behavior of this
     * function is to take the postFields and push them into a single
     * content field for the POST.  If this is set to true, the postFields
     * will be used as the fields for the form instead of getting pushed
     * into the content field.
     *
     * @param object[] $attachments Attachments array to add to ticket keyed by
     * filenames. The array values should be CURLFile objects for PHP > 5.5 or
     * an array of strings containing the file info prepended with "@" for
     * PHP < 5.5 eg:
     * @/tmp/phpK5TNJc
     * or
     * @/tmp/phpK5TNJc;type=text/plain;filename=2.txt
     *
     * From original Request Tracker API, to add attachment to ticket while doing a comment
     * we must add another attachment_1 param with raw file
     * http://requesttracker.wikia.com/wiki/REST#Ticket_History_Comment
     * After testings, one right way is to create CurlObject with file and to put into attachment_1
     * because normal POST field fails
     * More info: http://php.net/manual/en/class.curlfile.php
     */
    protected function send($doNotUseContentField = false, $attachments = false)
    {
        if (!empty($this->postFields) && $doNotUseContentField == true) {
            $fields = $this->postFields;
            $fields['user'] = $this->user;
            $fields['pass'] = $this->pass;
        } elseif (!empty($this->postFields)) {
            $fields = array('user'=>$this->user, 'pass'=>$this->pass, 'content'=>$this->parseArray($this->postFields));
        } else {
            $fields = array('user'=>$this->user, 'pass'=>$this->pass);
        }

        // If we've received attachment param, we have to add to POST params apart from 'content' and send Content-Type
        if (!empty($attachments)) {
            $i = 1;
            foreach ($attachments as $key => $attachment) {
                $fields['attachment_'.$i++] = $attachment;
            }
        }
        $response = $this->post($fields);
        $this->setPostFields('');

        return $response;
    }

    /**
     * Create a ticket
     * @param array $content the ticket fields as fieldname=>fieldvalue array
     * @return array key=>value response pair array
     */
    public function createTicket($content, array $attachments = array())
    {
        $content['id'] = 'ticket/new';
        $url = $this->url."ticket/new";
        if (isset($content['Text'])) {
            $content['Text'] = str_replace("\n", "\n ", $content['Text']);
        }
        $this->setRequestUrl($url);
        $this->setPostFields($content);
        if (!empty($attachments)) {
            $content['Attachment'] = implode("\n ", array_keys($attachments));
            $this->setPostFields($content);
            $response = $this->send(false, $attachments);
        } else {
            $this->setPostFields($content);
            $response = $this->send();
        }
        return $this->parseResponse($response);
    }

    /**
     * Edit ticket
     * @param int $ticketId
     * @param array $content the ticket fields as fieldname=>fieldvalue array
     * @return array key=>value response pair array
     */
    public function editTicket($ticketId, $content)
    {
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
     * @param array $attachments ticket attachments array keyed by filename.
     * For the array value see self::send() documentation.
     * @return array key=>value response pair array
     */
    public function doTicketReply($ticketId, $content, array $attachments = array())
    {
        $content['Action'] = 'correspond';
        $content['id'] = $ticketId;
        $url = $this->url."ticket/$ticketId/comment";
        if (isset($content['Text'])) {
            $content['Text'] = str_replace("\n", "\n ", $content['Text']);
        }
        $this->setRequestUrl($url);
        if (!empty($attachments)) {
            $content['Attachment'] = implode("\n ", array_keys($attachments));
            $this->setPostFields($content);
            $response = $this->send(false, $attachments);
        } else {
            $this->setPostFields($content);
            $response = $this->send();
        }

        return $this->parseResponse($response);
    }

    /**
     * Merge a ticket into another
     * @param int $ticketId
     * @param int $intoId
     * @return array key=>value response pair array
     */
    public function doTicketMerge($ticketId, $intoId)
    {
        $url = $this->url."ticket/$ticketId/merge/$intoId";
        $this->setRequestUrl($url);

        $response = $this->send();

        return $this->parseResponse($response);
    }

    /**
     * Comment on a ticket
     * @param int $ticketId
     * @param array $content
     * @param array $attachments ticket attachments array keyed by filename.
     * For the array value see self::send() documentation.
     * @return array key=>value response pair array
     */
    public function doTicketComment($ticketId, $content, array $attachments = array())
    {
        $content['Action'] = 'comment';
        $url = $this->url."ticket/$ticketId/comment";

        if (isset($content['Text'])) {
            $content['Text'] = str_replace("\n", "\n ", $content['Text']);
        }

        $this->setRequestUrl($url);
        if (!empty($attachments)) {
            $content['Attachment'] = implode("\n ", array_keys($attachments));
            $this->setPostFields($content);
            $response = $this->send(false, $attachments);
        } else {
            $this->setPostFields($content);
            $response = $this->send();
        }

        return $this->parseResponse($response);
    }

    /**
     * Get ticket metadata
     * @param int $ticketId
     */
    public function getTicketProperties($ticketId)
    {
        $url = $this->url."ticket/$ticketId/show";
        $this->setRequestUrl($url);

        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Get ticket links
     * @param int $ticketId
     * @return array key=>value response pair array
     */
    public function getTicketLinks($ticketId)
    {
        $url = $this->url."ticket/$ticketId/links/show";
        $this->setRequestUrl($url);
        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Add link from one ticket to another without worrying about existing links
     * @param int $ticket1
     * @param string $relationship - RefersTo, ReferredToBy, MemberOf, HasMember, DependsOn, DependedOnBy
     * @param int $ticket2
     * @return array key=>value response pair array
     */
    public function addTicketLink($ticket1, $relationship, $ticket2)
    {

        /* Note that this URL does not contain a ticket number. */
        $url = $this->url."ticket/link";
        $this->setRequestUrl($url);
        $content = array(
            'id'   => $ticket1,
            'rel'  => $relationship,
            'to'   => $ticket2
        );
        $this->setPostFields($content);

        /* Use $doNotUseContentField = true for the send($doNotUseContentField)
        function so that the fields won't get pushed into the content field. */
        $response = $this->send(true);
        return $this->parseResponse($response);
    }

    /**
     * Modify links on a ticket
     * @param int $ticketId
     * @param array $content
     * @return array key=>value response pair array
     */
    public function editTicketLinks($ticketId, $content)
    {
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
    public function getTicketAttachments($ticketId)
    {
        $url = $this->url."ticket/$ticketId/attachments";
        $this->setRequestUrl($url);

        $response = $this->send();
        $response = $this->parseResponse($response);
        if (!empty($response['Attachments'])) {
            // Turn Attachments to an arrray keyed by attachment id:
            $attachments = explode(chr(10), $response['Attachments']);
            $response = $this->parseResponseBody($attachments);
        }

        return $response;
    }

    /**
     * Get a specific attachment's metadata on a ticket
     * @param int $ticketId
     * @param int $attachmentId
     * @return array key=>value response pair array
     */
    public function getAttachment($ticketId, $attachmentId)
    {
        $url = $this->url."ticket/$ticketId/attachments/$attachmentId";
        $this->setRequestUrl($url);

        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Get the content of an attachment
     * @param int $ticketId
     * @param int $attachmentId
     * @param bool $raw @todo: Default this to true? I dont see a usecase for false tbh.
     * @return array key=>value response pair array
     */
    public function getAttachmentContent($ticketId, $attachmentId, $raw = false)
    {
        $url = $this->url."ticket/$ticketId/attachments/$attachmentId/content";
        $this->setRequestUrl($url);

        $response = $this->send();

        if (! $raw) {
            return $this->parseResponse($response);
        } else {
            // Remove unneeded newlines from response body and return it as string.
            $body = explode(chr(10), $response['body']);
            // getAttachmentContent even returns 3 newlines at the end, and cleanResponseBody only strips off 1
            // We therefor use array_splice($body, 0, -2) as an extra
            return implode(chr(10), $this->cleanResponseBody(array_splice($body, 0, -2)));
        }
    }

    /**
     * Get the history of a ticket
     * @param int $ticketId
     * @param boolean $longFormat Whether to return all data of each history node
     * @return array key=>value response pair array
     */
    public function getTicketHistory($ticketId, $longFormat = true)
    {
        if ($longFormat) {
            $url = $this->url."ticket/$ticketId/history?format=l";
        } else {
            $url = $this->url."ticket/$ticketId/history";
        }

        $this->setRequestUrl($url);

        $response = $this->send();
        return $longFormat ? $this->parseLongTicketHistoryResponse($response) : $this->parseResponse($response);
    }

    /**
     * Get the long form data of a specific ticket history node
     * @param int $ticketId
     * @param int $historyId
     * @return array key=>value response pair array
     */
    public function getTicketHistoryNode($ticketId, $historyId)
    {
        $url = $this->url."ticket/$ticketId/history/id/$historyId";

        $this->setRequestUrl($url);

        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Convenience wrapper to search for tickets.
     *
     * @see self::search()
     */
    public function searchTickets($query, $orderby, $format = 's')
    {
        return $this->search($query, $orderby, $format);
    }

    /**
     * General search based on a query
     *
     * Extend the Request Tracker class and implement custom search functions there
     * by passing $query and $orderBy to this function
     * @param string $query the query to run
     * @param string $orderBy how to order the query
     * @param string $format the format type (i,s,l)
     * @param string $type search for: 'ticket', 'queue', 'group' or 'user'?
     * @param array $fields fields to return
     *
     * @return array
     *      's' = ticket-id=>ticket-subject
     *      'i' = not implemented
     *      'l' = not implemented
     */
    public function search($query, $orderBy, $format = 's', $type = 'ticket', $fields = array())
    {
        $url = $this->url."search/$type?query=".urlencode($query);
        if (!empty($fields)) {
            $url .= '&fields=' . urlencode(implode(',', $fields));
        }
        $url .= "&orderby=$orderBy&format=$format";

        $this->setRequestUrl($url);

        $response = $this->send();

        $responseArray = array();

        if ($format=='s') {
            $responseArray = $this->parseResponse($response);
        } elseif ($format=='i') {
            return $response['body'];
        } elseif ($format=='l') {
            $responseArray = $this->parseLongFormatSearchResponse($response);
        }

        return $responseArray;
    }

    protected function parseResponse($response, $delimiter = ':')
    {
        $response = explode(chr(10), $response['body']);
        $response = $this->cleanResponseBody($response);

        return $this->parseResponseBody($response);
    }

    private function parseLongFormatSearchResponse($response, $delimiter = ':')
    {
        $resultNodes = array();
        $resultStrings = preg_split('/(?=id: )/', $response['body'], null);
        // First item contains RT version and newline, remove it.
        unset($resultStrings[0]);
        foreach ($resultStrings as $resultString) {
            $node = explode(chr(10), $resultString);
            // remove empty line in the end.
            array_pop($node);
            $resultNodes[] = $this->parseResponseBody($node, $delimiter);
        }

        return $resultNodes;
    }

    private function parseLongTicketHistoryResponse($response, $delimiter = ':')
    {
        $historyNodes = array();
        $historyNodeStrings = preg_split('/\# ([0-9]*)\/([0-9]*) \(id\/([0-9]*)\/total\)/', $response['body']);
        // First item contains RT version and newline, remove it.
        unset($historyNodeStrings[0]);
        foreach ($historyNodeStrings as $historyNodeString) {
            $node = explode(chr(10), $historyNodeString);
            $node = $this->cleanResponseBody($node);
            $node = $this->parseResponseBody($node, $delimiter);
            if (!empty($node['Attachments'])) {
                $node['Attachments'] = $this->parseResponseBody(explode(chr(10), $node['Attachments']), $delimiter);
            }
            else {
                // Normalize to an array.
                $node['Attachments'] = array();
            }
            $historyNodes[] = $node;
        }

        return $historyNodes;
    }

    private function cleanResponseBody(array $response)
    {
        array_shift($response); //skip RT status response
        array_shift($response); //skip blank line
        array_pop($response); //remove empty blank line in the end

        return $response;
    }

    private function parseResponseBody(array $response, $delimiter = ':')
    {
        $responseArray = array();
        $lastkey = null;
        foreach ($response as $line) {
            //RT will always preface a multiline with the length of the last key + length of $delimiter + one space)
            if(! is_null($lastkey) && preg_match('/^\s{' . ( strlen($lastkey) + strlen($delimiter) + 1 ) . '}(.*)$/', $line, $matches)) {
                $responseArray[$lastkey] .= "\n" . $matches[1];
            }
            elseif(! is_null($lastkey) && strlen($line) == 0) {
                $lastkey = null;
            }
            elseif(preg_match('/^#/', $line, $matches)) {
                $responseArray[$line] = '';
            }
            elseif(preg_match('/^([a-zA-Z0-9]+|CF\.{[^}]+})' . $delimiter . '\s(.*)$/', $line, $matches)) {
                $lastkey = $matches[1];
                $responseArray[$lastkey] = $matches[2];
            }
        }

        return $responseArray;
    }

    private function parseArray($contentArray)
    {
        $content = "";
        foreach ($contentArray as $key => $value) {
            $content .= "$key: $value".chr(10);
        }
        return $content;
    }

    /**
     * Get metadata for a user
     * @param int|string $userId either the user id or the user login
     * @return array key=>value response pair array
     */
    public function getUserProperties($userId)
    {
        $url = $this->url."user/$userId";

        $this->setRequestUrl($url);

        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Create a user
     * @param array $content the user fields as fieldname=>fieldvalue array
     * @return array key=>value response pair array
     */
    public function createUser($content)
    {
        $content['id'] = 'user/new';
        $url = $this->url."user/new";

        $this->setRequestUrl($url);
        $this->setPostFields($content);

        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Edit user
     * @param int $userId
     * @param array $content the user fields as fieldname=>fieldvalue array
     * @return array key=>value response pair array
     */
    public function editUser($userId, $content)
    {
        $url = $this->url."user/$userId/edit";

        $this->setRequestUrl($url);
        $this->setPostFields($content);

        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Convenience wrapper to search for users.
     *
     * @see self::search()
     */
    public function searchUsers($query = '', $orderby = '', $format = 's')
    {
        return $this->search($query, $orderby, $format, 'user');
    }

    /**
     * Get metadata of a queue
     * @param int $queueId
     * @return array key=>value response pair array
     */
    public function getQueueProperties($queueId)
    {
        $url = $this->url."queue/$queueId";

        $this->setRequestUrl($url);

        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Convenience wrapper to search for queues.
     *
     * @see self::search()
     */
    public function searchQueues($query = '', $orderby = '', $format = 's')
    {
        return $this->search($query, $orderby, $format, 'queue');
    }

    /**
     * Get metadata of a group
     * @param int $groupId
     * @param array $fields
     *   Ask for specific fields to retrieve. (eg Members or Name)
     * @return array key=>value response pair array
     */
    public function getGroupProperties($groupId, array $fields = array())
    {
        $url = $this->url."group/$groupId";
        if ($fields) {
            $url .= '?fields=' . implode(',', $fields);
        }

        $this->setRequestUrl($url);

        $response = $this->send();
        return $this->parseResponse($response);
    }

    /**
     * Convenience wrapper to search for groups.
     *
     * @see self::search()
     */
    public function searchGroups($query = '', $orderby = '', $format = 's')
    {
        return $this->search($query, $orderby, $format, 'group');
    }

    /**
     * Toggles SSL certificate verification.
     * @param $verify boolean false to turn off verification, true to enable
     */
    public function verifySslCertificates($verify)
    {
        $this->enableSslVerification = $verify;
    }

    protected function setRequestUrl($url)
    {
        $this->requestUrl = $url;
    }

    protected function setPostFields($data)
    {
        $this->postFields = $data;
    }

    private function post($data, $contentType = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->requestUrl);
        curl_setopt($ch, CURLOPT_POST, 1);

        if (!empty($contentType)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: $contentType"));
        }

        if (!$this->enableSslVerification) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        array_unshift($data, "");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = "";
        if ($response===false) {
            $error = curl_error($ch);
        }
        curl_close($ch);

        if ($response === false) {
            throw new RequestTrackerException("A fatal error occurred when communicating with RT :: ".$error);
        }

        # Fix for situations in which RT replies like this:
        #  RT/4.4.1 401 Credentials required
        #  
        #  Your username or password is incorrect
        if($code == 200 && preg_match('!^RT/\d+\.\d+\.\d+\s+(\d{3})\s+.+?\n\n(.*)\n$!s', $response, $matches)) {
            $code     = $matches[1];
            //$response = $matches[2];
        }

        if ($code == 401) {
            throw new AuthenticationException("The user credentials were refused.");
        }

        if ($code != 200) {
            throw new HttpException("An error occurred : [$code] :: $response");
        }

        $response =  array('code'=>$code, 'body'=>$response);
        return $response;
    }

    /**
     * Dont save any stateful information when serializing.
     */
    public function __sleep()
    {
        return array('url', 'user', 'pass', 'enableSslVerification');
    }
}

class RequestTrackerException extends Exception
{
}
class AuthenticationException extends RequestTrackerException
{
}
class HttpException extends RequestTrackerException
{
}
