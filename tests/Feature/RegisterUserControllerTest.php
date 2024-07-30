<?php

namespace Tests\Feature;

use App\Jobs\UserCreatedJob;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RolePermissionSeeder;

class RegisterUserControllerTest extends TestCase
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
    public function itRegisterUser(): void
    {
        $response = $this->postJson(
            route(
                'auth.register', [
                    'name' => 'Hashim',
                    'email' => 'hashim@gmail.com',
                    'password' => '@-password-19',
                    'password_confirmation' => '@-password-19',
                ]
            )
        );

        $response->assertSuccessful();

        $message = $response->json('message');
        $this->assertEquals($message, "register_successfully");
    }

    #[Test]
    public function itValidateRegisterUser(): void
    {
        $response = $this->postJson(
            route(
                'auth.register', [
                    'name' => '22',
                    'email' => 'not_email_string',
                    'password' => '@-password-19',
                    'password_confirmation' => '@-password-19',
                ]
            )
        );

        $response->assertStatus(422);
        $this->assertArrayHasKey('email', $response->json('errors'));
    }

    #[Test]
    public function itDispatchJob(): void
    {

        Bus::fake();

        $response = $this->postJson(
            route(
                'auth.register', [
                    'name' => 'Hashim',
                    'email' => 'hashim@gmail.com',
                    'password' => '@-password-19',
                    'password_confirmation' => '@-password-19',
                ]
            )
        );

        $response->assertSuccessful();
        $user = User::where('email', 'hashim@gmail.com')->first();
        Bus::assertDispatched(
            UserCreatedJob::class, function ($job) use ($user) {
                $this->assertEquals($job->data->email, $user->email);
                return $job->data->email === $user->email;
            }
        );
    }

    #[Test]
    public function itExecuteDispatchedJob()
    {
        Queue::fake();

        $data = 'test data this should be user data';

        UserCreatedJob::dispatch($data);

        Queue::assertPushed(
            UserCreatedJob::class, function ($job) use ($data) {
                return $job->data === $data;
            }
        );

        $job = new UserCreatedJob($data);
        $job->handle();

        Log::shouldReceive('info')
            ->once()
            ->with('New User Data:', ['data' => $data]);
collect([1, 2, 3])->each(function ($i) { echo $i;   });
        $job->handle();
    }
}
