<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Paste;
use App\Enums\ExpiryOption;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PastebinTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function it_shows_the_pastebin(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Paste')
            ->assertSee('Reset');
    }

    #[Test]
    public function users_can_create_paste_with_valid_data(): void
    {
        $this->post(route('home'), [
            'code' => 'My paste', 
            'color_scheme' => null, 
            'expiry' => ExpiryOption::NEVER->value,
            'custom_expiry' => null,
            'password' => null,
        ])->assertStatus(302);

        $this->assertDatabaseHas('pastes', ['code' => 'My paste']);
    }

    #[Test]
    public function user_can_not_create_paste_with_invalid_data(): void
    {
        $response = $this->post(route('home'), [
            'code' => '',
            'color_scheme' => 'invalid_scheme', 
            'expiry' => 'invalid_expiry',
            'custom_expiry' => 'invalid_date',
            'password' => '123', // too short
        ]);

        $response->assertSessionHasErrors(['code', 'color_scheme', 'expiry', 'custom_expiry', 'password']);
    }

    #[Test]
    public function users_can_see_pastes(): void
    {
        $paste = Paste::factory()->create();

        $this->get("/{$paste->hash}")
            ->assertSee($paste->code);
    }

    #[Test]
    public function users_can_see_raw_pastes(): void
    {
        $paste = Paste::factory()->create();

        $this->get("/{$paste->hash}/raw")
            ->assertSee($paste->code);
    }

    #[Test]
    public function users_can_see_the_fork_page(): void
    {
        $paste = Paste::factory()->create();

        $this->get("/fork/{$paste->hash}")
            ->assertSee($paste->code);
    }

    #[Test]
    public function users_can_fork_pastes(): void
    {
        $paste = Paste::factory()->create();

        $this->post("/fork/{$paste->hash}", ['code' => 'foo code'])
            ->assertStatus(302);

        $this->assertDatabaseHas('pastes', ['code' => 'foo code']);
    }

    #[Test]
    public function it_unlocks_paste_with_correct_password()
    {
        $paste = Paste::factory()->create([
            'password' => Hash::make('secret123')
        ]);

        $response = $this->post(route('pastes.unlock', $paste), [
            'password' => 'secret123'
        ]);

        $response->assertRedirect(route('show', $paste));
        $this->assertTrue(session()->has('paste_access_' . $paste->id));
        $this->assertTrue(session()->get('paste_access_' . $paste->id));
    }

    #[Test]
    public function it_fails_to_unlock_with_incorrect_password()
    {
        $paste = Paste::factory()->create([
            'password' => Hash::make('secret123')
        ]);

        $response = $this->post(route('pastes.unlock', $paste), [
            'password' => 'wrongpassword'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['password' => 'Incorrect password.']);
        $this->assertFalse(session()->has('paste_access_' . $paste->id));
    }
    
}
