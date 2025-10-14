<?php

namespace App\Livewire;

use Log;
use App\Models\Food;
use App\Models\Category;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class FoodManager extends Component
{
    public $foods;
    public $categories = [];
    public $name;
    public $brand;
    public $category_id;
    public $calories;
    public $protein = 0;
    public $carbs = 0;
    public $fat = 0;
    public $fiber;
    public $serving_unit = 'g';
    public $source;
    public $foodId;
    public $isEdit = false;

    public $search = '';
    public $suggestions = [];
    public $loading = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'calories' => 'required|numeric|min:0',
        'protein' => 'nullable|numeric|min:0',
        'carbs' => 'nullable|numeric|min:0',
        'fat' => 'nullable|numeric|min:0',
        'fiber' => 'nullable|numeric|min:0',
        'serving_unit' => 'required|string|max:50',
        'source' => 'nullable|string|max:255',
        'category_id' => 'nullable|exists:categories,id',
    ];

    public function mount()
    {
        $this->categories = Category::orderBy('name')->get();
        $this->loadFoods();
    }

    public function loadFoods()
    {
        $this->foods = Food::with('category')->orderBy('name')->get();
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'brand',
            'category_id',
            'calories',
            'protein',
            'carbs',
            'fat',
            'fiber',
            'serving_unit',
            'source',
            'foodId',
            'isEdit',
            'search',
            'suggestions'
        ]);
        $this->serving_unit = 'g';
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'brand' => $this->brand,
            'category_id' => $this->category_id,
            'calories' => $this->calories,
            'protein' => $this->protein,
            'carbs' => $this->carbs,
            'fat' => $this->fat,
            'fiber' => $this->fiber,
            'serving_unit' => $this->serving_unit,
            'source' => $this->source ?? 'manual',
        ];

        if ($this->isEdit) {
            $food = Food::findOrFail($this->foodId);
            $food->update($data);
        } else {
            $data['created_by'] = Auth::id();
            Food::create($data);
        }

        $this->resetForm();
        $this->loadFoods();

        session()->flash('message', $this->isEdit ? 'Lebensmittel aktualisiert.' : 'Lebensmittel hinzugefügt.');
    }

    public function edit($id)
    {
        $food = Food::findOrFail($id);

        $this->foodId = $food->id;
        $this->name = $food->name;
        $this->brand = $food->brand;
        $this->category_id = $food->category_id;
        $this->calories = $food->calories;
        $this->protein = $food->protein;
        $this->carbs = $food->carbs;
        $this->fat = $food->fat;
        $this->fiber = $food->fiber;
        $this->serving_unit = $food->serving_unit;
        $this->source = $food->source;

        $this->isEdit = true;
    }

    public function delete($id)
    {
        Food::findOrFail($id)->delete();
        $this->loadFoods();
        session()->flash('message', 'Lebensmittel gelöscht.');
    }

    // Live-Suche Open Food Facts
    public function updatedSearch()
    {
        if (strlen($this->search) < 3) {
            $this->suggestions = [];
            return;
        }

        $this->loading = true;

        try {
            $response = Http::get('https://world.openfoodfacts.org/cgi/search.pl', [
                'search_terms' => $this->search,
                'search_simple' => 1,
                'json' => 1,
                'fields' => 'product_name,brands,nutriments,serving_size',
                'page_size' => 5,
            ]);
        } catch (\Exception $e) {
            Log::error('OF request failed: ' . $e->getMessage());
            $this->suggestions = [];
            $this->loading = false;
            return;
        }

        if ($response->ok()) {
            $products = $response->json()['products'] ?? [];
            $this->suggestions = collect($products)->map(fn($p) => [
                'name' => $p['product_name'] ?? '',
                'brand' => $p['brands'] ?? '',
                'serving_unit' => $p['serving_size'] ?? '100g',
                'calories' => $p['nutriments']['energy-kcal_100g'] ?? 0,
                'protein' => $p['nutriments']['proteins_100g'] ?? 0,
                'carbs' => $p['nutriments']['carbohydrates_100g'] ?? 0,
                'fat' => $p['nutriments']['fat_100g'] ?? 0,
                'fiber' => $p['nutriments']['fiber_100g'] ?? 0,
            ])->toArray();
        }

        $this->loading = false;
    }

    public function selectSuggestion($index)
    {
        $item = $this->suggestions[$index] ?? null;
        if (!$item) return;

        $this->suggestions = [];

        $this->name = $item['name'];
        $this->brand = $item['brand'];
        $this->category_id = null; // Optional: manuell auswählen
        $this->serving_unit = $item['serving_unit'] ?? '100g';
        $this->calories = $item['calories'] ?? 0;
        $this->protein = $item['protein'] ?? 0;
        $this->carbs = $item['carbs'] ?? 0;
        $this->fat = $item['fat'] ?? 0;
        $this->fiber = $item['fiber'] ?? 0;
        $this->source = 'openfoodfacts';

        $this->search = '';
    }

    public function render()
    {
        return view('livewire.food.index', [
            'categories' => $this->categories,
        ]);
    }
}
