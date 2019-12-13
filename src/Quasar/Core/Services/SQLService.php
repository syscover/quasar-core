<?php namespace Quasar\Core\Services;

use Illuminate\Support\Facades\Schema;
use Quasar\Core\Exceptions\ParameterNotFoundException;
use Quasar\Core\Exceptions\ParameterValueException;

/**
 * Class SQLService
 * @package Quasar\Core\Services
 */
class SQLService
{
    const OPERATORS = [
        'EQUALS'    => '=',
        'IS_NULL'   => 'IS NULL'
    ];

    /**
     * @param $queryBuilder
     * @param null $query sql to filter total count
     * @return mixed
     */
    public static function countGroupPaginateTotalRecords($queryBuilder, array $queries = null)
    {
        if($queries && is_array($queries)) $queryBuilder = SQLService::setQueryGroup($queryBuilder, $queries);

        return $queryBuilder->count();
    }

    /**
     * @param $queryBuilder
     * @param $query
     * @return mixed
     *
     * Example of query parameter
     *  [
     *      'query' => [
     *          'type'  => 'and',  // operator between groups
     *          'sql'   => [
     *              [
     *                  'type'  => 'and', // operator between sql
     *                  'sql'   => [
     *                      [
     *                          'column' => 'prefix',
     *                          'operator' => '>',
     *                          'value' => 40
     *                      ],
     *                      [
     *                          'column' => 'prefix',
     *                          'operator' => '<',
     *                          'value' => 50
     *                      ]
     *                  ],
     *              ],
     *              [
     *                  'type'  => 'or', // operator between sql
     *                  'sql'   => [
     *                      [
     *                          'column' => 'lang_id',
     *                          'operator' => '=',
     *                          'value' => 'es'
     *                      ],
     *                      [
     *                          // raw property
     *                          'raw' => 'CASE `market_product`.`product_class_tax_id` WHEN 1 THEN (`market_product`.`subtotal` * 1.21) > 8 END'
     *                      ]
     *                  ]
     *              ]
     *          ]
     *      ]
     *  ];
     */
    public static function setQueryGroup($queryBuilder, array $query)
    {
        if(isset($query['type']))
        {
            $query['type'] = strtoupper($query['type']);

            foreach ($query['sql'] as $sql)
            {
                if(isset($sql['column']) || isset($sql['raw']))
                {
                    if($query['type'] === 'AND')
                    {
                        if(isset($sql['raw']))
                        {
                            $queryBuilder->whereRaw($sql['raw']);
                        }
                        elseif (isset($sql['relation']))
                        {
                            $queryBuilder->whereHas($sql['relation'], function ($queryBuilder) use ($sql) {
                                $queryBuilder->where($sql['column'], $sql['operator'], $sql['value']);
                            });
                        }
                        else
                        {
                            $queryBuilder->where($sql['column'], $sql['operator'], $sql['value']);
                        }

                    }
                    elseif ($query['type'] === 'OR')
                    {
                        if(isset($sql['raw']))
                        {
                            $queryBuilder->orWhereRaw($sql['raw']);
                        }
                        elseif (isset($sql['relation']))
                        {
                            $queryBuilder->orWhereHas($sql['relation'], function ($queryBuilder) use ($sql) {
                                $queryBuilder->where($sql['column'], $sql['operator'], $sql['value']);
                            });
                        }
                        else
                        {
                            $queryBuilder->orWhere($sql['column'], $sql['operator'], $sql['value']);
                        }
                    }
                }
                else // is a grouped queryBuilder
                {
                    if($query['type'] === 'AND')
                    {
                        $queryBuilder->where(function ($queryBuilder) use ($sql) {
                            self::setQueryGroup($queryBuilder, $sql);
                        });
                    }
                    elseif ($query['type'] === 'OR')
                    {
                        $queryBuilder->orWhere(function ($queryBuilder) use ($sql) {
                            self::setQueryGroup($queryBuilder, $sql);
                        });
                    }
                }
            }
        }

        return $queryBuilder;
    }

    

