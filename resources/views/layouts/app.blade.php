<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Leca Pijamas e Moda Fitness')</title>
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
                    <img src="{{ asset('images/logo.png') }}" alt="Leca Pijamas e Moda Fitness" class="h-16 w-16 rounded-lg">
                    <div class="ml-3">
                        <h1 class="text-xl font-bold text-pink-500">Leca</h1>
                        <p class="text-sm text-gray-600">Pijamas e Moda Fitness</p>
                    </div>
                </a>

                <nav class="hidden md:flex space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-pink-500">Home</a>
                    <a href="{{ route('product.category', 'fitness') }}" class="text-gray-700 hover:text-pink-500">Fitness</a>
                    <a href="{{ route('product.category', 'pijamas') }}" class="text-gray-700 hover:text-pink-500">Pijamas</a>
                    <!-- Link de gerenciamento de produtos (visível apenas para usuários autenticados) -->
                    @auth
                        <a href="{{ route('admin.products.index') }}" class="text-gray-700 hover:text-pink-500">Gerenciar Produtos</a>
                    @endauth
                    <a href="#" class="text-gray-700 hover:text-pink-500">Contato</a>
                </nav>

                <div class="flex items-center space-x-4">
                    <a
                        href="https://wa.me/5511962163422?text=Olá! Cheguei aqui pelo site da Leca e gostaria de tirar uma dúvida!"
                        target="_blank"
                        class="fixed bottom-6 right-6 bg-green-500 hover:bg-green-600 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg transition-colors z-50"
                        aria-label="Fale conosco no WhatsApp"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                        </svg>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-pink-500">Login</a>
                    @else
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
                    <h3 class="text-lg font-bold mb-4">Leca Pijamas e Moda Fitness</h3>
                    <p class="text-gray-300">Conforto e estilo para todos os momentos.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Links Rápidos</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-300 hover:text-white">Home</a></li>
                        <li><a href="{{ route('product.category', 'fitness') }}" class="text-gray-300 hover:text-white">Fitness</a></li>
                        <li><a href="{{ route('product.category', 'pijamas') }}" class="text-gray-300 hover:text-white">Pijamas</a></li>
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
                <p>&copy; {{ date('Y') }} Leca Pijamas e Moda Fitness. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
