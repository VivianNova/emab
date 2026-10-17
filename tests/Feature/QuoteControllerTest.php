<?php

namespace Tests\Feature;

use Tests\TestCase;

class QuoteControllerTest extends TestCase
{
    public function test_invalid_quote_submission_redirects_with_validation_errors(): void
    {
        $response = $this->post('/quote');

        $response->assertRedirectBackWithErrors([
            'name',
            'phone',
            'location',
            'description',
            'privacy_consent',
        ]);
    }
}
