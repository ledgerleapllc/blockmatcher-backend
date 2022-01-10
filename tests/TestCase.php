<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;

use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseMigrations;
    
    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        Artisan::call('passport:install');

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->registerPermissions();
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }

    public function getToken() {
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

        $apiResponse = $response->baseResponse->getData();
        $token = $apiResponse->user->accessTokenAPI;

        return $token;
    }
}
