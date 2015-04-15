<?php
namespace Dersam\RT;


class Client {
    protected $verifySsl = true;

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
}