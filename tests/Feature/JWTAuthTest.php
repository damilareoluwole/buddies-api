<?php

namespace Tests\Feature;

#use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JWTAuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**@var User */
    public $user;


    public function setUp(): void
    {
        parent::setUp();
        config()->set('jwt.keys.public', 'file://storage/certs/jwt-rsa-1024-public.pem');
        config()->set('jwt.keys.private', 'file://storage/certs/jwt-rsa-1024-private.pem');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_login()
    {
        $this->user = User::factory()->create();
        $this->withHeaders(['Accept' => 'application/json'])
            ->post(route('jwt-auth.login'), [
                'phone' => $this->user->phone,
                'password' => 'password'
            ])->assertOk();
    }

    public function test_user_can_access_authorized_resource_with_retrieved_token()
    {
        //test user can see profile with a valid credential
        $this->user = User::factory()->create();
        $this->withToken(auth()->fromUser($this->user))
            ->withHeader('Accept', 'application/json')
            ->get(route('user.profile'))
            ->assertOk();
    }

    public function test_signup()
    {
        $sample_response = '{
            "alias": "Tola Main",
            "accountNumber": "5000001766",
            "currency": "NGN",
            "createdOn": 1636404974795,
            "activatedOn": null,
            "authToken": null,
            "owner": {
              "productName": "PAYZONE"
            },
            "active": false
          }';

        Http::fake([
            config('readycash.wallet_url') . '/create' => Http::response(json_decode($sample_response, true), 200),
        ]);

        #Http::preventStrayRequests();

        $data = User::factory()->make();

        $this->withHeader('Accept', 'application/json')->post(route('jwt-auth.register'), [
            'firstName' => $data->firstName,
            'lastName' => $data->lastName,
            'email' => $this->faker->email(),
            'gender' => $data->gender,
            'password' => 'password',
            're_password' => 'password',
            'phone' => $data->phone,
            'dateOfBirth' => $data->dateOfBirth,
            'address' => $data->address,
            'addressCity' => $data->addressCity,
            'addressState' => $data->addressState,
            'account' => [
                'alias' => "{$data->firstName} {$data->lastName}",
                'currencyCode' => "NGN"
            ]
        ])
            ->dump()
            ->assertStatus(Response::HTTP_OK);
           
            // ->assertJsonStructure([
            //     'token'
            // ]);

        #assert a token was returned


        #assertDataBase has credentials
        $this->assertDatabaseHas('users', [
            'firstName' => $data->firstName,
        ]);
    }
}
