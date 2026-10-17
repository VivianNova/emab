<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_carousel_and_shared_layout(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Reliable electrical services for every space');
        $response->assertSee(asset('images/hero.jpg'));
    }
}
