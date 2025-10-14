<?php

namespace App\Livewire\Food;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Food;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $minCalories;
    public $maxCalories;
    public $minProtein;
    public $maxProtein;
    public $minCarbs;
    public $maxCarbs;
    public $minFat;
    public $maxFat;

    protected $queryString = [
        'search' => ['except' => ''],
        'minCalories' => ['except' => ''],
        'maxCalories' => ['except' => ''],
        'minProtein' => ['except' => ''],
        'maxProtein' => ['except' => ''],
        'minCarbs' => ['except' => ''],
        'maxCarbs' => ['except' => ''],
        'minFat' => ['except' => ''],
        'maxFat' => ['except' => ''],
    ];

    public function render()
    {
        $foods = Food::query()
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->minCalories, fn($query) => $query->where('calories', '>=', $this->minCalories))
            ->when($this->maxCalories, fn($query) => $query->where('calories', '<=', $this->maxCalories))
            ->when($this->minProtein, fn($query) => $query->where('protein', '>=', $this->minProtein))
            ->when($this->maxProtein, fn($query) => $query->where('protein', '<=', $this->maxProtein))
            ->when($this->minCarbs, fn($query) => $query->where('carbs', '>=', $this->minCarbs))
            ->when($this->maxCarbs, fn($query) => $query->where('carbs', '<=', $this->maxCarbs))
            ->when($this->minFat, fn($query) => $query->where('fat', '>=', $this->minFat))
            ->when($this->maxFat, fn($query) => $query->where('fat', '<=', $this->maxFat))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.food.index', [
            'foods' => $foods,
        ]);
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'minCalories',
            'maxCalories',
            'minProtein',
            'maxProtein',
            'minCarbs',
            'maxCarbs',
            'minFat',
            'maxFat',
        ]);
    }
}
