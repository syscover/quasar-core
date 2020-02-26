<?php namespace Quasar\Core\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Quasar\Core\Traits\CanManageCrud;
use Quasar\Core\Traits\CanManageDataLang;
use Quasar\Core\Traits\CamelCasing;
use Quasar\Core\Traits\Uuid;

/**
 * Class Model
 * @package Quasar\Core\Models
 */

class CoreModel extends BaseModel
{
    use CanManageCrud, CanManageDataLang, CamelCasing, Uuid;
}