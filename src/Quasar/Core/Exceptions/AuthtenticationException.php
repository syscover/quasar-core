<?php namespace Quasar\Core\Exceptions;

use Exception;

class AuthenticationException extends Exception
{
    protected $message = 'Error to authenticate user';
}
