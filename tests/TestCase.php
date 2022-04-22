<?php

namespace Tests;

use App\Models\User;
use App\Models\Todo;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function makeTodo(array $overrides = []): Todo
    {
        return Todo::factory()->create($overrides);
    }

    protected function makeUserAndLogin(array $override = []): User
    {
        $user = User::factory()->create($override);

        $this->actingAs($user);

        return $user;
    }
}
