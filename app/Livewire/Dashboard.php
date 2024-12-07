<?php

namespace App\Livewire;

use Livewire\Attributes\Title;

#[Title('Dashboard')]
class Dashboard extends AdminComponent
{
    public function render()
    {
        return view('livewire.dashboard');
    }
}
