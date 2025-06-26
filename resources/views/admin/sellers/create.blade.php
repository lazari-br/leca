@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="mb-6">
            <a href="{{ route('admin.sellers.index') }}" class="text-blue-500 hover:text-blue-700">
                &larr; Voltar para lista de vendedores
            </a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Cadastrar Vendedor</h1>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 max-w-2xl">
            <form method="POST" action="{{ route('admin.sellers.store') }}">
                @csrf

                <!-- Mostrar erros de validação -->
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-6">
                    <!-- Nome -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                               placeholder="Nome completo do vendedor"
                               value="{{ old('name') }}"
                               required>
                        @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                               placeholder="email@exemplo.com"
                               value="{{ old('email') }}"
                               required>
                        @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Senha -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Senha <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                                   placeholder="Digite a senha (mínimo 8 caracteres)"
                                   required>
                            <button type="button"
                                    id="togglePassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg id="eyeOpen" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eyeClosed" class="h-5 w-5 text-gray-400 hover:text-gray-600 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-xs mt-1">A senha deve ter pelo menos 8 caracteres</p>
                    </div>

                    <!-- Info sobre tipo de usuário -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm text-blue-700 font-medium">Informação</p>
                                <p class="text-sm text-blue-600">Este usuário será automaticamente cadastrado como vendedor. Você poderá definir o estoque e comissão na tela de edição.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex gap-4 mt-8">
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-md focus:outline-none focus:shadow-outline transition-colors">
                        Cadastrar Vendedor
                    </button>
                    <a href="{{ route('admin.sellers.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-md focus:outline-none transition-colors">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('togglePassword');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');

            toggleButton.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle eye icons
                if (type === 'text') {
                    eyeOpen.classList.add('hidden');
                    eyeClosed.classList.remove('hidden');
                } else {
                    eyeOpen.classList.remove('hidden');
                    eyeClosed.classList.add('hidden');
                }
            });
        });
    </script>
@endsection
