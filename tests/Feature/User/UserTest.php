<?php

namespace Tests\Feature\User;

use App\Models\Company\Company;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /*
     * ----- Route GET /user -----
     */
    /**
     * @test
     */
    public function getAllUserWithoutUserInDb()
    {
        $response = $this->getWithAuth('/api/user')
            ->assertSuccessful();

        $this->assertEmpty($response->json());
    }

    /**
     * @test
     */
    public function getAllUserWithOneUserInDb()
    {
        $user = $this->setUser();

        $response = $this->getWithAuth('/api/user')
            ->assertSuccessful();

        $this->assertCount(1, $response->json());

        $this->checkUser($user, $response->json()[0]);
    }

    /**
     * @test
     */
    public function getAllUserWithMoreUserInDb()
    {
        $userA = $this->setUser();
        $userB = $this->setUser([
            'companyId' => 2,
            'email' => 'johan@fidensio.com',
            'password' => 'passB',
            'mobile' => '695931695',
            'data' => [
                'firstName' => 'Johan',
                'lastName' => 'Melab',
                'dateOfBirth' => '1997-10-02'
            ]
        ]);
        $userC = $this->setUser([
            'companyId' => 3,
            'email' => 'ghislain@fidensio.com',
            'password' => 'passC',
            'mobile' => '695931696',
            'emailValidated' => 'Not Validated',
            'data' => [
                'firstName' => 'Ghislain',
                'lastName' => 'Michaud',
                'dateOfBirth' => '1997-10-02'
            ]
        ]);

        $response = $this->getWithAuth('/api/user')
            ->assertSuccessful();

        $this->checkUser($userA, $response->json()[0]);
        $this->checkUser($userB, $response->json()[1]);
        $this->checkUser($userC, $response->json()[2]);

        $this->assertCount(3, $response->json());
    }

    /*
     * ----- Route GET /user/:id -----
     */
    /**
     * @test
     */
    public function getUserWithBadId()
    {
        $response = $this->getWithAuth('/api/user/999')
            ->assertStatus(400);

        $this->assertEquals(1, $response->json()['error']);
        $this->assertEquals('Bad id or user not found', $response->json()['message']);
    }

    /**
     * @test
     */
    public function getUserWithOneUserInDb()
    {
        $user = $this->setUser();

        $response = $this->getWithAuth('/api/user/' . $user['id'])
            ->assertSuccessful();

        $this->checkUser($user, $response->json());
    }

    /**
     * @test
     */
    public function getUserWithMoreUserInDb()
    {
        $user = $this->setUser();
        $this->setUser([
            'email' => 'johan@fidensio.com',
            'mobile' => '695931690',
            'data' => [
                'firstName' => 'Johan',
                'lastName' => 'Melab',
                'dateOfBirth' => '1997-10-05',
                'gender' => 1,
            ]
        ]);
        $this->setUser([
            'email' => 'ghislain@fidensio.com',
            'mobile' => '695931698',
            'data' => [
                'firstName' => 'Ghislain',
                'lastName' => 'Michaud',
                'dateOfBirth' => '1997-10-08',
                'gender' => 2,
            ]
        ]);

        $response = $this->getWithAuth('/api/user/' . $user['id'])
            ->assertSuccessful();

        $this->checkUser($user, $response->json());
    }

    /*
     * ----- Route POST /user
     */
    /**
     * @test
     */
    public function createUserWithoutParams()
    {
        $response = $this->postWithAuth('/api/user')
            ->assertStatus(400);

        $this->assertEquals(2, $response->json()['error']);

        $this->assertEquals('The company id field is required.', $response->json()['message']['companyId'][0]);
        $this->assertEquals('The email field is required.', $response->json()['message']['email'][0]);
        $this->assertEquals('The indic mobile field is required.', $response->json()['message']['indicMobile'][0]);
        $this->assertEquals('The mobile field is required.', $response->json()['message']['mobile'][0]);
        $this->assertEquals('The data.first name field is required.', $response->json()['message']['data.firstName'][0]);
        $this->assertEquals('The data.last name field is required.', $response->json()['message']['data.lastName'][0]);
        $this->assertEquals('The data.date of birth field is required.', $response->json()['message']['data.dateOfBirth'][0]);
        $this->assertEquals('The data.gender field is required.', $response->json()['message']['data.gender'][0]);
    }

    /**
     * @test
     */
    public function createUserWithBadGender()
    {
        $response = $this->postWithAuth('/api/user', [
            'companyId' => 1,
            'email' => 'benjamin@fidensio.com',
            'password' => 'pass',
            'indicMobile' => '33',
            'mobile' => '695931694',
            'emailValidated' => null,
            'emailValidatedExp' => Carbon::now(),
            'resetPassword' => null,
            'resetPasswordExp' => Carbon::now(),
            'data' => [
                'firstName' => 'Benjamin',
                'lastName' => 'Velluet',
                'dateOfBirth' => '1997-10-02',
                'gender' => 3,
            ]
        ])
            ->assertStatus(400);

        $this->assertEquals(2, $response->json()['error']);
        $this->assertEquals('The data.gender may not be greater than 2.', $response->json()['message']['data.gender'][0]);
    }

    /**
     * @test
     */
    public function createUserWithBadEmail()
    {
        $response = $this->postWithAuth('/api/user', [
            'companyId' => 1,
            'email' => 'benjaminfidensio.com',
            'password' => 'pass',
            'indicMobile' => '33',
            'mobile' => '695931694',
            'emailValidated' => null,
            'emailValidatedExp' => Carbon::now(),
            'resetPassword' => null,
            'resetPasswordExp' => Carbon::now(),
            'data' => [
                'firstName' => 'Benjamin',
                'lastName' => 'Velluet',
                'dateOfBirth' => '1997-10-02',
                'gender' => 2,
            ]
        ])
            ->assertStatus(400);

        $this->assertEquals(2, $response->json()['error']);
        $this->assertEquals('The email must be a valid email address.', $response->json()['message']['email'][0]);
    }

    /**
     * @test
     */
    public function createUserWithBadIndicMobile()
    {
        $response = $this->postWithAuth('/api/user', [
            'companyId' => 1,
            'email' => 'benjamin@fidensio.com',
            'password' => 'pass',
            'indicMobile' => 'ab',
            'mobile' => '695931694',
            'emailValidated' => null,
            'emailValidatedExp' => Carbon::now(),
            'resetPassword' => null,
            'resetPasswordExp' => Carbon::now(),
            'data' => [
                'firstName' => 'Benjamin',
                'lastName' => 'Velluet',
                'dateOfBirth' => '1997-10-02',
                'gender' => 2,
            ]
        ])
            ->assertStatus(400);

        $this->assertEquals(2, $response->json()['error']);
        $this->assertEquals('The indic mobile must be a number.', $response->json()['message']['indicMobile'][0]);
    }

    /**
     * @test
     */
    public function createUserWithBadMobile()
    {
        $response = $this->postWithAuth('/api/user', [
            'companyId' => 1,
            'email' => 'benjamin@fidensio.com',
            'password' => 'pass',
            'indicMobile' => '33',
            'mobile' => 'abcdef',
            'emailValidated' => null,
            'emailValidatedExp' => Carbon::now(),
            'resetPassword' => null,
            'resetPasswordExp' => Carbon::now(),
            'data' => [
                'firstName' => 'Benjamin',
                'lastName' => 'Velluet',
                'dateOfBirth' => '1997-10-02',
                'gender' => 2,
            ]
        ])
            ->assertStatus(400);

        $this->assertEquals(2, $response->json()['error']);
        $this->assertEquals('The mobile must be a number.', $response->json()['message']['mobile'][0]);
    }

    /**
     * @test
     */
    public function createUserWithBadEmailValidatedExp()
    {
        $response = $this->postWithAuth('/api/user', [
            'companyId' => 1,
            'email' => 'benjamin@fidensio.com',
            'password' => 'pass',
            'indicMobile' => '33',
            'mobile' => '695931694',
            'emailValidated' => null,
            'emailValidatedExp' => 'testError',
            'resetPassword' => null,
            'resetPasswordExp' => Carbon::now(),
            'data' => [
                'firstName' => 'Benjamin',
                'lastName' => 'Velluet',
                'dateOfBirth' => '1997-10-02',
                'gender' => 2,
            ]
        ])
            ->assertStatus(400);

        $this->assertEquals(2, $response->json()['error']);
        $this->assertEquals('The email validated exp is not a valid date.', $response->json()['message']['emailValidatedExp'][0]);
    }

    /**
     * @test
     */
    public function createUserWithBadResetPasswordExp()
    {
        $response = $this->postWithAuth('/api/user', [
            'companyId' => 1,
            'email' => 'benjamin@fidensio.com',
            'password' => 'pass',
            'indicMobile' => '33',
            'mobile' => '695931694',
            'emailValidated' => null,
            'emailValidatedExp' => Carbon::now(),
            'resetPassword' => null,
            'resetPasswordExp' => 'testError',
            'data' => [
                'firstName' => 'Benjamin',
                'lastName' => 'Velluet',
                'dateOfBirth' => '1997-10-02',
                'gender' => 2,
            ]
        ])
            ->assertStatus(400);

        $this->assertEquals(2, $response->json()['error']);
        $this->assertEquals('The reset password exp is not a valid date.', $response->json()['message']['resetPasswordExp'][0]);
    }

    /**
     * @test
     */
    public function createUserWithBadDateOfBirth()
    {
        $response = $this->postWithAuth('/api/user', [
            'companyId' => 1,
            'email' => 'benjamin@fidensio.com',
            'password' => 'pass',
            'indicMobile' => '33',
            'mobile' => '695931694',
            'emailValidated' => null,
            'emailValidatedExp' => Carbon::now(),
            'resetPassword' => null,
            'resetPasswordExp' => Carbon::now(),
            'data' => [
                'firstName' => 'Benjamin',
                'lastName' => 'Velluet',
                'dateOfBirth' => 'testError',
                'gender' => 2,
            ]
        ])
            ->assertStatus(400);

        $this->assertEquals(2, $response->json()['error']);
        $this->assertEquals('The data.date of birth is not a valid date.', $response->json()['message']['data.dateOfBirth'][0]);
    }

    /**
     * @test
     */
    public function createUserAlreadyExistEmail()
    {
        $this->postWithAuth('/api/user', [
            'companyId' => 1,
            'email' => 'benjamin@fidensio.com',
            'password' => 'pass',
            'indicMobile' => '33',
            'mobile' => '695931694',
            'emailValidated' => null,
            'emailValidatedExp' => Carbon::now(),
            'resetPassword' => null,
            'resetPasswordExp' => Carbon::now(),
            'data' => [
                'firstName' => 'Benjamin',
                'lastName' => 'Velluet',
                'dateOfBirth' => '1997-10-02',
                'gender' => 1,
            ]
        ])
            ->assertSuccessful();

        $response = $this->postWithAuth('/api/user', [
            'companyId' => 1,
            'email' => 'benjamin@fidensio.com',
            'password' => 'password',
            'indicMobile' => '33',
            'mobile' => '695931695',
            'emailValidated' => null,
            'emailValidatedExp' => Carbon::now(),
            'resetPassword' => null,
            'resetPasswordExp' => Carbon::now(),
            'data' => [
                'firstName' => 'Johan',
                'lastName' => 'Melab',
                'dateOfBirth' => '1997-10-05',
                'gender' => 2,
            ]
        ])
            ->assertStatus(400);

        $this->assertEquals(2, $response->json()['error']);
        $this->assertEquals('The email has already been taken.', $response->json()['message']['email'][0]);
    }

    /**
     * @test
     */
    public function createUserAlreadyExistMobile()
    {
        $this->postWithAuth('/api/user', [
            'companyId' => 1,
            'email' => 'benjamin@fidensio.com',
            'password' => 'pass',
            'indicMobile' => '33',
            'mobile' => '695931694',
            'emailValidated' => null,
            'emailValidatedExp' => Carbon::now(),
            'resetPassword' => null,
            'resetPasswordExp' => Carbon::now(),
            'data' => [
                'firstName' => 'Benjamin',
                'lastName' => 'Velluet',
                'dateOfBirth' => '1997-10-02',
                'gender' => 1,
            ]
        ])
            ->assertSuccessful();

        $response = $this->postWithAuth('/api/user', [
            'companyId' => 1,
            'email' => 'johan@fidensio.com',
            'password' => 'password',
            'indicMobile' => '33',
            'mobile' => '695931694',
            'emailValidated' => null,
            'emailValidatedExp' => Carbon::now(),
            'resetPassword' => null,
            'resetPasswordExp' => Carbon::now(),
            'data' => [
                'firstName' => 'Johan',
                'lastName' => 'Melab',
                'dateOfBirth' => '1997-10-05',
                'gender' => 2,
            ]
        ])
            ->assertStatus(400);

        $this->assertEquals(2, $response->json()['error']);
        $this->assertEquals('The mobile has already been taken.', $response->json()['message']['mobile'][0]);
    }

    /**
     * @test
     */
    public function createUser()
    {
        $userData = [
            'companyId' => 1,
            'email' => 'benjamin@fidensio.com',
            'password' => 'pass',
            'indicMobile' => '33',
            'mobile' => '695931694',
            'emailValidated' => null,
            'emailValidatedExp' => Carbon::now(),
            'resetPassword' => null,
            'resetPasswordExp' => Carbon::now(),
            'data' => [
                'firstName' => 'Benjamin',
                'lastName' => 'Velluet',
                'dateOfBirth' => '1997-10-02',
                'gender' => 1,
            ]
        ];

        $response = $this->postWithAuth('/api/user', $userData)
            ->assertSuccessful();

        $this->assertEquals($userData['companyId'], $response->json()['companyId']);
        $this->assertEquals($userData['email'], $response->json()['email']);
        $this->assertEquals($userData['password'], $response->json()['password']);
        $this->assertEquals($userData['indicMobile'], $response->json()['indicMobile']);
        $this->assertEquals($userData['mobile'], $response->json()['mobile']);
        $this->assertNull($userData['emailValidated']);
        $this->assertEquals($userData['emailValidatedExp']->toISOString(), $response->json()['emailValidatedExp']);
        $this->assertNull($userData['resetPassword']);
        $this->assertEquals($userData['resetPasswordExp']->toISOString(), $response->json()['resetPasswordExp']);
        $this->assertEquals($userData['data']['firstName'], $response->json()['data']['firstName']);
        $this->assertEquals($userData['data']['lastName'], $response->json()['data']['lastName']);
        $this->assertEquals($userData['data']['dateOfBirth'], $response->json()['data']['dateOfBirth']);
        $this->assertEquals($userData['data']['gender'], $response->json()['data']['gender']);

        $this->getWithAuth('/api/user')
            ->assertSuccessful()
            ->assertJsonCount(1);
    }

    /*
     * ----- Route PUT /user/:id
     */
    /**
     * @test
     */
    public function updateUserWithBadId()
    {
        $this->setUser();

        $response = $this->putWithAuth('/api/user/999', [
            'firstName' => 'Miguel'
        ])
            ->assertStatus(400);

        $this->assertEquals(1, $response->json()['error']);
        $this->assertEquals('Bad id or user not found', $response->json()['message']);
    }

    /**
     * @test
     */
    public function updateUserWithoutParams()
    {
        $user = $this->setUser();

        $response = $this->putWithAuth('/api/user/' . $user['id'])
            ->assertSuccessful();

        $this->checkUser($user, $response->json());
    }

    /**
     * @test
     */
    public function updateUserWithBadParams()
    {
        $user = $this->setUser();

        $response = $this->putWithAuth('/api/user/' . $user['id'], [
            'email' => 'benjaminfidensio.com'
        ])
            ->assertStatus(400);

        $this->assertEquals(2, $response->json()['error']);
        $this->assertEquals('The email must be a valid email address.', $response->json()['message']['email'][0]);
    }

    /**
     * @test
     */
    public function updateUser()
    {
        $user = $this->setUser();

        $response = $this->putWithAuth('/api/user/' . $user['id'], [
            'email' => 'johan@fidensio.com',
        ])
            ->assertSuccessful();

        $this->assertEquals('johan@fidensio.com', $response->json()['email']);

        $user['email'] = 'johan@fidensio.com';
        $this->checkUser($user, $response->json());
    }
}
