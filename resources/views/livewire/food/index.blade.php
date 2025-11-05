<div>

    <x-daisyui.heading level="1" size="xl">
        <div class="flex items-center gap-2 mb-2">
            <x-daisyui.icon name="beef" class="h-6 w-6 text-primary mr-1"/>
            Lebensmittel-Datenbank
        </div>
    </x-daisyui.heading>
    <x-daisyui.heading level="1" size="sm" type="sub">Lebensmittel Verwaltung mit API Abfragen</x-daisyui.heading>
    <x-daisyui.separator/>

    @if (session()->has('message'))
        <x-daisyui.toast
            message="{{ session('message') }}"
            type="success"
            icon="info"
            duration="3000"
        />
    @endif

    <div class="text-base-content [&>.card]:bg-base-200 lg:gap-6 mx-auto [&>*]:mb-6 [&>*]:break-inside-avoid [&_:is(div,button)]:[transition:background-color_0ms,border-color_100ms,box-shadow_300ms,border-radius_500ms_ease-out]">
    <div class="card card-border border-base-300 card-sm">
        <div class="card-body gap-4 overflow-visible">
            <div class="mb-2 p-2 bg-yellow-100 border border-yellow-300 dark:bg-gray-700 dark:border-gray-900 rounded">
                <p><strong>DEBUG:</strong></p>
                <p>Search: {{ $search }}</p>
                <p>Suggestions: {{ json_encode($suggestions) }}</p>
                <p>Loading: {{ $loading ? 'true' : 'false' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Name + Suche -->
                <div class="form-control mb-4 relative">
                    <x-daisyui.input id="api-search" name="api-search" wire:model.live.debounce.300ms="search"
                        label="Lebensmittel Suche"
                        placeholder="Lebensmittel suchen..."
                        class="input input-bordered w-full"
                        icon="search"
                        />

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

                <x-daisyui.input id="name" name="name" wire:model="name" label="Lebensmittel Name" placeholder="Lebensmittel Name..." class="input input-bordered w-full" />
            </div>

            <!-- Restliches Formular -->
            <form wire:submit.prevent="save" class="grid grid-cols-2 gap-4">
                <x-daisyui.input type="number" step="1" id="calories" name="calories" wire:model="calories" label="Kalorien (kcal)" class="input input-bordered w-full" />

                <x-daisyui.input type="number" step="0.01" id="carbs" name="carbs" wire:model="carbs" label="Kohlenhydrate (g)" class="input input-bordered w-full" />

                <x-daisyui.input type="number" step="0.01" id="protein" name="protein" wire:model="protein" label="Protein (g)" class="input input-bordered w-full" />

                <x-daisyui.input type="number" step="0.01" id="fat" name="fat" wire:model="fat" label="Fett (g)" class="input input-bordered w-full" />

                <x-daisyui.input type="number" step="0.01" id="fiber" name="fiber" wire:model="fiber" label="Ballaststoffe (g)" class="input input-bordered w-full" />

                <x-daisyui.input id="brand" name="brand" wire:model="brand" label="Marke" placeholder="Marke eingeben" class="input input-bordered w-full" />

                <x-daisyui.input id="serving-unit" name="serving-unit" wire:model="serving_unit" label="Menge" description="z.B. 100g, 1 Stück" class="input input-bordered w-full" />

                <x-daisyui.input id="source" name="source" wire:model="source" label="Menge" placeholder="Quelle eingeben" description="z.B. Open Food Facts" class="input input-bordered w-full" />

                <div class="col-span-2 flex items-center gap-2">
                    <x-daisyui.select
                        name="category_id"
                        label="Kategorie"
                        placeholder="Keine Kategorie"
                        :options="$categories"
                        optionValue="id"
                        optionLabel="name"
                        selectClass="select-bordered w-full"
                        wire:model="category_id"
                    />
                    <x-daisyui.button type="button" class="btn btn-sm btn-outline" wire:click="openAddCategoryModal">
                        Kategorie hinzufügen
                    </x-daisyui.button>
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


    <!-- Modal zum Hinzufügen einer neuen Kategorie -->
    <x-daisyui.modal name="category" wire:model="showAddCategoryModal" title="Neue Kategorie hinzufügen">
        <form wire:submit.prevent="addCategory" class="space-y-4">
            <x-daisyui.input id="new-category-name" name="new-category-name" wire:model="newCategoryName" label="Kategoriename" placeholder="Name der Kategorie" class="input input-bordered w-full" />

            <div class="modal-action">
                <x-daisyui.button type="button" class="btn btn-ghost" wire:click="$set('showAddCategoryModal', false)">Abbrechen</x-daisyui.button>
                <x-daisyui.button type="submit" class="btn btn-primary">Hinzufügen</x-daisyui.button>
            </div>
        </form>
    </x-daisyui.modal>
</div>
