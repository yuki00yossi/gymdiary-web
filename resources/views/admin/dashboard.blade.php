@extends('layouts.admin')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('content')
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'navy': '#0F1117',
                        'navy-light': '#1A1D24',
                        'accent': '#4B5563'
                    }
                }
            }
        }
    </script>
    <div class="flex h-screen">
        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            <header class="p-4 border-b border-gray-800">
                <div class="relative">
                    <input type="search" placeholder="Search"
                        class="w-full bg-navy-light rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </header>

            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-xl font-semibold">Deployments</h1>
                    <div class="relative">
                        <button class="flex items-center space-x-2 bg-navy-light px-4 py-2 rounded-lg text-sm">
                            <span>Sort by</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Deployment List -->
                <div class="space-y-4">
                    <div class="bg-navy-light rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <span class="font-medium">workout-app</span>
                                </div>
                                <div class="text-sm text-gray-400 mt-1">Deployed 3m ago</div>
                            </div>
                            <button class="px-4 py-1 bg-gray-800 rounded text-sm">Preview</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Right Sidebar -->
        <aside class="w-80 border-l border-gray-800 p-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold">Activity feed</h2>
                <a href="#" class="text-sm text-blue-500">View all</a>
            </div>

            <div class="space-y-6">
                <div class="flex space-x-3">
                    <div class="w-8 h-8 bg-gray-700 rounded-full"></div>
                    <div>
                        <div class="text-sm">
                            <span class="font-medium">User Name</span>
                            <span class="text-gray-400">pushed to main</span>
                        </div>
                        <div class="text-xs text-gray-400 mt-1">3h ago</div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
@endsection
