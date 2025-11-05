<li>
    <a href="{{ route('dashboard') }}" class={{ request()->routeIs('dashboard') ? "menu-active" : "" }} wire:navigate
      ><x-heroicon-o-squares-2x2 class="size-5"/>
      Dashboard</a
    >
  </li>
  <li>
    <a href="{{ route('foods') }}" class={{ request()->routeIs('') ? "menu-active" : "" }} wire:navigate
        ><x-daisyui.icon name="beef" class="w-5 h-5" />
      Lebensmiittel</a
    >
  </li>
