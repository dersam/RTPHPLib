<?php
namespace Dersam\RT;


use Dersam\RT\Exceptions\AuthenticationException;
use Dersam\RT\Exceptions\HttpException;
use Dersam\RT\Exceptions\RTException;

class Client {
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
    function __construct($baseUrl, $user, $pass){
        $this->url = $baseUrl."/REST/1.0";
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
     * @param Request $request
     * @return Response|boolean Returns a parsed Response object
     *     or false if validation failed- request contains validation errors
     * @throws AuthenticationException
     * @throws HttpException
     * @throws RTException
     */
    public function send(Request &$request){
        if(!$this->validator->validate($request)){
            $request->setValidationErrors($this->validator->getLastErrors());
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url.$request->getRequestUri());
        curl_setopt($ch, CURLOPT_POST, 1);

        if(!($request->getContentType() == '')){
            curl_setopt($ch, CURLOPT_HTTPHEADER,
                array("Content-type: ".$request->getContentType()));
        }

        if(!$this->isVerifyingSsl()){
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        $data = array(
            'user'=>$this->user,
            'pass'=>$this->pass,
            'content'=>$request->serializeFields()
        );

        array_unshift($data, "");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = "";

        if($response===false){
            $error = curl_error($ch);
        }
        curl_close($ch);

        if($response === false){
            throw new RTException("A fatal error occurred when communicating with RT :: ".$error);
        }

        if($code == 401){
            throw new AuthenticationException("The user credentials were refused.");
        }

        if($code != 200){
            throw new HttpException("An error occurred : [$code] :: $response");
        }

        $response = $request->makeResponseInstance();
        $response->parse($code, $response);
        return $response;
    }
}