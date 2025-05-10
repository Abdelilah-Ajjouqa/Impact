<style>
    @import url('https://fonts.googleapis.com/css2?family=Edu+VIC+WA+NT+Beginner:wght@400..700&display=swap');

    .impact-font {
        font-family: 'Edu VIC WA NT Beginner', cursive;
    }

    .logo {
        background-image: url('{{ asset('butterfly-logo.png') }}');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        width: 3.5rem;
        height: 9vh;
        
    }
</style>

<!-- Navigation -->
<nav class="bg-white shadow-md py-4 fixed w-full z-10 h-16">
    <div class="container mx-auto px-4 flex justify-between items-center h-full">
        <a href="{{ route('posts.index') }}" class="text-black font-bold text-2xl flex items-center h-full">
            <img src="/butterfly-logo.png" alt="Logo"
                class="bg-cover no-repeat h-[9vh] w-auto"/>
            {{-- <div class="logo"></div> --}}
            <span class="impact-font">Impact</span>
        </a>

        <div class="hidden md:block flex-grow mx-4">
            <form action="{{ route('search') }}" method="GET">
                <div class="relative flex items-center">
                    <input type="text" name="q" placeholder="Search" value="{{ request('q') }}"
                        class="w-full bg-gray-100 rounded-full py-2 px-4 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-700">
                        <button type="submit">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-500"></i>
                        {{-- <i class="fas fa-search"></i> --}}
                    </button>
                </div>
            </form>
        </div>

        <div class="flex items-center space-x-4">
            <a href="{{ route('posts.index') }}" class="text-gray-700 hover:bg-sky-100 p-2 rounded-full">
                <i class="fas fa-home"></i>
            </a>

            @auth
                <a href="{{ route('posts.create') }}" class="text-gray-700 hover:bg-sky-100 p-2 rounded-full text-lg transition-all duration-300 hover:scale-110 hover:text-sky-600">
                    <i class="fa-solid fa-square-plus"></i>
                </a>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center focus:outline-none">
                        <img src="{{ Auth::user()->avatar ?? 'https://placehold.co/40' }}" alt="Profile"
                            class="w-8 h-8 rounded-full hover:opacity-80">
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                        <a href="{{ route('users.show', Auth::id()) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-100">Profile</a>
                        @if (Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-sky-100">Admin Dashboard</a>
                        @endif
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-sky-100">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('auth.login.form') }}"
                    class="bg-gray-100 hover:bg-sky-100 text-gray-800 font-semibold py-2 px-4 rounded-full">Log in</a>
                <a href="{{ route('auth.register.form') }}"
                    class="bg-sky-500 hover:bg-sky-600 text-white font-semibold py-2 px-4 rounded-full">Sign up</a>
            @endauth
        </div>
    </div>
</nav>
