<?php namespace Test\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Quasar\Admin\Models\Country;
use Quasar\Core\Services\SqlService;

class SqlServiceTest extends TestCase
{
    public function testMakeQueryBuilderSimpleWhere()
    {
        $queries = [
            [
                'command'   => 'WHERE',
                'column'    => 'uuid',
                'operator'  => 'EQUALS',
                'value'     => '4470b5ab-9d57-4c9d-a68f-5bf8e32f543a'
            ]
        ];

        $builder = Country::builder();

        $query = SqlService::makeQueryBuilder($builder, $queries);

        $this->assertEquals($query->toSql(), 'select * from `admin_country` where `uuid` = ?');
    }

    public function testMakeQueryBuilderSimpleMultipleOrWhere()
    {
        $queries = [
            [
                'command'   => 'WHERE',
                'column'    => 'uuid',
                'operator'  => 'EQUALS',
                'value'     => '4470b5ab-9d57-4c9d-a68f-5bf8e32f543a'
            ],
            [
                'command'   => 'OR_WHERE',
                'column'    => 'name',
                'operator'  => 'EQUALS',
                'value'     => 'Spain'
            ]
        ];

        $builder = Country::builder();

        $query = SqlService::makeQueryBuilder($builder, $queries);

        $this->assertEquals($query->toSql(), 'select * from `admin_country` where `uuid` = ? or `name` = ?');
    }
}
