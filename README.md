#RTPHPLib v2.0.0

[![Build Status](https://travis-ci.org/dersam/RTPHPLib.svg?branch=2.0.0)](https://travis-ci.org/dersam/RTPHPLib.svg?branch=2.0.0)

Provides an implementation of the Request Tracker API in PHP.

##Requirements
The curl php extension is required.

##Versioning
As of 2.0.0, RTPHPLib will follow semantic versioning.


Available as a composer package at http://packagist.org/packages/dersam/rt-php-lib.

See example.php for usage instructions.

Requires curl.

See http://requesttracker.wikia.com/wiki/REST for information on available fields. 
Note that if a request type has mandatory fields, they are requested in the function
call, or (in certain cases) automatically added to the request.  So you don't need
to specify the ticket id in content, or the type of action.

Please report any issues at https://github.com/dersam/RTPHPLib/issues 