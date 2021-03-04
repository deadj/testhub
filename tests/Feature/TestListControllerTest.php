<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TestListControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testPrintPage()
    {
        factory(\App\Models\Test::class)->create(['id' => 1]);
        factory(\App\Models\Test::class)->create(['id' => 2]);

        $response = $this->get('/tests');
        $response->assertStatus(200);      
    }
}
