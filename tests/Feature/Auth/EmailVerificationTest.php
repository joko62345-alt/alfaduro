<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->unverified()->create();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_unverified_users_are_redirected_from_staff_dashboards(): void
    {
        $admin = User::factory()->unverified()->create(['role' => 'admin']);
        $cashier = User::factory()->unverified()->create(['role' => 'kasir']);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('verification.notice'));

        $this->actingAs($cashier)
            ->get(route('kasir.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verified_users_can_access_staff_dashboards(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create(['role' => 'kasir']);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($cashier)->get(route('kasir.dashboard'))->assertOk();
    }

    public function test_verification_email_resends_are_rate_limited(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();
        $this->actingAs($user);

        for ($attempt = 0; $attempt < 6; $attempt++) {
            $this->post(route('verification.send'))->assertRedirect();
        }

        $this->post(route('verification.send'))->assertTooManyRequests();
        Notification::assertSentToTimes($user, VerifyEmail::class, 6);
    }
}
