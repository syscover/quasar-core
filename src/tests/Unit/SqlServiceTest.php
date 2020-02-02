<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Quasar\Core\Services\SqlService;

class SqlServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSimpleQuery()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
