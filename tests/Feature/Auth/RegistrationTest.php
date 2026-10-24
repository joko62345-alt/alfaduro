<?php

namespace Tests\Feature\Auth;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $this->freezeTime();

        Mail::fake();
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'test@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertModelExists($user);
        $this->assertNull($user->email_verified_at);
        $response->assertRedirect(route('verification.notice'));
        $expectedDelay = now()->addSeconds(5);
        Mail::assertQueued(WelcomeMail::class, function (WelcomeMail $mail) use ($expectedDelay): bool {
            $mail->assertSeeInHtml('Test User');
            $mail->assertSeeInHtml('Terima kasih telah bergabung');
            $mail->assertSeeInHtml('Lihat Dashboard');
            $mail->assertSeeInText('Terima kasih telah bergabung');

            return $mail->hasTo('test@example.com')
                && $mail->delay->equalTo($expectedDelay);
        });
        Notification::assertSentTo($user, VerifyEmail::class);

        $this->get(route('verification.notice'))->assertOk();
        Mail::assertQueued(WelcomeMail::class, 1);
    }

    public function test_registration_succeeds_when_the_welcome_email_fails(): void
    {
        Notification::fake();
        Mail::shouldReceive('to')
            ->once()
            ->with('test@example.com')
            ->andReturnSelf();
        Mail::shouldReceive('queue')
            ->once()
            ->with(\Mockery::type(WelcomeMail::class))
            ->andThrow(new \RuntimeException('SMTP unavailable.'));

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'test@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertModelExists($user);
        $response->assertRedirect(route('verification.notice'))
            ->assertSessionHas('welcome_email_status', 'failed');
    }
}
