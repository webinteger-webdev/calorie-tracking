<?php

namespace App\Livewire\Food;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Food;
use Illuminate\Support\Facades\Auth;

class Crud extends Component
{
    use WithPagination;

    // Eigenschaften für das Formular
    public $name;
    public $calories;
    public $protein;
    public $carbs;
    public $fat;
    public $fiber;
    public $serving_unit = 'g';
    public $source;

    // Eigenschaften für das Bearbeiten
    public $editMode = false;
    public $foodId;

    // Suche und Filter
    public $search = '';
    protected $queryString = ['search'];

    // Validierungsregeln
    protected $rules = [
        'name' => 'required|string|max:255',
        'calories' => 'required|integer|min:0',
        'protein' => 'required|numeric|min:0',
        'carbs' => 'required|numeric|min:0',
        'fat' => 'required|numeric|min:0',
        'fiber' => 'nullable|numeric|min:0',
        'serving_unit' => 'required|string|in:g,ml',
        'source' => 'nullable|string|max:255',
    ];

    // Formular zurücksetzen
    public function resetForm()
    {
        $this->reset([
            'name',
            'calories',
            'protein',
            'carbs',
            'fat',
            'fiber',
            'serving_unit',
            'source',
            'editMode',
            'foodId'
        ]);
    }

    // Lebensmittel speichern (Create)
    public function save()
    {
        $this->validate();

        Food::create([
            'name' => $this->name,
            'calories' => $this->calories,
            'protein' => $this->protein,
            'carbs' => $this->carbs,
            'fat' => $this->fat,
            'fiber' => $this->fiber,
            'serving_unit' => $this->serving_unit,
            'source' => $this->source,
            'created_by' => Auth::id(),
        ]);

        session()->flash('message', 'Lebensmittel erfolgreich angelegt!');
        $this->resetForm();
    }

    // Lebensmittel bearbeiten (Update) - Formular laden
    public function edit(Food $food)
    {
        $this->editMode = true;
        $this->foodId = $food->id;
        $this->name = $food->name;
        $this->calories = $food->calories;
        $this->protein = $food->protein;
        $this->carbs = $food->carbs;
        $this->fat = $food->fat;
        $this->fiber = $food->fiber;
        $this->serving_unit = $food->serving_unit;
        $this->source = $food->source;
    }

    // Lebensmittel aktualisieren (Update)
    public function update()
    {
        $this->validate();

        $food = Food::findOrFail($this->foodId);
        $food->update([
            'name' => $this->name,
            'calories' => $this->calories,
            'protein' => $this->protein,
            'carbs' => $this->carbs,
            'fat' => $this->fat,
            'fiber' => $this->fiber,
            'serving_unit' => $this->serving_unit,
            'source' => $this->source,
        ]);

        session()->flash('message', 'Lebensmittel erfolgreich aktualisiert!');
        $this->resetForm();
    }

    // Lebensmittel löschen (Delete)
    public function delete(Food $food)
    {
        $food->delete();
        session()->flash('message', 'Lebensmittel erfolgreich gelöscht!');
    }

    // Lebensmittel-Liste anzeigen (Read)
    public function render()
    {
        $foods = Food::query()
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.food.index', [
            'foods' => $foods,
        ]);
    }
}
