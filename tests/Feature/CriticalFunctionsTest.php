<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CriticalFunctionsTest extends TestCase
{




    public function testLogin(): void
    {
        $response = $this->post('/api/login');
        $response->assertStatus(200);
    }





    public function testMe(): void
    {
        $response = $this->get('/api/me');
        $response->assertStatus(302);
    }




}
