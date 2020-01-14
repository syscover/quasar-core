<?php namespace Quasar\Core\Services;

use Illuminate\Support\Str;

class SlugService
{
    /**
     *  Function to check if slug exists
     *
     * @access  public
     * @param   string          $model
     * @param   string          $slug
     * @param   string          $column
     * @param   integer|string  $uuid
     * @param   null|string     $langUuid
     * @return  string          $slug
     */
    public static function checkSlug($model, $slug, $uuid = null, $column = 'slug', $langUuid = null)
    {
        $slug   = Str::slug($slug);
        $model  = new $model;

        $queryBuilder = $model->where($column, $slug);

        if ($langUuid !== null) $queryBuilder->where('lang_uuid', $langUuid);
        if ($uuid !== null)     $queryBuilder->whereNotIn('uuid', [$uuid]);
        $n = $queryBuilder->count();

        if ($n > 0) 
        {
            $suffix = 0;
            while ($n > 0) 
            {
                $suffix++;
                $slug = $slug . '-' . $suffix;

                $queryBuilder = $model->where($column, $slug);
                if ($uuid !== null) $queryBuilder->whereNotIn('uuid', [$uuid]);
                $n = $queryBuilder->count();
            }
        }

        return $slug;
    }
}
