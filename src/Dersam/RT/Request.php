<?php
namespace Dersam\RT;


use Dersam\RT\Exceptions\AuthenticationException;
use Dersam\RT\Exceptions\HttpException;
use Dersam\RT\Exceptions\RTException;

abstract class Request {
    protected $validations = array();
    protected $validationErrors = array();
    protected $requestFields  = array();
    protected $requestUri     = '';
    protected $contentType    = null;

    /**
     * @return Response
     */
    abstract function makeResponseInstance();

    public function serializeFields(){
        return $this->requestFields;
    }

    public function getField($fieldName){
        return isset($fieldName) || $this->requestFields[$fieldName];
    }

    public function setField($fieldName, $content){
        $requestFields[$fieldName] = $content;
    }

    public function setList($fields){
        foreach($fields as $key=>$value){
            $this->setField($key, $value);
        }
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * @param string $requestUri
     */
    public function setRequestUri($requestUri)
    {
        $this->requestUri = $requestUri;
    }

    /**
     * @return array
     */
    public function getValidations()
    {
        return $this->validations;
    }

    /**
     * @return null
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param null $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return array
     */
    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

    /**
     * @param array $validationErrors
     */
    public function setValidationErrors($validationErrors)
    {
        $this->validationErrors = $validationErrors;
    }

    public function send(Client $client){
        $validator = $client->getValidator();
        if(!$validator->validate($this)){
            $this->setValidationErrors($validator->getLastErrors());
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $client->getUrl().$this->getRequestUri());
        curl_setopt($ch, CURLOPT_POST, 1);

        if(!($this->getContentType() == '')){
            curl_setopt($ch, CURLOPT_HTTPHEADER,
                array("Content-type: ".$this->getContentType()));
        }

        if(!$client->isVerifyingSsl()){
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        $data = array(
            'user'=>$client->getUser(),
            'pass'=>$client->getPass(),
            'content'=>$this->serializeFields()
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

        $response = $this->makeResponseInstance();
        $response->parse($code, $response);
        return $response;
    }
}