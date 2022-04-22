<?php

namespace App\Http\Livewire;

use App\Models\Todo;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TodoList extends Component
{
    use AuthorizesRequests;

    public Collection $todos;
    public string $filter = "*";

    /** @var array|string[] $allowedFilterValues */
    // This is made to prevent front-end manipulation and, potential, SQL Injection attempts.
    // Also, it's always better to be prepared than sorry, no?
    protected array $allowedFilterValues = [
        '0', '1', '*'
    ];

    protected $listeners = [
        'refresh-todo-list' => 'render'
    ];


    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function deleteTodo(Todo $todo)
    {
        $this->authorize('todo-belongs-to-user', $todo);

        $todo->delete();
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function toggleTodo(Todo $todo)
    {
        $this->authorize('todo-belongs-to-user', $todo);

        $todo->toggleCompletion();
    }

    public function filterTodos()
    {
        abort_if(
            !in_array($this->filter, $this->allowedFilterValues),
            403,
            "Invalid Filter!"
        );

        $this->todos = Auth::user()
            ->todos()
            ->when(
                $this->filter !== '*',
                fn ($query) => $query->where('completed', $this->filter)
            )
            ->latest()
            ->get();
    }

    public function render()
    {
        $this->filterTodos();

        return view('livewire.todo-list');
    }
}
