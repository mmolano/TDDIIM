<?php

namespace Tests;

use App\Models\Consumer;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public $faker;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->faker = Factory::create('fr_FR');
    }

    /*
     * Request Http Methode
     */
    public function getWithAuth(string $url): TestResponse
    {
        $consumer = Consumer::create([
            'name' => $this->faker->name,
            'ip' => json_encode([
                '127.0.0.1'
            ]),
            'token' => Str::random(32)
        ]);

        return $this->get($url, [
            'Authorization' => 'Bearer ' . $consumer->token
        ]);
    }

    public function postWithAuth(string $url, array $params = []): TestResponse
    {
        $consumer = Consumer::create([
            'name' => $this->faker->name,
            'ip' => json_encode([
                '127.0.0.1'
            ]),
            'token' => Str::random(32)
        ]);

        return $this->post($url, $params, [
            'Authorization' => 'Bearer ' . $consumer->token
        ]);
    }

    public function putWithAuth(string $url, array $params = []): TestResponse
    {
        $consumer = Consumer::create([
            'name' => $this->faker->name,
            'ip' => json_encode([
                '127.0.0.1'
            ]),
            'token' => Str::random(32)
        ]);

        return $this->put($url, $params, [
            'Authorization' => 'Bearer ' . $consumer->token
        ]);
    }

    public function deleteWithAuth(string $url, array $params = []): TestResponse
    {
        $consumer = Consumer::create([
            'name' => $this->faker->name,
            'ip' => json_encode([
                '127.0.0.1'
            ]),
            'token' => Str::random(32)
        ]);

        return $this->delete($url, $params, [
            'Authorization' => 'Bearer ' . $consumer->token
        ]);
    }

    /*
     * User Methode
     */
    public function setUser(array $data = []): array
    {
        $data = array_replace_recursive([
            'companyId' => 1,
            'email' => $this->faker->email,
            'password' => $this->faker->password,
            'indicMobile' => '33',
            'mobile' => $this->faker->numberBetween(111111111, 999999999),
            'emailValidated' => null,
            'emailValidatedExp' => Carbon::now(),
            'resetPassword' => null,
            'resetPasswordExp' => Carbon::now(),
            'data' => [
                'firstName' => $this->faker->firstName,
                'lastName' => $this->faker->lastName,
                'dateOfBirth' => '1997-10-02',
                'gender' => $this->faker->numberBetween(0, 2),
            ]
        ], $data);

        return $this->postWithAuth('/api/user', $data)->json();
    }

    public function checkUser(array $expected, array $actual): void
    {
        $this->assertEquals($expected['id'], $actual['id']);
        $this->assertEquals($expected['email'], $actual['email']);
        $this->assertEquals($expected['companyId'], $actual['companyId']);
        $this->assertEquals($expected['password'], $actual['password']);
        $this->assertEquals($expected['indicMobile'], $actual['indicMobile']);
        $this->assertEquals($expected['mobile'], $actual['mobile']);
        $this->assertEquals($expected['emailValidated'], $actual['emailValidated']);
        $this->assertEquals(Carbon::create($expected['emailValidatedExp'])->toDateTimeString(), $actual['emailValidatedExp']);
        $this->assertEquals($expected['resetPassword'], $actual['resetPassword']);
        $this->assertEquals(Carbon::create($expected['resetPasswordExp'])->toDateTimeString(), $actual['resetPasswordExp']);
        $this->assertEquals($expected['data']['firstName'], $actual['data']['firstName']);
        $this->assertEquals($expected['data']['lastName'], $actual['data']['lastName']);
        $this->assertEquals($expected['data']['dateOfBirth'], $actual['data']['dateOfBirth']);
        $this->assertEquals($expected['data']['gender'], $actual['data']['gender']);
        $this->assertEquals($expected['data']['language'], $actual['data']['language']);
    }
}
