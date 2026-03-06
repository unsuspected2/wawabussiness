<!doctype html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WawaBusiness - Dashboard</title>

    <!-- Bootstrap 5 & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --sidebar-width: 260px;
            --bg-dark: #0a0a0b;
            --bg-sidebar: #111112;
            --accent-color: #198754;
            /* Verde Sucesso */
        }

        body {
            background-color: var(--bg-dark);
            color: #e2e8f0;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        /* Estrutura Principal */
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }

        /* Menu Lateral */
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
        }

        .sidebar-header {
            padding: 25px;
            text-align: center;
            border-bottom: 1px solid #222;
        }

        .sidebar-header h3 {
            font-size: 1.5rem;
            margin: 0;
            letter-spacing: 1px;
        }

        #sidebar ul.components {
            padding: 20px 0;
        }

        #sidebar ul li {
            padding: 5px 15px;
        }

        #sidebar ul li a {
            padding: 12px 15px;
            font-size: 1.05rem;
            display: block;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 10px;
            transition: 0.3s;
        }

        #sidebar ul li a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        #sidebar ul li a:hover,
        #sidebar ul li.active>a {
            color: #fff;
            background: rgba(25, 135, 84, 0.15);
            color: var(--accent-color);
        }

        /* Conteúdo Principal */
        #content {
            width: 100%;
            padding: 40px;
            min-height: 100vh;
            transition: all 0.3s;
            margin-left: var(--sidebar-width);
        }

        /* Estilo para Mobile */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
            }

            #sidebar.active {
                margin-left: 0;
            }

            #content {
                margin-left: 0;
                padding: 20px;
            }

            #sidebarCollapse {
                display: block !important;
            }
        }

        /* Botão Sair no fundo */
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
            padding: 10px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn-logout:hover {
            background: #dc3545;
            color: #fff;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #000;
        }

        ::-webkit-scrollbar-thumb {
            background: #333;
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
                        <i class="fas fa-chart-pie"></i> Dashboard
                    </a>
                </li>
                <li class="{{ request()->routeIs('clients.*') ? 'active' : '' }}">
                    <a href="{{ route('clients.index') }}">
                        <i class="fas fa-user-friends"></i> Clientes
                    </a>
                </li>
                <li class="{{ request()->routeIs('reports.index') ? 'active' : '' }}">
                    <a href="{{ route('reports.index') }}">
                        <i class="fas fa-file-invoice-dollar"></i> Relatórios
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

        <!-- Page Content -->
        <div id="content">
            <!-- Botão de Menu para Mobile (Oculto no Desktop) -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-transparent d-md-none mb-4">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-success">
                        <i class="fas fa-align-left"></i>
                    </button>
                    <span class="text-white ms-3">WawaBusiness</span>
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
            // Toggle para o menu no mobile
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar').toggleClass('active');
            });
        });
    </script>
</body>

</html>
