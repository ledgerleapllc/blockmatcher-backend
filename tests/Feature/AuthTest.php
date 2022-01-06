<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    // Register API Test - Buyer
    public function testBuyerRegister() {
        $user = [
            'name' => 'Test Buyer',
            'email' => 'buyer@blockmatcher.com',
            'type' => 'buyer',
            'password' => 'TestBuyer111',
            'telegram' => '@testbuyer',
        ];

        $response = $this->json('POST', '/api/register', $user, [
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(200)
                    ->assertJsonStructure([
                        'success',
                        'user' 
                    ]);
    }

    // Register API Test - Seller
    public function testSellerRegister() {
        $user = [
            'name' => 'Test Seller',
            'email' => 'seller@blockmatcher.com',
            'type' => 'seller',
            'password' => 'TestSeller111',
            'telegram' => '@testseller',
        ];

        $response = $this->json('POST', '/api/register', $user, [
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(200)
                    ->assertJsonStructure([
                        'success',
                        'user' 
                    ]);
    }

    // Login API Test
    public function testLogin() {
        // Register
        $user = [
            'name' => 'Test User',
            'email' => 'testuser@blockmatcher.com',
            'type' => 'buyer',
            'password' => 'TestUser111',
            'telegram' => '@testuser',
        ];

        $this->json('POST', '/api/register', $user, [
            'Accept' => 'application/json'
        ]);

        // Login
        $params = [
            'email' => 'testuser@blockmatcher.com',
            'password' => 'TestUser111',
        ];

        $response = $this->json('POST', '/api/login', $params, [
            'Accept' => 'application/json'
        ]);

        // $apiResponse = $response->baseResponse->getData();

        $response->assertStatus(200)
                    ->assertJsonStructure([
                        'success',
                        'user' 
                    ]);
    }
}