    /**
     * DEPRECATED by countGroupPaginateTotalRecords
     * @param $queryBuilder
     * @param null $queries sql to filter total count
     * @return mixed
     * @throws ParameterNotFoundException
     * @throws ParameterValueException
     */
    public static function count($queryBuilder, $queries = null)
    {
        if($queries && is_array($queries)) $queryBuilder = SQLService::setQueryFilter($queryBuilder, $queries);

        return $queryBuilder->count();
    }

    /*/
     * @param $queryBuilder
     * @param $queries
     * @return mixed
     * @throws ParameterNotFoundException
     * @throws ParameterValueException
     */
    public static function makeQueryBuilder($queryBuilder, $queries)
    {
        // commands without pagination and limit
        foreach ($queries as $query)
        {
            if(! isset($query['command']))
                throw new ParameterNotFoundException('Parameter command not found in request, please set command parameter in ' . json_encode($query));

            if(($query['command'] === "WHERE" || $query['command'] === "ORDER_BY") && ! isset($query['column']))
                throw new ParameterNotFoundException('Parameter column not found in request, please set column parameter in ' . json_encode($query));

            if(($query['command'] === "WHERE" || $query['command'] === "ORDER_BY") && ! isset($query['operator']))
                throw new ParameterNotFoundException('Parameter operator not found in request, please set operator parameter in ' . json_encode($query));

            switch ($query['command'])
            {
                case 'OFFSET':
                case 'LIMIT':
                case 'ORDER_BY':
                    // commands not accepted
                    break;
                case 'WHERE':
                    $queryBuilder->where($query['column'], self::OPERATORS[$query['operator']], $query['value']);
                    break;
                case 'orWhere':
                    $queryBuilder->orWhere($query['column'], $query['operator'], $query['value']);
                    break;
                case 'whereIn':
                    $queryBuilder->whereIn($query['column'], $query['value']);
                    break;
                case 'whereJsonContains':
                    $queryBuilder->whereJsonContains($query['column'], $query['value']);
                    break;

                default:
                    throw new ParameterValueException('command parameter has a incorrect value, must to be where');
            }
        }

        return $queryBuilder;
    }

    /**
     * @param   $queryBuilder
     * @param   array $queries
     * @return  mixed
     * @throws  ParameterNotFoundException
     * @throws  ParameterValueException
     */
    public static function makeQueryBuilderOrderedAndLimited($queryBuilder, $queries = null)
    {
        if(! $queries) return $queryBuilder;

        // sentences for order query and limited
        foreach ($queries as $query)
        {
            if(! isset($query['command']))
                throw new ParameterNotFoundException('Parameter command not found in request, please set command parameter in ' . json_encode($query));

            if(($query['command'] === "OFFSET" || $query['command'] === "LIMIT") && ! isset($query['value']))
                throw new ParameterNotFoundException('Parameter value not found in request, please set value parameter in: ' . json_encode($query));

            switch ($query['command']) {
                case 'WHERE':
                case 'orWhere';
                case 'whereIn';
                case 'whereJsonContains';
                    // commands not accepted, already
                    // implemented in Quasar\Core\Services\SQLService::setQueryFilter method
                    break;
                case 'ORDER_BY':
                    $queryBuilder->orderBy($query['column'], $query['operator']);
                    break;
                case 'OFFSET':
                    $queryBuilder->offset($query['value']);
                    break;
                case 'LIMIT':
                    $queryBuilder->limit($query['value']);
                    break;

                default:
                    throw new ParameterValueException('command parameter has a incorrect value, must to be offset or take');
            }
        }

        return $queryBuilder;
    }

