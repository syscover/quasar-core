<?php namespace Quasar\Core\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Quasar\Core\Traits\CanManageCrud;
use Quasar\Core\Traits\CanManageDataLang;
use Illuminate\Support\Str;

/**
 * Class Model
 * @package Quasar\Pulsar\Core
 */

class CoreModel extends BaseModel
{
    use CanManageCrud, CanManageDataLang;

    public function getAttribute($key)
    {
        return parent::getAttribute(Str::snake($key));
    }

    public function setAttribute($key, $value)
    {
        return parent::setAttribute(Str::snake($key), $value);
    }
}