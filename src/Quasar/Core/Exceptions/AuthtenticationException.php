<?php namespace Quasar\Core\Exceptions;

use Exception;

class AuthenticateException extends Exception
{
    protected $message = 'Error to authenticate user';
}
