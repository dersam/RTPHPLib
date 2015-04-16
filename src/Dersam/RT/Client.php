<?php
namespace Dersam\RT;

use Dersam\RT\Exceptions\AuthenticationException;
use Dersam\RT\Exceptions\HttpException;
use Dersam\RT\Exceptions\RTException;

abstract class Client
{
    protected $verifySsl = true;
    protected $url;
    protected $user;
    protected $pass;
    protected $validator;

    /**
     * Create a new instance for API requests
     * @param string $baseUrl
     *          The base URL to your request tracker installation. For example,
     *          if your RT is located at "http://rt.example.com", your rootUrl
     *          would be "http://rt.example.com".  There should be no trailing slash.
     * @param string $user The username to authenticate with.
     * @param string $pass The password to authenticate with.
     */
    public function __construct($baseUrl, $user, $pass)
    {
        $this->url = $baseUrl;
        $this->user = $user;
        $this->pass = $pass;
        $this->validator = new Validator();
    }

    /**
     * @return boolean
     */
    public function isVerifyingSsl()
    {
        return $this->verifySsl;
    }

    /**
     * @param boolean $verifySsl
     */
    public function setVerifySsl($verifySsl)
    {
        $this->verifySsl = $verifySsl;
    }

    /**
     * Injects the credentials into the request.
     *
     * @param Request $request
     * @return Response|boolean Returns a parsed Response object
     *          or false if validation failed-
     *          original Request will contain the validation errors
     * @throws AuthenticationException
     * @throws HttpException
     * @throws RTException
     */
    public function send(Request &$request)
    {
        return $request->send($this);
    }

    /**
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }
}
