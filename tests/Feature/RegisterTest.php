<?php

namespace Tests\Feature;

use App\Constants\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private $baseUrl = 'api/auth/register';

    private $data = [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@test.com',
        'password' => 'Ec1234sasa@#',
        'password_confirmation' => 'Ec1234sasa@#',
        'role' => 'user',
    ];

    public function testCreateNewUser(): void
    {
        $response = $this->postJson($this->baseUrl, $this->data);
        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'user' => ['id']]);
    }

    function testInvalidEmail()
    {
        $data = [
            ...$this->data,
            'email' => 'invalidEmail',
        ];
        $response = $this->postJson($this->baseUrl, $data);
        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['email']]);
    }

    function testInvalidPassword()
    {
        $data = [...$this->data, 'password' => '1'];
        $response = $this->postJson($this->baseUrl, $data);
        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['password']]);
    }

    function testPasswordConfirmMismatch()
    {
        $data = [...$this->data, 'password_confirmation' => 'testpass'];
        $response = $this->postJson($this->baseUrl, $data);
        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => ['password']]);

    }

}
