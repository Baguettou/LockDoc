@extends('layouts.app')

@section('content')

<!-- Alerta de compra realizada con éxito -->
@if (session('products-nologin-alert'))
    <div id="products-nologin-alert" class="alert alert-danger m-4 alert-dismissible fade show" role="alert">
        {{ session('products-nologin-alert') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
        $(document).ready(function(){
            // Mostrar alerta con fade
            $("#products-nologin-alert").fadeIn();

            // Ocultar la alerta después de 4 segundos con fade
            setTimeout(function() {
                $("#products-nologin-alert").fadeOut();
            }, 4000);
        });
    </script>
@endif

    <div class="row m-5">
        <div class="col-lg-6">
            <div id="carouselAutoplaying" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="{{ asset('imgs/1.png') }}" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="{{ asset('imgs/2.png') }}" class="d-block w-100" alt="...">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselAutoplaying" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselAutoplaying" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        <div class="col m-4 d-flex flex-column align-items-center justify-content-center">
            <h1 class="text-center">¡Bienvenid@ a LockDock!</h1>
            <p class="text-lg text-center">
                Aquí puedes subir y acceder de manera <strong>segura</strong> a tus documentos PDF sensibles. Utilizamos Auth0 para autenticación, contamos con encriptación de datos propia y una interfaz intuitiva para una experiencia confiable y fácil de usar. ¡Explora la seguridad en la gestión de documentos con nosotros!            
            </p>
        </div>
    </div>

    <div class="col m-5">
        <div class="col-lg-12">
            <h3>¿Cómo empezó LockDock?</h3>
            <p class="text">
                LockDock surgió como respuesta a la creciente necesidad de proporcionar a los usuarios una plataforma segura para la gestión de documentos PDF sensibles. En un entorno donde la privacidad y la seguridad de la información son de suma importancia, se identificó una problemática significativa: la ausencia de un lugar confiable donde los usuarios pudieran almacenar y acceder a sus documentos de manera segura. Ante esta situación, se decidió crear LockDock con el objetivo de ofrecer una solución integral y confiable.
            </p>
            <p class="text">
                LockDock se creó utilizando las últimas tecnologías en seguridad informática, como Auth0 para la autenticación y técnicas avanzadas de encriptación de datos. El proceso de desarrollo se llevó a cabo con un enfoque centrado en el usuario, priorizando la facilidad de uso y la seguridad en cada etapa del diseño y la implementación.
            </p>
        </div>
    </div>
    
@endsection