<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Optional Bootstrap Icons (for sidebar icons) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
        }
        #sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 250px;
    background-color: #007BFF; /* Changed to blue */
    color: #fff;
    padding-top: 20px;
    transition: all 0.3s ease;
    z-index: 1000;
}

#sidebar a {
    color: #fff;
    text-decoration: none;
    font-size: 18px;
    padding: 10px 20px;
    display: block;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
}

#sidebar a i {
    margin-right: 10px;
    font-size: 20px;
}

#sidebar a:hover {
    background-color: #0056b3; /* Darker blue */
    border-radius: 5px;
}

#sidebar .navbar-brand {
    color: #fff;
    font-size: 22px;
    font-weight: bold;
    margin-bottom: 30px;
    text-align: center;
}

#sidebar .nav-item {
    margin-bottom: 15px;
}

#sidebar .dropdown-menu {
    background-color: #007BFF; /* Blue */
    border: none;
}

        .container-fluid {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
        }

        .alert {
            border-radius: 5px;
        }

        /* Responsive Sidebar */
        @media (max-width: 768px) {
            #sidebar {
                width: 0;
                padding: 0;
            }

            .container-fluid {
                margin-left: 0;
            }

            #sidebar.active {
                width: 250px;
                padding-top: 20px;
            }

            .navbar-toggler {
                display: block;
            }
        }
    </style>
</head>

<body class="d-flex">
    <div id="app" class="d-flex w-100">

        <!-- Sidebar -->
        <div id="sidebar">
            <a class="navbar-brand mb-3" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>

            @auth
            <!-- Sidebar Links for Authenticated Users -->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.index') }}">
                        <i class="bi bi-person-lines-fill"></i> Customer
                    </a>
                </li>
                <!-- Add other authenticated links here -->
            </ul>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('items.index') }}">
                        <i class="bi bi-box-seam"></i> Barang
                    </a>
                </li>
                <!-- Add other authenticated links here -->
            </ul>
            @hasrole('admin')
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('transactions.index') }}">
                        <i class="bi bi-cart4"></i> Transaksi
                    </a>
                </li>
                <!-- Add other authenticated links here -->
            </ul>
            
            @endhasrole
            @endauth

            <!-- Authentication Links -->
            @guest
            @if (Route::has('login'))
            <a class="nav-link" href="{{ route('login') }}">
                <i class="bi bi-box-arrow-in-right"></i> {{ __('Login') }}
            </a>
            @endif

            @if (Route::has('register'))
            <a class="nav-link" href="{{ route('register') }}">
                <i class="bi bi-pencil-square"></i> {{ __('Register') }}
            </a>
            @endif
            @endguest
        </div>

        <!-- Main Content -->
        <div class="container-fluid">
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}" onclick="toggleSidebar()">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Logout Button in Navbar -->
                @auth
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right"></i> {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                @endauth
            </nav>

            <main class="py-4">
                <div class="container mt-4">
                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <!-- Alert Error -->
                    @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <!-- Alert Any Errors (validation) -->
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Toggle Sidebar Script -->
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }
    </script>
</body>

</html> 