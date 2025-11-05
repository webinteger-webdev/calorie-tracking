<?

$userTheme = auth()->user()->settings()->get('theme');
$userFont = explode("|", auth()->user()->settings()->get('font'))[0] ?? 'Outfit';

?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $userTheme }}"
    style="font-family: '{{ $userFont }}'">

<head>
    @include('partials.head')
</head>

<body class="h-screen flex bg-base-100">

    <!-- Sidebar -->
    <aside
        class="bg-base-300 text-base-content p-6 xborder-r-1 xborder-neutral w-64 hidden lg:block flex flex-col h-full fixed z-9999">
        <!-- <div class="p-4">
      <h2 class="text-xl font-bold mb-4">Dashboard</h2>
      <ul>
        <li class="mb-2">
          <a href="#" class="block px-4 py-2 hover:bg-gray-700">Home</a>
        </li>
        <li class="mb-2">
          <a href="#" class="block px-4 py-2 hover:bg-gray-700">Analytics</a>
        </li>
        <li class="mb-2">
          <a href="#" class="block px-4 py-2 hover:bg-gray-700">Settings</a>
        </li>
      </ul>
    </div> -->
        <div class="flex flex-col h-full">
            <div class="flex-none">
                <h1 class="text-lg font-bold mb-2 flex items-stretch">
                    <div
                        class="flex aspect-square size-8 items-center justify-center rounded-box bg-primary text-accent-foreground">
                        <a href="{{ route('dashboard') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chef-hat-icon lucide-chef-hat size-5"><path d="M17 21a1 1 0 0 0 1-1v-5.35c0-.457.316-.844.727-1.041a4 4 0 0 0-2.134-7.589 5 5 0 0 0-9.186 0 4 4 0 0 0-2.134 7.588c.411.198.727.585.727 1.041V20a1 1 0 0 0 1 1Z"/><path d="M6 17h12"/></svg>
                        </a>
                    </div>

                    <span class="self-center ml-2">Kalorie Tracker</span>
                </h1>

                <h2 class="text-md font-bold mt-6 mb-2">Platform</h2>
                <ul class="menu rounded-box w-full xxw-56">
                    @include('partials.navbar.menu-main')
                </ul>
            </div>
            <div class="flex-1"></div>
            <div class="flex-none">
                <h2 class="text-md font-bold mb-2">Links</h2>

                <ul class="menu rounded-box w-full xxw-56 mb-2">
                    <li>
                        <a href="{{ route('components') }}"
                            class={{ request()->routeIs('components') ? 'menu-active' : '' }}
                            wire:navigate><x-heroicon-o-puzzle-piece class="size-5" />
                            Components</a>
                    </li>
                </ul>

                <div class="dropdown dropdown-start dropdown-top flex items-stretch  rounded-box">

                    <div tabindex="0" role="button"
                        class=" btn btn-ghost btn-square rounded-box hover:bg-base-200 avatar avatar-placeholder ">
                        <div class="w-8 rounded-box">
                            {{-- <img src="{{ Avatar::create(auth()->user()->name)->setFontFamily($userFont)->toBase64() }}" /> --}}

                            <div>
                                <span class="text-base font-bold">{{ auth()->user()->initials() }}</span>
                            </div>

                        </div>
                    </div>

                    <div class="w-full text-md self-center ml-2">{{ auth()->user()->name }}</div>
                    <div class="self-center">
                        <!-- up down chevron thing -->
                    </div>

                    @include('partials.navbar.user-dropdown')

                </div>
            </div>
        </div>
    </aside>

    <main class="flex-grow flex flex-col lg:pl-64 bg-base-100 text-base-content">

        <!-- existing header goes here... -->
        <header class="bg-base-300 shadow-sm lg:hidden">
            @include('partials.navbar')
        </header>

        <div class="p-6 @container">
            {{ $slot }}
        </div>
    </main>

    {{-- @include('partials.footer') --}}

    @livewireScripts
    @stack('scripts')
</body>

</html>
