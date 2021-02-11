<?php

namespace Tests\Feature;

use App\Models\Consumer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ConsumerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function connectWithoutToken()
    {
        $response = $this->get('/api/iamauthorized')
            ->assertStatus(400);

        $this->assertEquals(1, $response->json()['error']);
        $this->assertEquals('Empty token', $response->json()['message']);
    }

    /**
     * @test
     */
    public function connectWithBadToken()
    {
        $response = $this->get('/api/iamauthorized', [
            'Authorization' => 'Bearer JXXS0GY-K6EQ7SA-KBSTRKA-5TXWFTP'
        ])
            ->assertStatus(401);

        $this->assertEquals(2, $response->json()['error']);
        $this->assertEquals('Bad token', $response->json()['message']);
    }

    /**
     * @test
     */
    public function connectWithGoodTokenAndBadIp()
    {
        $consumer = Consumer::create([
            'name' => 'Test',
            'ip' => json_encode([
                '0.0.0.0'
            ]),
            'token' => Str::random(32)
        ]);

        $response = $this->get('/api/iamauthorized', [
            'Authorization' => 'Bearer ' . $consumer->token
        ])
            ->assertStatus(401);

        $this->assertEquals(3, $response->json()['error']);
        $this->assertEquals('Ip unauthorized', $response->json()['message']);
    }

    /**
     * @test
     */
    public function connectWithGoodTokenAndGoodIpAlone()
    {
        $consumer = Consumer::create([
            'name' => 'Test',
            'ip' => json_encode([
                '127.0.0.1'
            ]),
            'token' => Str::random(32)
        ]);

        $response = $this->get('/api/iamauthorized', [
            'Authorization' => 'Bearer ' . $consumer->token
        ])
            ->assertSuccessful();

        $this->assertEquals('It\'s good, you can pass bro !', $response->json());
    }

    /**
     * @test
     */
    public function connectWithGoodTokenAndGoodIpInList()
    {
        $consumer = Consumer::create([
            'name' => 'Test',
            'ip' => json_encode([
                '0.0.0.0',
                '1.1.1.1',
                '127.0.0.1',
                '5.2.2.3'
            ]),
            'token' => Str::random(32)
        ]);

        $response = $this->get('/api/iamauthorized', [
            'Authorization' => 'Bearer ' . $consumer->token
        ])
            ->assertSuccessful();

        $this->assertEquals('It\'s good, you can pass bro !', $response->json());
    }

    /**
     * @test
     */
    public function connectWithGoodTokenAndGoodIpWilcard()
    {
        $consumer = Consumer::create([
            'name' => 'Test',
            'ip' => json_encode([
                '*'
            ]),
            'token' => Str::random(32)
        ]);

        $response = $this->get('/api/iamauthorized', [
            'Authorization' => 'Bearer ' . $consumer->token
        ])
            ->assertSuccessful();

        $this->assertEquals('It\'s good, you can pass bro !', $response->json());
    }
}
