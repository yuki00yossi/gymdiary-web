<div
    class="bg-gray-800 text-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out">
    <a href="{{ route('admin.dashboard') }}" class="text-white flex items-center space-x-2 px-4">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
        </svg>
        <span class="text-2xl font-extrabold">Gym Diary</span>
    </a>

    <nav>
        <a href="{{ route('admin.dashboard') }}"
            class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
            Dashboard
        </a>
        <a href="" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
            Users
        </a>
        <a href="" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
            Training Logs
        </a>
        <a href="" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
            Settings
        </a>
    </nav>
</div>
