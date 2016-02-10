#RTPHPLib
[![Build Status](https://travis-ci.org/dersam/RTPHPLib.svg?branch=master)](https://travis-ci.org/dersam/RTPHPLib)

Provides an implementation of the Request Tracker API in PHP.

##Requirements
* PHP 5.3+
* curl

##Installation
`composer require dersam/rt-php-lib`

Or just download and include RequestTracker.php.

##Usage
See example.php for usage instructions.

See http://requesttracker.wikia.com/wiki/REST for information on available fields. 
Note that if a request type has mandatory fields, they are requested in the function
call, or (in certain cases) automatically added to the request.  So you don't need
to specify the ticket id in content, or the type of action.

##Issues
Please report any issues at https://github.com/dersam/RTPHPLib/issues 

##Contributing
* Fork the repository.
* Make your changes (Adding tests makes you a good person!).
* Submit pull requests against master.

##Tests
Running `phpunit` from the project root will run the tests. The tests currently 
expect an RT instance running on `localhost:8080`.  You can easily get a local 
instance by using the `netsandbox/request-tracker` docker container. If your
instance is not at localhost, you can specify a different uri by setting the
`RT_ENDPOINT` environment variable.
