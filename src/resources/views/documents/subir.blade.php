@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            @php
                $user = auth()->user();
                $email = $user ? $user->email : '';

                // Check if the user exists in the local database
                $localUser = DB::table('usuarios')->where('CorreoElectronico', $email)->first();

                if ($localUser) {
                    // echo "<p>User $email exists.</p>";
                } else {
                    $now = now();
                    DB::table('usuarios')->insert([
                        'NombreUsuario' => $user->name,
                        'CorreoElectronico' => $email,
                        'FechaCreacion' => $now,
                    ]);
                    // echo "<p>User $email created in the local database.</p>";
                }
            @endphp
            
            <h1>Subir Documento</h1>
            <form action="{{ route('document.upload') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <input type="file" class="form-control-file" name="documento" accept=".pdf">
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Subir</button>
            </form>

        </div>
    </div>
</div>

@endsection