    /**
     * @param int           $uuid
     * @param string        $modelClassName
     * @param string|null   $commonUuid
     * @param string|null   $commonClassName
     * @param array         $filters            filters to select and delete records
     * @return mixed
     */
    public static function deleteRecord(
        $uuid,
        string $modelClassName,
        string $commonUuid = null,
        string $commonClassName = null,
        array $filters = []
    )
    {
        // get data to do model queries
        $model      = new $modelClassName;
        $table      = $model->getTable();
        $primaryKey = $model->getKeyName();

        /**
         *  Delete object with lang.
         *  If destroy baseLang object, delete all objects with this id
         */
        if(isset($commonUuid))
        {
            /**
             * Check if controller has defined $commonClassName property,
             * if has $commonClassName, this means that the translations are in another table.
             * Get table name to do the query
             */
            if($commonClassName !== null)
            {
                // get data to do model queries
                $commonModel    = new $commonClassName;
                $commonTable    = $commonModel->getTable();

                // get object from main table and lang table
                // in builder method do the join between table and table lang
                $object = $model->builder()
                    ->where($commonTable . '.common_uuid', $commonUuid)
                    ->where($table . '.uuid', $uuid)
                    ->first();

                // check if must delete base_lang object
                if(base_lang() === $commonUuid)
                {
                    // Delete record from main table and delete records in table lang by relations
                    $model::where($table . '.uuid', $uuid)
                        ->delete();

                    return $object;
                }

                /**
                 * This option is for tables that dependent of other tables to set your languages
                 * set parameter $deleteLangDataRecord to false, because lang model haven't data_lag column
                 */
                $commonModel->deleteTranslationRecord($uuid, $commonUuid, false);

                /**
                 * This kind of tables has field data_lang in main table, not in lang table
                 * delete data_lang parameter
                 */
                $model->deleteDataLang($commonUuid, $uuid, 'id');

                /**
                 * Count records, to know if has more lang
                 */
                $nRecords = $commonModel->builder()
                    ->where($commonTable . '.uuid', $uuid)
                    ->count();

                /**
                 * if haven't any lang record, delete record from main table
                 */
                if($nRecords === 0)
                {
                    $model->where($table . '.' . $primaryKey, $uuid)
                        ->delete();
                }

                return $object;
            }
            else
            {
                $query = $model->builder()
                    ->where($table . '.uuid', $uuid);

                /**
                 * The table may have lang parameter but not have the field common_uuid.
                 * Whe is false, the model overwrite method deleteTranslationRecord
                 * to delete json language field, for example in field table with labels column
                 */
                if(Schema::hasColumn($table, 'common_uuid')) $query->where($table . '.common_uuid', $commonUuid);

                $object = $query->filterQuery($filters)->first();

                // check if must delete base_lang object
                if(base_lang() === $commonUuid)
                {
                    // Delete record from main table and delete records in table lang by relations
                    $model::where($table . '.uuid', $uuid)
                        ->delete();

                    return $object;
                }

                // delete record from table without dependency from other table lang
                $model->deleteTranslationRecord($uuid, $commonUuid, true, $filters);

                return $object;
            }
        }
        else
        {
            // Delete single record
            $object = $model->builder()
                    ->where($table . '.uuid', $uuid)
                    ->filterQuery($filters)
                    ->first();

            $object->delete();

            return $object;
        }
    }






    /**
     * DEPRECATED
     */

    /**
     * DEPRECATED
     * Get query apply sql or filters
     *
     * @param $query
     * @param array $sql
     * @param null $filters
     * @return mixed
     */
    public static function getGroupQueryFiltered($query, $sql = null, $filters = null)
    {
        if(! $sql) $sql = [];

        // filter all data by lang
        if(isset($filters) && is_array($filters))
        {
            // filter query
            $query = SQLService::setQueryGroup($query, $filters);

            // apply query parameters over filter
            $query->where(function ($query) use ($sql) {
                SQLService::setQueryGroup($query, $sql);
            });
        }
        else
        {
            $query = SQLService::setQueryGroup($query, $sql);
        }

        return $query;
    }

    /**
     * DEPRECATED by getGroupQueryFiltered
     * Get query apply sql or filters
     *
     * @param $queryBuilder
     * @param array $query
     * @param null $constraints
     * @return mixed
     * @throws ParameterNotFoundException
     * @throws ParameterValueException
     */
    public static function getQueryFiltered($queryBuilder, array $query = null, array $constraints = null)
    {
        if(! $query) $query = [];

        // filter all data by lang
        if($constraints && is_array($constraints))
        {
            // filter query
            $queryBuilder = SQLService::setQueryFilter($queryBuilder, $constraints);

            // apply query parameters over filter
            $queryBuilder->where(function ($queryBuilder) use ($query) {
                SQLService::setQueryFilter($queryBuilder, $query);
            });
        }
        else
        {
            $queryBuilder = SQLService::setQueryFilter($queryBuilder, $query);
        }

        return $queryBuilder;
    }
}
