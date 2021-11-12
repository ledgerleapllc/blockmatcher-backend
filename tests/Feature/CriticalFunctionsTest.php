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

    public function testRegister(): void
    {
        $response = $this->post('/api/register');
        $response->assertStatus(200);
    }

    public function testMe(): void
    {
        $response = $this->get('/api/me');
        $response->assertStatus(302);
    }

    public function testSellOffers(): void
    {
        $response = $this->get('/api/admin/sell-offers');
        $response->assertStatus(302);
    }

    public function testBuyOffers(): void
    {
        $response = $this->get('/api/admin/buy-offers');
        $response->assertStatus(302);
    }

    public function testExport(): void
    {
        $response = $this->get('/api/admin/export');
        $response->assertStatus(302);
    }

    public function testBatchesList(): void
    {
        $response = $this->get('/api/admin/batches/');
        $response->assertStatus(302);
    }

    public function testBatchDetail(): void
    {
        $response = $this->get('/api/admin/batches/1');
        $response->assertStatus(302);
    }

    public function testBatchSellOffers(): void
    {
        $response = $this->get('/api/admin/batches/1/sell-offers');
        $response->assertStatus(302);
    }

    public function testBatchBuyOffers(): void
    {
        $response = $this->get('/api/admin/batches/1/buy-offers');
        $response->assertStatus(302);
    }

    public function testDetailExport(): void
    {
        $response = $this->get('/api/admin/batches/1/export');
        $response->assertStatus(302);
    }

    public function testCreateBatch(): void
    {
        $response = $this->post('/api/admin/batches/');
        $response->assertStatus(302);
    }

    public function testUpdateBatch(): void
    {
        $response = $this->put('/api/admin/batches/1');
        $response->assertStatus(302);
    }

    public function testRemoveBatch(): void
    {
        $response = $this->delete('/api/admin/batches/1');
        $response->assertStatus(302);
    }

    public function testSellerGetOffersList(): void
    {
        $response = $this->get('/api/user/sell-offers/');
        $response->assertStatus(302);
    }

    public function testSellerCreateOffer(): void
    {
        $response = $this->post('/api/user/sell-offers/');
        $response->assertStatus(302);
    }

    public function testSellerRemoveOffer(): void
    {
        $response = $this->delete()('/api/user/sell-offers/1');
        $response->assertStatus(302);
    }

    public function testBuyerGetOffersList(): void
    {
        $response = $this->get('/api/user/buy-offers/');
        $response->assertStatus(302);
    }

    public function testBuyerCreateOffer(): void
    {
        $response = $this->post('/api/user/buy-offers/');
        $response->assertStatus(302);
    }

    public function testBuyerUpdateOffer(): void
    {
        $response = $this->put()('/api/user/buy-offers/1');
        $response->assertStatus(302);
    }

    public function testBuyerRemoveOffer(): void
    {
        $response = $this->delete()('/api/user/buy-offers/1');
        $response->assertStatus(302);
    }

}
