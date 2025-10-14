<?php

namespace App\Livewire;

use Livewire\Component;

class TestComponent extends Component
{
    public $message = 'Hallo Welt';
    public function render()
    {
        return view('livewire.test-component');
    }
}
