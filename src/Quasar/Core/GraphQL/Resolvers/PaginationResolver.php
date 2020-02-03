<?php namespace Quasar\Core\GraphQL\Resolvers;

use Illuminate\Support\Facades\DB;
use Quasar\Core\Services\SQLService;

class PaginationResolver
{
    public function total($root, array $args)
    {
        // save eager loads to load after execute FOUND_ROWS() MySql Function
        // FOUND_ROWS function get total number rows of last query, if model has eagerLoads, after execute the query model,
        // will execute eagerLoads losing the reference os last query to execute FOUND_ROWS() MySql Function
        $eagerLoads     = $root->queryBuilder->getEagerLoads();
        $queryBuilder   = $root->queryBuilder->setEagerLoads([]);

        // count total objects with constraints
        $total = SQLService::makeQueryBuilder($queryBuilder, $args['constraints'] ?? null)->count();

        // to count elements, if sql has a groupBy statement, count function always return 1
        // check if total is equal to 1, execute FOUND_ROWS() to guarantee the real result
        // https://github.com/laravel/framework/issues/22883
        // https://github.com/laravel/framework/issues/4306
        return $total === 1 ? DB::select(DB::raw("select FOUND_ROWS() as 'total'"))[0]->total : $total;
    }

    public function objects($root, array $args)
    {
        //ATTENTION, this queryBuilder keep constraints applied in total method
        
        // save eager loads to load after execute FOUND_ROWS() MySql Function
        // FOUND_ROWS function get total number rows of last query, if model has eagerLoads, after execute the query model,
        // will execute eagerLoads losing the reference os last query to execute FOUND_ROWS() MySql Function
        $eagerLoads     = $root->queryBuilder->getEagerLoads();
        $queryBuilder   = $root->queryBuilder->setEagerLoads([]);

        // get query builder filtered by sql statement and filtered by filters statement
        $queryBuilder = SQLService::makeQueryBuilder($queryBuilder, $args['query'] ?? null);

        // get query ordered and limited
        $queryBuilder = SQLService::makeQueryBuilderOrderedAndLimited($queryBuilder, $args['query'] ?? null);

        // get objects filtered
        $objects = $queryBuilder->get();

        // execute FOUND_ROWS() MySql Function and save filtered value, to be returned in resolveFilteredField() function
        // this function is executed after resolveObjectsField according to the position of fields marked in the GraphQL query
        $root->filtered = DB::select(DB::raw("select FOUND_ROWS() as 'filtered'"))[0]->filtered;

        // load eager loads
        $objects->load($eagerLoads);

        return $objects;
    }

    public function filtered($root, array $args)
    {
        return $root->filtered;
    }
}
