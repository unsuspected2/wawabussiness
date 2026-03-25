<!doctype html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WawaBusiness - @yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Bootstrap 5 & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --sidebar-width: 260px;
            --bg-dark: #0a0a0b;
            --bg-sidebar: #111112;
            --accent-color: #198754;
        }

        body {
            background-color: var(--bg-dark);
            color: #e2e8f0;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            background: var(--bg-sidebar);
            color: #fff;
            transition: all 0.3s;
            height: 100vh;
            position: fixed;
            border-right: 1px solid #222;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 25px;
            text-align: center;
            border-bottom: 1px solid #222;
        }

        .sidebar-header h3 {
            font-size: 1.6rem;
            margin: 0;
            letter-spacing: 1px;
        }

        #sidebar ul.components {
            padding: 20px 0;
        }

        #sidebar ul li a {
            padding: 12px 20px;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 10px;
            margin: 4px 15px;
            transition: all 0.3s;
        }

        #sidebar ul li a i {
            margin-right: 12px;
            width: 24px;
            text-align: center;
        }

        #sidebar ul li a:hover,
        #sidebar ul li.active a {
            color: #fff;
            background: rgba(25, 135, 84, 0.15);
            color: var(--accent-color);
        }

        #content {
            width: 100%;
            padding: 40px 30px;
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            transition: all 0.3s;
        }

        .badge-vencidos {
            background: #dc3545;
            font-size: 0.75rem;
            padding: 4px 8px;
            margin-left: auto;
        }

        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
            }

            #sidebar.active {
                margin-left: 0;
            }

            #content {
                margin-left: 0;
                padding: 20px 15px;
            }

            #sidebarCollapse {
                display: block !important;
            }
        }

        .logout-section {
            position: absolute;
            bottom: 20px;
            width: 100%;
            padding: 0 15px;
        }

        .btn-logout {
            width: 100%;
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: none;
            padding: 12px;
            border-radius: 8px;
            transition: 0.3s;
            font-size: 1.05rem;
        }

        .btn-logout:hover {
            background: #dc3545;
            color: #fff;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #111;
        }

        ::-webkit-scrollbar-thumb {
            background: #444;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3 class="text-success fw-bold">WawaBusiness</h3>
            </div>

            <ul class="list-unstyled components">
                <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>

                <li class="{{ request()->routeIs('clients.*') ? 'active' : '' }}">
                    <a href="{{ route('clients.index') }}">
                        <i class="fas fa-users"></i> Clientes
                        @if ($vencidosCount ?? 0 > 0)
                            <span class="badge-vencidos">{{ $vencidosCount }}</span>
                        @endif
                    </a>
                </li>

                <li class="{{ request()->routeIs('services.*') ? 'active' : '' }}">
                    <a href="{{ route('services.index') }}">
                        <i class="fas fa-tv"></i> Serviços
                    </a>
                </li>

                <li class="{{ request()->routeIs('payments.index') ? 'active' : '' }}">
                    <a href="{{ route('payments.index') }}">
                        <i class="fas fa-hand-holding-dollar"></i> Renovações
                    </a>
                </li>

                <!-- Nova seção: Perfis Alocados -->
                <li class="{{ request()->routeIs('perfis-alocados.') ? 'active' : '' }}">
                    <a href="{{ route('perfis-alocados.index') }}">
                        <i class="fas fa-user-lock"></i> Perfis Alocados
                    </a>
                </li>

                <li class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <a href="{{ route('reports.index') }}">
                        <i class="fas fa-chart-line"></i> Relatórios
                    </a>
                </li>

                <li class="{{ request()->routeIs('withdrawals.index') ? 'active' : '' }}">
                    <a href="{{ route('withdrawals.index') }}">
                        <i class="fas fa-money-bill-transfer"></i> Saques / Caixa
                    </a>
                </li>

                <li class="{{ request()->routeIs('cash-closure.*') ? 'active' : '' }}">
                    <a href="{{ route('cash-closure.index') }}">
                        <i class="fas fa-cash-register"></i> Fechamento de Caixa Mensal
                    </a>
                </li>

                <li class="{{ request()->routeIs('logs.index') ? 'active' : '' }}">
                    <a href="{{ route('logs.index') }}">
                        <i class="fas fa-history"></i> Logs de Atividades
                    </a>
                </li>

                <li>
                    <a href="#">
                        <i class="fas fa-cog"></i> Configurações
                    </a>
                </li>
            </ul>

            <div class="logout-section">
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout text-start">
                        <i class="fas fa-sign-out-alt me-2"></i> Sair do Painel
                    </button>
                </form>
            </div>
        </nav>

        <!-- Conteúdo Principal -->
        <div id="content">
            <!-- Botão de toggle no mobile -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-transparent d-md-none mb-4">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-success">
                        <i class="fas fa-align-left"></i>
                    </button>
                    <span class="text-white ms-3 fw-bold">WawaBusiness</span>
                </div>
            </nav>

            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar').toggleClass('active');
            });
        });
    </script>
</body>

</html>
