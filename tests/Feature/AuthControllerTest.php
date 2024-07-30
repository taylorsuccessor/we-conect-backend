<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use App\Models\Store\Book;
use App\Models\Store\Store;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RolePermissionSeeder;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setup(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);
    }

    #[Test]
    public function itLogin(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson(
            route(
                'auth.login', [
                'email' => 'admin@gmail.com',
                'password' => 'admin',
                ]
            )
        );

        $response->assertRedirect('/');

        $response = $this->getJson(
            route(
                'auth.login', [
                'email' => 'wrong_email@gmail.com',
                'password' => 'wrong_password',
                ]
            )
        );
        $response->assertRedirect('/login');
    }

    #[Test]
    public function itGetToken()
    {
        $response = $this->postJson(
            route(
                'auth.get-token', [
                'email' => 'admin@gmail.com',
                'password' => 'admin',
                'device_name' => 'android'
                ]
            )
        );

        $response->assertSuccessful();


        $token = $response->json("token");

        $response = $this->withHeader('Authorization', 'Bearer not_correct_token')
            ->getJson(route('articles.index'));

        $response->assertUnauthorized();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson(route('articles.index'));

        $response->assertStatus(200);

        $response = $this->postJson(
            route(
                'auth.get-token', [
                'email' => 'wrong_email@gmail.com',
                'password' => 'wrong_password',
                'device_name' => 'android'
                ]
            )
        );

        $response->assertStatus(401);

        $this->assertEquals('incorrect_access_data', $response->json('message'));

    }

}
