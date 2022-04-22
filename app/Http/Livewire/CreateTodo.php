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
            'todoTitle' => 'required',
            'todoBody' => 'required',
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
