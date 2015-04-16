<?php
/**
 * User: Sam
 * Date: 4/15/2015
 * Time: 7:38 PM
 */

namespace Dersam\RT\Requests;

use Dersam\RT\Client;
use Dersam\RT\Request;

abstract class V2Request extends Request
{
    public function send(Client $client)
    {
        // TODO: Implement send() method.
    }
}