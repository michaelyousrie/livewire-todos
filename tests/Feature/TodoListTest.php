<?php

namespace Tests\Feature;

use Tests\TestCase;
use Livewire\Livewire;
use App\Http\Livewire\TodoList;
use App\Http\Livewire\CreateTodo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function todos_list_component_exists()
    {
        $this->makeUserAndLogin();

        $this->get('/dashboard')
            ->assertSeeLivewire('todo-list');
    }

    /** @test */
    public function it_refreshes_correctly_when_a_new_todo_is_created()
    {
        $this->makeUserAndLogin();

        Livewire::test(CreateTodo::class)
            ->set('todoBody', "My Body")
            ->set('todoTitle', "My Title")
            ->call('createTodo')
            ->assertEmitted("refresh-todo-list");

        Livewire::test(TodoList::class)
            ->assertSee(["My Body", "My Title"]);
    }

    /** @test */
    public function it_lists_todos_correctly()
    {
        $user = $this->makeUserAndLogin();

        $todo = $this->makeTodo(['user_id' => $user->id]);
        $secondTodo = $this->makeTodo(['body' => 'test', 'user_id' => $user->id]);

        $this->get('/dashboard')
            ->assertSee([$todo->title, $todo->body])
            ->assertSee([$secondTodo->title, $secondTodo->body]);
    }

    /** @test */
    public function it_only_shows_todos_for_the_currently_logged_in_user()
    {
        // create an entry that belongs to a random user.
        // then make another one because you can't be sure enough.
        $randomTodo = $this->makeTodo();
        $anotherRandomTodo = $this->makeTodo();

        // create a test user and login as their account then create an entry for that user.
        $userTodo = $this->makeTodo(['user_id' => $this->makeUserAndLogin()->id]);

        // make sure we only see the second entry which belongs to our logged-in user...
        // and that we can't see the first entry that belongs to a random user.
        $this->get('/dashboard')
            ->assertSee([$userTodo->title, $userTodo->body])
            ->assertDontSee([$randomTodo->title, $randomTodo->body])
            ->assertDontSee([$anotherRandomTodo->title, $anotherRandomTodo->body]);
    }

    /** @test */
    public function it_can_toggle_todo_completed_status_correctly()
    {
        $user = $this->makeUserAndLogin();
        $todo = $this->makeTodo(['user_id' => $user->id]);

        $this->assertDatabaseHas('todos', ['id' => $todo->id, 'user_id' => $user->id, 'completed' => false]);

        Livewire::test(TodoList::class)
            ->call('toggleTodo', $todo)
            ->assertSuccessful();

        $this->assertDatabaseHas('todos', ['id' => $todo->id, 'completed' => true]);
    }

    /** @test */
    public function it_deletes_todos_correctly()
    {
        $user = $this->makeUserAndLogin();
        $todo = $this->makeTodo(['user_id' => $user->id]);

        $this->assertDatabaseHas('todos', ['id' => $todo->id]);

        Livewire::test(TodoList::class)
            ->call('deleteTodo', $todo)
            ->assertSuccessful();

        $this->assertSoftDeleted('todos', ['id' => $todo->id]);
    }

    /** @test */
    public function users_cant_delete_todos_that_dont_belong_to_them()
    {
        $todo = $this->makeTodo();
        $anotherTodo = $this->makeTodo();

        $this->assertDatabaseHas('todos', ['id' => $todo->id]);
        $this->assertDatabaseHas('todos', ['id' => $anotherTodo->id]);

        $this->makeUserAndLogin();
        Livewire::test(TodoList::class)
            ->call('deleteTodo', $todo)
            ->assertForbidden();

        Livewire::test(TodoList::class)
            ->call('deleteTodo', $anotherTodo)
            ->assertForbidden();

        $this->assertDatabaseHas('todos', ['id' => $todo->id]);
        $this->assertDatabaseHas('todos', ['id' => $anotherTodo->id]);
    }

    /** @test */
    public function it_filters_todos_correctly()
    {
        $user = $this->makeUserAndLogin();
        $completedTodo = $this->makeTodo(['completed' => true, 'user_id' => $user->id]);
        $incompletedTodo = $this->makeTodo(['completed' => false, 'user_id' => $user->id]);

        Livewire::test(TodoList::class)
            ->set('filter', '0')
            ->call('filterTodos')
            ->assertSee([$incompletedTodo->title, $incompletedTodo->body])
            ->assertDontSee([$completedTodo->title, $completedTodo->body]);

        Livewire::test(TodoList::class)
            ->set('filter', '1')
            ->call('filterTodos')
            ->assertSee([$completedTodo->title, $completedTodo->body])
            ->assertDontSee([$incompletedTodo->title, $incompletedTodo->body]);

        Livewire::test(TodoList::class)
            ->set('filter', '*')
            ->call('filterTodos')
            ->assertSee([$completedTodo->title, $completedTodo->body])
            ->assertSee([$incompletedTodo->title, $incompletedTodo->body]);
    }
}
