<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Todo;
use Livewire\Livewire;
use App\Http\Livewire\CreateTodo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTodoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function livewire_create_todos_component_exists()
    {
        $this->actingAs(User::factory()->create());

        $this->get('/dashboard')
            ->assertSeeLivewire("create-todo");
    }

    /** @test */
    public function it_can_create_todos_successfully()
    {
        $user = $this->makeUserAndLogin();

        Livewire::test(CreateTodo::class)
            ->set('todoTitle', "My Title")
            ->set('todoBody', "My Body")
            ->call("createTodo")
            ->assertSuccessful();

        $this->assertDatabaseHas('todos', ['title' => 'My Title', 'body' => 'My Body', 'user_id' => $user->id]);
    }

    /** @test */
    public function it_correctly_validates_data()
    {
        $this->makeUserAndLogin();

        // Validate that it fails when both fields are null
        Livewire::test(CreateTodo::class)
            ->set('todoTitle', " ")
            ->set('todoBody', " ")
            ->call("createTodo")
            ->assertHasErrors(['todoTitle' => 'required', 'todoBody' => 'required']);

        $this->assertDatabaseMissing('todos', ['todoTitle' => " ", 'todoBody' => " "]);

        // Validate that it fails if only the title is null
        Livewire::test(CreateTodo::class)
            ->set('todoTitle', " ")
            ->set("todoBody", "My Body")
            ->call("createTodo")
            ->assertHasErrors(['todoTitle' => 'required']);

        $this->assertDatabaseMissing('todos', ['todoTitle' => " ", "todoBody" => "My Body"]);

        // Validate that it fails if only the body is null
         Livewire::test(CreateTodo::class)
            ->set('todoTitle', "My Title")
            ->set("todoBody", " ")
            ->call("createTodo")
            ->assertHasErrors(['todoBody' => 'required']);

        $this->assertDatabaseMissing('todos', ['todoTitle' => "My Title"]);
    }

    /** @test */
    public function it_fails_if_title_is_not_unique()
    {
        $this->makeUserAndLogin();

        // giving that we have an item with the title 'my title'
        Todo::factory()->create(['title' => 'my title']);

        // make sure that we can NOT make a new item with that same title.
        Livewire::test(CreateTodo::class)
            ->set('todoTitle', "my title")
            ->set("todoBody", "My Body")
            ->call("createTodo")
            ->assertHasErrors(['todoTitle' => 'unique']);
    }
}
