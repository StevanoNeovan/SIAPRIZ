<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIAPRIZ') }} - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left side: Logo & Menu -->
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-xl font-bold text-indigo-600">SIAPRIZ</h1>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a 
                            href="{{ route('dashboard') }}" 
                            class="@if(request()->routeIs('dashboard')) border-indigo-500 text-gray-900 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                        >
                            Dashboard
                        </a>
                        
                        <a 
                            href="{{ route('infografis.index') }}" 
                            class="@if(request()->routeIs('infografis.*')) border-indigo-500 text-gray-900 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                        >
                            Infografis
                        </a>
                        
                        @if(auth()->user()->isAdministrator())
                            <a 
                                href="{{ route('penjualan.upload') }}" 
                                class="@if(request()->routeIs('penjualan.*')) border-indigo-500 text-gray-900 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                            >
                                Upload Penjualan
                            </a>
                            
                            <a 
                                href="{{ route('profil-usaha.index') }}" 
                                class="@if(request()->routeIs('profil-usaha.*')) border-indigo-500 text-gray-900 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 @endif inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                            >
                                Profil Usaha
                            </a>
                        @endif
                    </div>
                </div>
                
                <!-- Right side: User Info & Logout -->
                <div class="flex items-center space-x-4">
                    <div class="text-sm">
                        <p class="font-medium text-gray-900">{{ auth()->user()->nama_lengkap }}</p>
                        <p class="text-gray-500 text-xs">{{ auth()->user()->role->nama_role }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button 
                            type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        >
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <a 
                    href="{{ route('dashboard') }}" 
                    class="@if(request()->routeIs('dashboard')) bg-indigo-50 border-indigo-500 text-indigo-700 @else border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 @endif block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                >
                    Dashboard
                </a>
                
                <a 
                    href="{{ route('infografis.index') }}" 
                    class="@if(request()->routeIs('infografis.*')) bg-indigo-50 border-indigo-500 text-indigo-700 @else border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 @endif block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                >
                    Infografis
                </a>
                
                @if(auth()->user()->isAdministrator())
                    <a 
                        href="{{ route('penjualan.upload') }}" 
                        class="@if(request()->routeIs('penjualan.*')) bg-indigo-50 border-indigo-500 text-indigo-700 @else border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 @endif block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                    >
                        Upload Penjualan
                    </a>
                    
                    <a 
                        href="{{ route('profil-usaha.index') }}" 
                        class="@if(request()->routeIs('profil-usaha.*')) bg-indigo-50 border-indigo-500 text-indigo-700 @else border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 @endif block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
                    >
                        Profil Usaha
                    </a>
                @endif
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Page Content -->
    <main class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>
</body>
</html>