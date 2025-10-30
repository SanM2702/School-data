<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CuentasCobro')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --color-primary: #F57C00; /* Naranja cálido */
            --color-secondary: #424242; /* Gris oscuro */
            --color-accent: #0277BD; /* Azul petróleo */
            --color-bg: #F5F5F5; /* Fondo suave */
            --color-bg-contrast: #FFFFFF; /* Blanco */
            --color-text: #212121; /* Texto principal */
            --color-text-muted: #6b7280; /* Gris para texto secundario */
        }

        body {
            color: var(--color-text);
            background-color: var(--color-bg);
        }

        a, .link-primary {
            color: var(--color-accent);
        }
        a:hover, .link-primary:hover {
            color: #015a8e; /* hover del acento */
        }

        /* Botones principales y secundarios */
        .btn-primary {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: #e06f00;
            border-color: #e06f00;
        }

        .btn-secondary {
            background-color: var(--color-secondary);
            border-color: var(--color-secondary);
        }
        .btn-secondary:hover, .btn-secondary:focus {
            background-color: #333333;
            border-color: #333333;
        }

        /* Opcional: botón con color de acento */
        .btn-accent {
            background-color: var(--color-accent);
            border-color: var(--color-accent);
            color: #fff;
        }
        .btn-accent:hover, .btn-accent:focus {
            background-color: #015a8e;
            border-color: #015a8e;
            color: #fff;
        }

        /* Tarjetas de login */
        .login-container {
            min-height: 100vh;
            /* El fondo de imagen se maneja inline en las vistas de auth; esto sirve como fallback */
            background-color: var(--color-bg);
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        /* Navbar y branding */
        .navbar-brand {
            font-weight: bold;
            color: var(--color-secondary) !important;
        }

        /* Sidebar */
        .sidebar {
            background: var(--color-secondary);
            min-height: calc(100vh - 56px);
        }
        .sidebar .nav-link {
            color: #ECEFF1;
            padding: 12px 20px;
            border-radius: 5px;
            margin: 2px 0;
        }
        .sidebar .nav-link:hover {
            background: #333333;
            color: #fff;
        }
        .sidebar .nav-link.active {
            background: var(--color-accent);
            color: #fff;
        }

        /* Contenido principal */
        .main-content {
            background: var(--color-bg);
            min-height: calc(100vh - 56px);
        }
        h1, h2, h3, h4, h5, h6 {
            color: var(--color-text);
        }
        .text-muted {
            color: var(--color-text-muted) !important;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @yield('content')
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>