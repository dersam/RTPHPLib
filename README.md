#RTPHPLib
Provides an implementation of the Request Tracker API in PHP.

##Requirements
* PHP 5.3+
* curl

##Installation
`composer require dersam/rtphplib`

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
* Make your changes.
* Submit pull requests against master.
