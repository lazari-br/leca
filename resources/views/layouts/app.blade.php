<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Leca Moda Fitness')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --primary-color: #ff4d79;
            --secondary-color: #ffd1dc;
        }
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 77, 121, 0.3);
        }
    </style>
    @yield('styles')
</head>
<body class="bg-gray-50">
<!-- Header -->
<header class="bg-white shadow-sm">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="Leca Moda Fitness" class="h-16 w-16 rounded-lg">
                <div class="ml-3">
                    <h1 class="text-xl font-bold text-pink-500">Leca</h1>
                    <p class="text-sm text-gray-600">Moda Fitness</p>
                </div>
            </a>

            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-pink-500">Home</a>
                <a href="{{ route('product.category', 'fitness') }}" class="text-gray-700 hover:text-pink-500">Catálogo</a>
                {{--                    <a href="{{ route('product.category', 'pijamas') }}" class="text-gray-700 hover:text-pink-500">Pijamas</a>--}}
                <!-- Link de gerenciamento de produtos (visível apenas para usuários autenticados) -->
                @auth
                    <a href="{{ route('admin.index') }}">Admin</a>
                @endauth
                <a href="#" class="text-gray-700 hover:text-pink-500">Contato</a>
            </nav>

            <div class="flex items-center space-x-4">
                @guest
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-pink-500">Login</a>
                @else
                    <!-- Ícone de Perfil -->
                    <a href="{{ route('profile.edit') }}"
                       class="text-gray-700 hover:text-pink-500 flex items-center"
                       title="Editar Perfil">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="ml-1 hidden sm:inline"></span>
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-pink-500">Sair</button>
                    </form>
                @endguest
            </div>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="container mx-auto px-4 py-8">
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-gray-800 text-white py-8">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-bold mb-4">Leca Moda Fitness</h3>
                <p class="text-gray-300">Conforto e estilo para todos os momentos.</p>
            </div>
            <div>
                <h3 class="text-lg font-bold mb-4">Links Rápidos</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-white">Home</a></li>
                    <li><a href="{{ route('product.category', 'fitness') }}" class="text-gray-300 hover:text-white">Catálogo</a></li>
                    {{--                        <li><a href="{{ route('product.category', 'pijamas') }}" class="text-gray-300 hover:text-white">Pijamas</a></li>--}}
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-bold mb-4">Contato</h3>
                <ul class="space-y-2 text-gray-300">
                    <li>WhatsApp: (11) 96216-3422</li>
                    <li>Email: leca.pijamas@gmail.com</li>
                    <li>Instagram: @lecapijamas</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
            <p>&copy; {{ date('Y') }} Leca Moda Fitness. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>

@stack('scripts')
@include('components.chat-widget')
</body>
</html>
