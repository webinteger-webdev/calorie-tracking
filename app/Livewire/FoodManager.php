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
                'fields' => 'product_name,brands,nutriments,serving_size,id',
                'page_size' => 5,
            ]);

            if ($response->failed()) {
                session()->flash('error', 'Fehler beim Abrufen der Daten von Open Food Facts.');
                $this->suggestions = [];
                $this->loading = false;
                return;
            }

            $products = $response->json()['products'] ?? [];
            $this->suggestions = collect($products)->map(function ($p) {
                return [
                    'id' => $p['id'] ?? null,
                    'name' => $p['product_name'] ?? '',
                    'brand' => $p['brands'] ?? '',
                    'serving_unit' => $p['serving_size'] ?? '100g',
                    'calories' => $p['nutriments']['energy-kcal_100g'] ?? 0,
                    'protein' => $p['nutriments']['proteins_100g'] ?? 0,
                    'carbs' => $p['nutriments']['carbohydrates_100g'] ?? 0,
                    'fat' => $p['nutriments']['fat_100g'] ?? 0,
                    'fiber' => $p['nutriments']['fiber_100g'] ?? 0,
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('OF request failed: ' . $e->getMessage());
            session()->flash('error', 'Netzwerkfehler. Bitte versuchen Sie es später erneut.');
            $this->suggestions = [];
        }

        $this->loading = false;
    }

    public function selectSuggestion($index)
    {
        $item = $this->suggestions[$index] ?? null;
        if (!$item) return;

        $openfoodfactsId = $item['id'] ?? null;
        if ($openfoodfactsId) {
            $food = Food::fetchFromOpenFoodFacts($openfoodfactsId);
            if ($food) {
                session()->flash('message', 'Produkt erfolgreich aus Open Food Facts importiert!');
                $this->loadFoods();
                $this->resetForm();
                return;
            }
        }

        session()->flash('error', 'Fehler beim Importieren des Produkts.');
    }

    public function render()
    {
        return view('livewire.food.index', [
            'categories' => $this->categories,
        ]);
    }
}
