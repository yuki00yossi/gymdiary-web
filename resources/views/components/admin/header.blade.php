<header class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">
            @yield('header')
        </h1>
        <div class="flex items-center">
            <span class="text-gray-500 mr-2">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-gray-700">
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>
