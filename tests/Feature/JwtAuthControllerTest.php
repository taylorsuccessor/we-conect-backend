<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Support\Facades\DB;

class JwtAuthControllerTest extends TestCase
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
    public function itGetToken(): void
    {
        $response = $this->postJson(
            route(
                'jwt-auth.get-token', [
                'email' => 'admin@gmail.com',
                'password' => 'admin'
                ]
            )
        );

        $response->assertSuccessful();


        $token = $response->json('token');

        $response = $this->withHeader('Authorization', 'Bearer not_correct_token')
            ->getJson(route('jwt-articles.index'));

        $response->assertUnauthorized();

        DB::enableQueryLog();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson(route('jwt-articles.index'));

        $queries = DB::getQueryLog();
        \Log::info($queries);

        $response->assertStatus(200);

        $response = $this->postJson(
            route(
                'jwt-auth.get-token', [
                'email' => 'wrong_email@gmail.com',
                'password' => 'wrong_password'
                ]
            )
        );

        $response->assertStatus(401);

        $this->assertEquals('incorrect_access_data', $response->json('message'));
    }
}
