<?php
namespace Dersam\RT;

abstract class Request
{
    protected $validations = array();
    protected $validationErrors = array();
    protected $requestFields  = array();
    protected $requestUri     = '';
    protected $contentType    = null;

    /**
     * @return Response
     */
    abstract public function makeResponseInstance();

    abstract public function send(Client $client);

    abstract public function serializeFields();

    public function getField($fieldName)
    {
        return isset($fieldName) || $this->requestFields[$fieldName];
    }

    public function setField($fieldName, $content)
    {
        $this->requestFields[$fieldName] = $content;
    }

    public function setList($fields)
    {
        foreach ($fields as $key => $value) {
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
}
