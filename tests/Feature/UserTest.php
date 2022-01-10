<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testGetSellerOffersList() {
        $token = $this->getToken();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('get', '/api/user/sell-offers');

        $response->assertStatus(200)
                    ->assertJsonStructure([
                        'success',
                        'offer_list',
                        'total' 
                    ]);
    }

    public function testGetBuyerOffersList() {
        $token = $this->getToken();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('get', '/api/user/buy-offers');

        $response->assertStatus(200)
                    ->assertJsonStructure([
                        'success',
                        'offer_list',
                        'total' 
                    ]);
    }

    public function testSellerCreateOffer() {
        $token = $this->getToken();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('post', '/api/user/sell-offers', [
            'amount' => 100,
            'unlocked' => 1,
            'where_held' => 2,
            'desired_price' => 100,
        ]);

        $response->assertStatus(200)
                    ->assertJsonStructure([
                        'success',
                    ]);
    }

    public function testBuyerCreateOffer() {
        $token = $this->getToken();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('post', '/api/user/buy-offers', [
            'amount' => 100,
            'type' => 0,
            'desired_price' => 100,
        ]);

        $response->assertStatus(200)
                    ->assertJsonStructure([
                        'success',
                    ]);
    }

    public function testBuyerUpdateOffer() {
        $token = $this->getToken();
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->json('put', '/api/user/buy-offers/1', [
            'amount' => 100,
        ]);

        $response->assertStatus(200)
                    ->assertJsonStructure([
                        'success',
                    ]);
    }
}
