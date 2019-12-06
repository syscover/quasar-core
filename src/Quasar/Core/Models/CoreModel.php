<?php namespace Quasar\Core\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Quasar\Core\Traits\CanManageCrud;
use Quasar\Core\Traits\CanManageDataLang;

/**
 * Class Model
 * @package Quasar\Pulsar\Core
 */

class CoreModel extends BaseModel
{
    use CanManageCrud, CanManageDataLang;
}