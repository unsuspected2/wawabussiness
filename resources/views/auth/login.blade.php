<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WawaBusiness</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            width: 100%;
            overflow: hidden;
        }

        body {
            background: linear-gradient(135deg, #0a1d4d 0%, #13294b 50%, #0f2545 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #e0e7ff;
            position: relative;
        }

        /* Ícones flutuantes – animação suave e que chega ao topo */
        .floating-icons i {
            position: absolute;
            font-size: 3.2rem;
            opacity: 0.10;
            animation: riseSmooth 55s linear infinite;
            pointer-events: none;
            will-change: transform;
        }

        @keyframes riseSmooth {
            0%   { transform: translateY(120vh) rotate(0deg);   opacity: 0.10; }
            10%  { opacity: 0.18; }
            90%  { opacity: 0.18; }
            100% { transform: translateY(-10vh) rotate(360deg); opacity: 0; }
        }

        /* Cores das marcas – mais suaves */
        .netflix   { color: #e50914; animation-duration: 55s; animation-delay: 0s;    }
        .hbo       { color: #d1d5db; animation-duration: 62s; animation-delay: 6s;    }
        .prime     { color: #00a8e1; animation-duration: 58s; animation-delay: 12s;   }
        .disney    { color: #0f71a8; animation-duration: 65s; animation-delay: 18s;   }
        .iptv      { color: #facc15; animation-duration: 70s; animation-delay: 24s;   }
        .spotify   { color: #1db954; animation-duration: 68s; animation-delay: 30s;   }

        /* Container central – caixa maior */
        .login-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 520px; /* ← Aumentado para ficar mais confortável */
            z-index: 10;
        }

        .login-box {
            background: rgba(15, 23, 42, 0.82);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 20px;
            padding: 45px 40px;
            box-shadow: 0 25px 70px rgba(0,0,0,0.65);
            border: 1px solid rgba(255,255,255,0.07);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo i {
            font-size: 5.5rem;
            color: #60a5fa;
            filter: drop-shadow(0 0 20px rgba(96,165,250,0.5));
        }

        h2 {
            text-align: center;
            font-size: 2.4rem;
            margin-bottom: 12px;
            color: #f1f5f9;
            letter-spacing: 1px;
        }

        h3 {
            text-align: center;
            font-size: 1.15rem;
            color: #cbd5e1;
            margin-bottom: 40px;
            font-weight: 400;
        }

        .error-msg {
            color: #fca5a5;
            text-align: center;
            margin-bottom: 25px;
            font-size: 1rem;
        }

        .input-group {
            position: relative;
            margin-bottom: 38px;
        }

        .input-group input {
            width: 100%;
            padding: 16px 0 12px;
            font-size: 1.15rem;
            color: #f1f5f9;
            background: transparent;
            border: none;
            border-bottom: 2px solid rgba(203,213,225,0.35);
            outline: none;
            transition: all 0.35s ease;
        }

        .input-group input:focus {
            border-bottom-color: #60a5fa;
        }

        .input-group label {
            position: absolute;
            top: 16px;
            left: 0;
            font-size: 1.15rem;
            color: #94a3b8;
            pointer-events: none;
            transition: all 0.35s ease;
        }

        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label {
            top: -12px;
            font-size: 0.95rem;
            color: #60a5fa;
        }

        .remember {
            display: flex;
            align-items: center;
            margin: 25px 0 35px;
            color: #cbd5e1;
            font-size: 1rem;
        }

        .remember input {
            margin-right: 12px;
            width: 18px;
            height: 18px;
            accent-color: #60a5fa;
        }

        button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
            border: none;
            border-radius: 999px;
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s ease;
            box-shadow: 0 10px 30px rgba(59,130,246,0.35);
        }

        button:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(59,130,246,0.55);
        }

        @media (max-width: 480px) {
            .login-box { padding: 35px 28px; }
            h2 { font-size: 2rem; }
            .logo i { font-size: 4.5rem; }
            .login-container { max-width: 420px; }
        }
    </style>
</head>
<body>

    <!-- Ícones flutuantes – agora sobem até o topo suavemente -->
    <div class="floating-icons">
        <i class="fab fa-netflix tech-icon netflix"   style="top:10%; left:8%;"></i>
        <i class="fas fa-play-circle tech-icon netflix" style="top:22%; left:78%;"></i>
        <i class="fas fa-film tech-icon hbo"          style="top:35%; left:12%;"></i>
        <i class="fas fa-tv tech-icon prime"          style="top:48%; left:88%;"></i>
        <i class="fas fa-crown tech-icon disney"      style="top:62%; left:6%;"></i>
        <i class="fas fa-satellite-dish tech-icon iptv" style="top:78%; left:65%;"></i>
        <i class="fas fa-music tech-icon spotify"     style="top:15%; left:48%;"></i>
        <i class="fas fa-video tech-icon hbo"         style="top:30%; left:32%;"></i>
        <i class="fas fa-play tech-icon prime"        style="top:55%; left:20%;"></i>
        <i class="fas fa-film tech-icon disney"       style="top:70%; left:75%;"></i>
    </div>

    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <i class="fas fa-user-lock"></i>
            </div>

            <h2>WawaBusiness</h2>
            <h3>Entre com seu email e senha</h3>

            @if ($errors->any())
                <div class="error-msg">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="input-group">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder=" ">
                    <label for="email">Email</label>
                </div>

                <div class="input-group">
                    <input id="password" type="password" name="password" required placeholder=" ">
                    <label for="password">Senha</label>
                </div>

                <div class="remember">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Manter-me conectado</label>
                </div>

                <button type="submit">Entrar no Painel</button>
            </form>
        </div>
    </div>

    @if (session('status'))
        <script>
            Swal.fire({
                title: 'Sucesso!',
                text: '{{ session('status') }}',
                icon: 'success',
                confirmButtonColor: '#60a5fa'
            });
        </script>
    @endif

</body>
</html>
