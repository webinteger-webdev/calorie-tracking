<div class="max-w-7xl mx-auto p-6">

    <h1 class="text-2xl font-bold mb-6">Lebensmittel-Datenbank</h1>

    @if (session()->has('message'))
        <div class="alert alert-success mb-4">{{ session('message') }}</div>
    @endif

    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body overflow-visible">
            <div class="mb-2 p-2 bg-yellow-100 border border-yellow-300 rounded">
                <p><strong>DEBUG:</strong></p>
                <p>Search: {{ $search }}</p>
                <p>Suggestions: {{ json_encode($suggestions) }}</p>
                <p>Loading: {{ $loading ? 'true' : 'false' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Name + Suche -->
                <div class="form-control mb-4 relative">
                    <label class="label"><span class="label-text">Lebensmittel Suche</span></label>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           placeholder="Lebensmittel suchen..." 
                           class="input input-bordered w-full" />

                    <!-- Ladeanimation -->
                    @if($loading)
                        <div class="absolute right-3 top-3">
                           <span wire:loading class="loading loading-spinner loading-sm absolute right-3 top-3"></span>
                        </div>
                    @endif

                    <!-- Vorschläge Dropdown -->
                    @if (!empty($suggestions))
                        <ul class="absolute bg-base-100 border w-full mt-1 max-h-60 overflow-y-auto rounded shadow z-[9999]">
                            @foreach ($suggestions as $i => $s)
                                <li wire:click="selectSuggestion({{ $i }})"
                                    class="px-4 py-2 hover:bg-primary/20 cursor-pointer text-sm flex justify-between">
                                    <span>{{ $s['name'] }}</span>
                                    <span class="text-gray-500">{{ $s['brand'] }}</span>
                                    <span class="font-semibold">{{ $s['calories'] }} kcal</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">Lebensmittel Name</span></label>
                    <input type="text" wire:model="name" placeholder="Lebensmittel Name..." class="input input-bordered w-full" />
                </div>
            </div>

            <!-- Restliches Formular -->
            <form wire:submit.prevent="save" class="grid grid-cols-2 gap-4">

                <div class="form-control">
                    <label class="label"><span class="label-text">Kalorien (kcal)</span></label>
                    <input type="number" wire:model="calories" class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Protein (g)</span></label>
                    <input type="number" step="0.01" wire:model="protein" class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Kohlenhydrate (g)</span></label>
                    <input type="number" step="0.01" wire:model="carbs" class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Fett (g)</span></label>
                    <input type="number" step="0.01" wire:model="fat" class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Ballaststoffe (g)</span></label>
                    <input type="number" step="0.01" wire:model="fiber" class="input input-bordered w-full">
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Marke</span></label>
                    <input type="text" wire:model="brand" class="input input-bordered w-full" placeholder="Marke eingeben">
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Menge</span></label>
                    <input type="text" wire:model="serving_unit" class="input input-bordered w-full" placeholder="z.B. 100g, 1 Stück">
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Quelle</span></label>
                    <input type="text" wire:model="source" class="input input-bordered w-full" placeholder="Quelle eingeben">
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Kategorie</span></label>
                    <select wire:model="category_id" class="select select-bordered w-full">
                        <option value="">Keine Kategorie</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2 flex justify-between mt-4">
                    <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Aktualisieren' : 'Hinzufügen' }}</button>
                    @if($isEdit)
                        <button type="button" wire:click="resetForm" class="btn btn-ghost">Abbrechen</button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tabelle -->
    <table class="table w-full table-zebra">
        <thead>
        <tr>
            <th>Name</th>
            <th>Marke</th>
            <th>Kcal</th>
            <th>Protein</th>
            <th>Carbs</th>
            <th>Fett</th>
            <th>Fiber</th>
            <th>Aktionen</th>
        </tr>
        </thead>
        <tbody>
        @foreach($foods as $food)
            <tr>
                <td>{{ $food->name }}</td>
                <td>{{ $food->brand }}</td>
                <td>{{ $food->calories }}</td>
                <td>{{ $food->protein }}</td>
                <td>{{ $food->carbs }}</td>
                <td>{{ $food->fat }}</td>
                <td>{{ $food->fiber }}</td>
                <td>
                    <button wire:click="edit({{ $food->id }})" class="btn btn-sm btn-ghost">Bearbeiten</button>
                    <button wire:click="delete({{ $food->id }})" class="btn btn-sm btn-error">Löschen</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
