<?php namespace Quasar\Core\Exceptions;

use Exception;

class ModelNotChangeException extends Exception
{
    protected $message = 'At least one value must change';
}
