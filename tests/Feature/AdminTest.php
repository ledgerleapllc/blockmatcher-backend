<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTest extends TestCase
{
    public function testGetSellOffersList() {
        $token = $this->getToken();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('get', '/api/admin/sell-offers');

        $response->assertStatus(200)
                    ->assertJsonStructure([
                        'success',
                        'offer_list',
                        'total' 
                    ]);
    }

    public function testGetBuyOffersList() {
        $token = $this->getToken();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('get', '/api/admin/buy-offers');

        $response->assertStatus(200)
                    ->assertJsonStructure([
                        'success',
                        'offer_list',
                        'total' 
                    ]);
    }

    public function testGetBatchesList() {
        $token = $this->getToken();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('get', '/api/admin/batches');

        $response->assertStatus(200)
                    ->assertJsonStructure([
                        'success',
                        'batch_list',
                        'total' 
                    ]);
    }

    public function testCreateBatch() {
        $token = $this->getToken();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('post', '/api/admin/batches', [
            'notes' => 'Example',
            'price' => 10,
            'checks' => [1],
            'buyChecks' => [1],
        ]);

        $response->assertStatus(200)
                    ->assertJsonStructure([
                        'success',
                    ]);
    }

    public function testUpdateBatch() {
        $token = $this->getToken();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('put', '/api/admin/batches/1', [
            'notes' => 'Example',
        ]);

        $response->assertStatus(200)
                    ->assertJsonStructure([
                        'success',
                    ]);
    }
}
