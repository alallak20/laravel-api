<?php

namespace App\Livewire;

use App\Models\Article;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Search extends Component
{
    #[Validate('required')]
    public $searchText = '';
    public $results = [];
    public $placeholder = "Search stuff...";


    #[On('search:clear')]
    public function clear() {
        $this->reset(['results', 'searchText']);
    }

    public function updatedSearchText($value) {
        $this->reset('results');

        $this->validate();

        $searchTerm = "%" . $value . "%";

        $this->results = Article::where('title','LIKE', $searchTerm)->get();
    }

    public function render()
    {
        return view('livewire.search');
    }
}