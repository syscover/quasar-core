<?php namespace Quasar\Core\Exceptions;

class ModelNotChangeException extends \Exception
{
    protected $message = 'At least one value must change';
}
