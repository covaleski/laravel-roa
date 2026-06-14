<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure the map is compiled and cached when queried.
     */
    public function test_can_use_to_create_endpoints(): void
    {
        $this->get('/api/aircraft')->assertOk();
        $this->get('/api/books')->assertOk();
        $this->get('/api/flights')->assertOk();
        $this->get('/api/users')->assertOk();
        $data = [
            'isbn' => '999-9-9999-9999-9',
            'title' => 'Foobar Engineering',
        ];
        $errors = [
            'author' => 'The author field is required.',
        ];
        $this->post('/api/books', $data)->assertInvalid($errors);
    }
}
