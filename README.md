#RTPHPLib v2

[![Build Status](https://travis-ci.org/dersam/RTPHPLib.svg?branch=2.0.0)](https://travis-ci.org/dersam/RTPHPLib.svg?branch=2.0.0)

Provides an implementation of the Request Tracker API in PHP.

##Requirements
The curl php extension is required.

##Versioning
As of 2.0.0, RTPHPLib will follow semantic versioning.

*2.0.0 flagrantly breaks backward compatibility*. It requires you to build 
requests completely differently from 1.0. This change was done so that if RT
should ever update the API spec in the future, the new API can be supported
without needing to break backwards compatibility with the old API.

##Installation
Available as a composer package at http://packagist.org/packages/dersam/rt-php-lib.

Or, just download RequestTracker.php and require it in.

##Usage

##TODO: Detailed instructions on how to use 2.0's requests


See http://requesttracker.wikia.com/wiki/REST for information on available fields.

##Issues
Please report any issues at https://github.com/dersam/RTPHPLib/issues.