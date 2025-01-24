<div
    class="bg-gray-800 text-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out">
    <a href="{{ route('admin.dashboard') }}" class="text-white flex items-center space-x-2 px-4">
        <img src="/logo_color.png" class="w-8 h-8" fill="none" stroke="currentColor">
        <span class="text-2xl font-extrabold">Gym Diary</span>
    </a>

    <nav>
        <a href="{{ route('admin.dashboard') }}"
            class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
            Dashboard
        </a>
        <div onclick="toggleSideSubMenu(event)"
            class="block cursor-pointer py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
            Users
            <div class="hidden">
                <a href=""
                    class="block py-2.5 px-4 rounded transition duratition-200 hover:bg-gray-500 hover:text-white">管理ユーザー
                </a>
                <a href=""
                    class="block py-2.5 px-4 rounded transition duratition-200 hover:bg-gray-500 hover:text-white">トレーナー
                </a>
                <a href=""
                    class="block py-2.5 px-4 rounded transition duratition-200 hover:bg-gray-500 hover:text-white">一般ユーザー
                </a>
            </div>
        </div>
        <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
            Training Logs
        </a>
        <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
            Settings
        </a>

        <form method="POST" action="{{ route('admin.logout') }}"
            class="block pointer-cursor py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">
            @csrf
            <button type="submit" class="w-full h-full text-left">
                Logout
            </button>
        </form>
    </nav>
</div>

<script>
    function toggleSideSubMenu(e) {
        if (e.currentTarget !== e.target) {
            return false;
        }
        e.currentTarget.children[0].classList.contains('hidden') ?
            e.currentTarget.children[0].classList.remove('hidden') :
            e.currentTarget.children[0].classList.add('hidden');
        return false;
    }
</script>
