<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    public function test_email_validation_passes_with_valid_email(): void
    {
        $data = ['email' => 'test@example.com'];
        $rules = ['email' => 'required|email'];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    public function test_email_validation_fails_with_invalid_email(): void
    {
        $data = ['email' => 'invalid-email'];
        $rules = ['email' => 'required|email'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_required_validation_fails_when_field_is_missing(): void
    {
        $data = [];
        $rules = ['name' => 'required'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
    }

    public function test_min_length_validation(): void
    {
        $data = ['password' => '123'];
        $rules = ['password' => 'required|min:8'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
    }

    public function test_max_length_validation(): void
    {
        $data = ['name' => str_repeat('a', 256)];
        $rules = ['name' => 'required|max:255'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
    }

    public function test_unique_validation_passes_with_unique_value(): void
    {
        $data = ['email' => 'unique@example.com'];
        $rules = ['email' => 'required|email|unique:users,email'];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    public function test_numeric_validation(): void
    {
        $data = ['age' => 'not-a-number'];
        $rules = ['age' => 'numeric'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
    }

    public function test_array_validation(): void
    {
        $data = ['items' => 'not-an-array'];
        $rules = ['items' => 'array'];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
    }
}
