<?php
/**
 * User: Sam
 * Date: 4/14/2015
 * Time: 10:12 PM
 */

namespace Dersam\RT;

class Validator
{
    protected $lastErrors = array();

    /**
     * @param Request $request
     * @return boolean
     */
    public function validate(Request $request)
    {

    }

    /**
     * @return array
     */
    public function getLastErrors()
    {
        return $this->lastErrors;
    }
}