<?php

namespace Tests\Feature\General;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_app_layout_renders_with_seo_and_a11y_enhancements()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard'); // assuming dashboard uses app layout

        $response->assertStatus(200);

        // Assert dynamic SEO exists
        $response->assertSee('<meta name="title"', false);
        $response->assertSee('<meta name="description"', false);

        // Assert skip-to-content link exists for A11y
        $response->assertSee('Skip to main content');
        $response->assertSee('id="main-content"', false);

        // Assert aria-hidden applied to SVGs
        $response->assertSee('aria-hidden="true"', false);

        // Assert x-cloak used
        $response->assertSee('x-cloak', false);
    }
}
