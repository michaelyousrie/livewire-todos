<?php

namespace App\Http\Livewire;

use App\Models\Todo;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CreateTodo extends Component
{
    public string $todoTitle = "";
    public string $todoBody = "";

    public function render()
    {
        return view('livewire.create-todo');
    }

    public function createTodo()
    {
        $this->validate([
            'todoTitle' => 'required|unique:todos,title',
            'todoBody' => 'required',
        ], [
            'todoTitle.unique' => 'You already have a Todo with the same title!',
            'todoBody.required' => "You can't have a todo without a body!"
        ]);

        Auth::user()->todos()->save(
            Todo::query()->make([
                'title' => $this->todoTitle,
                'body'  => $this->todoBody
            ])
        );

        $this->todoTitle = "";
        $this->todoBody = "";

        $this->emit("refresh-todo-list");
    }
}
