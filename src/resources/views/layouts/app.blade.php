<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LockDoc</title>
    <!-- CSS, JavaScript, or other resources -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/@catppuccin/palette/css/catppuccin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="navbar-container container-fluid">
                <a class="ms-4 navbar-brand" href="/"><strong>LockDoc</strong></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    @if(Auth::user())
                        <ul class="me-4 navbar-nav ml-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="/documentos">Documentos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/encriptar">Encriptar</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/desencriptar">Desencriptar</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/logout">Cerrar Sesión</a>
                            </li>
                        </ul>
                    @else
                        <ul class="me-4 navbar-nav ml-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="/login">Iniciar Sesión</a>
                            </li>
                        </ul>
                    @endif
                </div>
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="footer mt-auto py-3">
        <div class="footer-container container pull-left ms-5">
            <p class="m-0"><strong>&copy;2024 LockDoc.</strong></p>
            <p class="m-0"><small>Todos los derechos reservados. LockDoc™ es una marca registrada de <strong>GitChest.</strong></small></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
