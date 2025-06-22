<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Leca Moda Fitness</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #fff5f8;
        }

        .login-container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(255, 51, 102, 0.15);
            padding: 40px;
            width: 400px;
            text-align: center;
        }

        .logo-container {
            margin-bottom: 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo-img {
            width: 120px;
            height: 120px;
            border-radius: 20px;
            margin-bottom: 15px;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #ffd1dc;
            border-radius: 10px;
            outline: none;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .input-group input:focus {
            border-color: #ff4d79;
        }

        .input-group label {
            position: absolute;
            top: 15px;
            left: 15px;
            color: #ff6b8b;
            font-size: 16px;
            transition: all 0.3s;
            pointer-events: none;
        }

        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label {
            top: -10px;
            left: 10px;
            font-size: 12px;
            background-color: white;
            padding: 0 5px;
            color: #ff4d79;
        }

        button {
            width: 100%;
            padding: 15px;
            background: #ff4d79;
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 77, 121, 0.3);
            background: #ff2d64;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="Leca Moda Fitness" class="logo-img">
            <h1 class="text-2xl font-bold text-pink-500">Leca</h1>
            <p class="text-sm text-gray-600">Moda Fitness</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="input-group">
                <input type="email" id="email" name="email" placeholder=" " value="{{ old('email') }}" required>
                <label for="email">E-mail</label>
            </div>
            <div class="input-group">
                <input type="password" id="password" name="password" placeholder=" " required>
                <label for="password">Senha</label>
            </div>
            <button type="submit">Entrar</button>
            <div class="mt-4 text-center">
                <a href="{{ route('home') }}" class="text-pink-500 hover:text-pink-600">Voltar para a loja</a>
            </div>
        </form>
    </div>
</body>
</html>
