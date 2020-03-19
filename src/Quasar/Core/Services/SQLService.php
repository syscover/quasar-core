<?php namespace Quasar\Core\Services;

use Quasar\Core\Exceptions\ParameterNotFoundException;
use Quasar\Core\Exceptions\ParameterValueException;
use Quasar\Core\Support\Operator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class SQLService
 * @package Quasar\Core\Services
 */
class SQLService
{
    /*
     * @param \Illuminate\Database\Eloquent\Builder $queryBuilder
     * @param array                                 $queries
     * @return mixed
     * @throws ParameterNotFoundException
     * @throws ParameterValueException
     */
    public static function makeQueryBuilder($queryBuilder, $queries = [])
    {
        if (! is_array($queries)) return $queryBuilder;

        // queries less OFFSET, LIMIT and ORDER_BY
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
                    $queryBuilder->where($query['column'], Operator::${$query['operator']}, $query['value']);
                    break;
                case 'OR_WHERE':
                    $queryBuilder->orWhere($query['column'], $query['operator'], $query['value']);
                    break;
                case 'WHERE_IN':
                    $queryBuilder->whereIn($query['column'], $query['value']);
                    break;
                case 'WHERE_JSON_CONTAINS':
                    $queryBuilder->whereJsonContains($query['column'], $query['value']);
                    break;
                case 'WHERE_HAS':
                    $queryBuilder->whereHas($query['column'], function(Builder $queryBuilder) use ($query) {
                        self::makeQueryBuilder($queryBuilder, $query['query']);
                    });
                    break;
                case 'OR_WHERE_HAS':
                    $queryBuilder->orWhereHas($query['column'], function(Builder $queryBuilder) use ($query) {
                        self::makeQueryBuilder($queryBuilder, $query['query']);
                    });
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
                case 'OR_WHERE';
                case 'OR_WHERE_HAS';
                case 'WHERE':
                case 'WHERE_HAS';
                case 'WHERE_IN';
                case 'WHERE_JSON_CONTAINS';
                    // commands not accepted, already
                    // implemented in Quasar\Core\Services\SQLService::makeQueryBuilder method
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
     *                          'column' => 'lang_uuid',
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
     * @param string        $uuid
     * @param object        $model
     * @return mixed
     */
    public static function deleteRecord(
        string $uuid,
        object $model
    )
    {
        // get data to do model queries
        $table      = $model->getTable();
        $primaryKey = $model->getKeyName();
        $object     = $model->builder()
                    ->where($table . '.uuid', $uuid)
                    ->first();
        $objects    = null; // variable that contain various objects to delete, is used when delete object with base lang

        // Check if object has a commonUuid, to know if has multiple languages
        if ($object->commonUuid)
        {
            // delete records with same commonUuid
            if (baseLangUuid() === $object->langUuid)
            {
                $objects = $model::where($table . '.common_uuid', $object->commonUuid)->get();

                // Delete records from same common uuid by delete main language
                $model::where($table . '.common_uuid', $object->commonUuid)->delete();
            }
            // delete simple record
            else
            {
                // delete record from table without dependency from other table lang
                $object->delete();
                $object->deleteDataLang();
            }

            /**
             * Check if controller has defined $langModelClassName property,
             * if has $langModelClassName, this means that the translations are in another table.
             * Get table name to do the query
             */
            
            // TODO repasar si es langModelClassName
            if(false) 
            {
                if ($langModelClassName)
                {
                    // get data to do model queries
                    $langModel    = new $langModelClassName;
                    $langTable    = $langModel->getTable();
    
                    // get object from main table and lang table
                    // in builder method do the join between table and table lang
                    $object = $model->builder()
                        ->where($langTable . '.lang_uuid', $langUuid)
                        ->where($table . '.uuid', $uuid)
                        ->first();
    
                    // check if must delete base lang object
                    if (baseLangUuid() === $langUuid)
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
                }
                else
                {
                    // check if must delete base lang object
                    if (baseLangUuid() === $langUuid)
                    {
                        // Delete records from same common uuid by delete main language
                        $model::where($table . '.common_uuid', $object->commonUuid)->delete();
                    }
                    else
                    {
                        // delete record from table without dependency from other table lang
                        $object->delete();
                        $object->deleteDataLang();
                    }
                }
            }
        }
        else
        {
            // Delete single record
            $object = $model->builder()
                    ->where($table . '.uuid', $uuid)
                    ->first();

            $object->delete();
        }

        // return objects collection deleted
        return $objects ? $objects : collect([$object]);
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
            $queryBuilder = SQLService::makeQueryBuilder($queryBuilder, $constraints);

            // apply query parameters over filter
            $queryBuilder->where(function ($queryBuilder) use ($query) {
                SQLService::makeQueryBuilder($queryBuilder, $query);
            });
        }
        else
        {
            $queryBuilder = SQLService::makeQueryBuilder($queryBuilder, $query);
        }

        return $queryBuilder;
    }
}
