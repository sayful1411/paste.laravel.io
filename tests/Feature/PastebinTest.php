<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Paste;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PastebinTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_the_pastebin(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Paste')
            ->assertSee('Reset');
    }

    /** @test */
    public function users_can_create_pastes(): void
    {
        $this->post('/', ['code' => 'My paste'])->assertStatus(302);

        $this->assertDatabaseHas('pastes', ['code' => 'My paste']);
    }

    /** @test */
    public function users_can_see_pastes(): void
    {
        $paste = Paste::factory()->create();

        $this->get("/{$paste->hash}")
            ->assertSee($paste->code);
    }

    /** @test */
    public function users_can_see_raw_pastes(): void
    {
        $paste = Paste::factory()->create();

        $this->get("/{$paste->hash}/raw")
            ->assertSee($paste->code);
    }

    /** @test */
    public function users_can_see_the_fork_page(): void
    {
        $paste = Paste::factory()->create();

        $this->get("/fork/{$paste->hash}")
            ->assertSee($paste->code);
    }

    /** @test */
    public function users_can_fork_pastes(): void
    {
        $paste = Paste::factory()->create();

        $this->post("/fork/{$paste->hash}", ['code' => 'foo code'])
            ->assertStatus(302);

        $this->assertDatabaseHas('pastes', ['code' => 'foo code']);
    }

    /** @test */
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

    /** @test */
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

    /** @test */
    public function it_sets_expiry_for_1_hour()
    {
        $this->post('/', [
            'code' => 'Test code',
            'expiry' => '1_hour'
        ]);

        $paste = Paste::first();
        $expectedExpiry = now()->addHour();
        
        $this->assertNotNull($paste->expires_at);
        $this->assertEqualsWithDelta($expectedExpiry, $paste->expires_at, 5);
    }

    /** @test */
    public function it_sets_expiry_for_1_day()
    {
        $this->post('/', [
            'code' => 'Test code',
            'expiry' => '1_day'
        ]);

        $paste = Paste::first();
        $expectedExpiry = now()->addDay();
        
        $this->assertNotNull($paste->expires_at);
        $this->assertEqualsWithDelta($expectedExpiry, $paste->expires_at, 5);
    }

    /** @test */
    public function it_sets_color_scheme()
    {
        $colorScheme = 'dark';
        
        $this->post('/', [
            'code' => 'Test code',
            'expiry' => 'never',
            'color_scheme' => $colorScheme
        ]);

        $paste = Paste::first();
        
        $this->assertEquals($colorScheme, $paste->color_scheme);
    }

    /** @test */
    public function it_hashes_password_when_provided()
    {
        $password = 'secret123';
        
        $this->post('/', [
            'code' => 'Test code',
            'expiry' => 'never',
            'password' => $password
        ]);

        $paste = Paste::first();
        
        $this->assertNotNull($paste->password);
        $this->assertTrue(Hash::check($password, $paste->password));
    }

    
}
