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

                if (!$localUser) {
                    $now = now();
                    DB::table('usuarios')->insert([
                        'NombreUsuario' => $user->name,
                        'CorreoElectronico' => $email,
                        'FechaCreacion' => $now,
                    ]);

                    $localUser = DB::table('usuarios')->where('CorreoElectronico', $email)->first();
                }

                // Retrieve documents related to the user
                $userDocuments = DB::table('documentos')->where('UsuarioID', $localUser->id)->get();
            @endphp

            <h1>Subir Documento</h1>
            <a href="/subir">
                <button>Documentos</button>
            </a>

            @if(count($userDocuments) > 0)
                <h2 class="mt-4">Documentos del Usuario:</h2>
                <div class="row">
                    <div class="col-md-8">
                        <ul class="list-group">
                            @foreach($userDocuments as $document)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <input type="radio" class="form-check-input" name="selected_document" value="{{ $document->id }}">
                                        {{ $document->NombreArchivo }} - {{ \Carbon\Carbon::parse($document->FechaCarga)->format('Y-m-d h:i:s A') }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <div class="btn-group-vertical w-100" role="group">
                            <button id="editButton" class="btn btn-primary" disabled>Editar</button>
                            <button id="downloadButton" class="btn btn-success" disabled>Descargar</button>
                            <button id="deleteButton" class="btn btn-danger" disabled>Eliminar</button>
                        </div>
                    </div>
                </div>
            @else
                <p class="mt-4">No hay documentos asociados a este usuario.</p>
            @endif

        </div>
    </div>
</div>

<!-- Modal para la contraseña -->
<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">Ingrese la contraseña</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="passwordForm">
                    <div class="form-group">
                        <label for="passwordInput">Contraseña:</label>
                        <input type="password" class="form-control" id="passwordInput" required>
                    </div>
                    <button type="submit" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal para subir documentos -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Ingrese la contraseña</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadForm">
                    <div class="form-group">
                        <label for="uploadInput">Contraseña:</label>
                        <input type="upload" class="form-control" id="uploadInput" required>
                    </div>
                    <button type="submit" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const radios = document.querySelectorAll('input[name="selected_document"]');
        const editButton = document.getElementById('editButton');
        const downloadButton = document.getElementById('downloadButton');
        const deleteButton = document.getElementById('deleteButton');
        let selectedId = null;

        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                const selected = document.querySelector('input[name="selected_document"]:checked');
                const isSelected = !!selected;

                editButton.disabled = !isSelected;
                downloadButton.disabled = !isSelected;
                deleteButton.disabled = !isSelected;

                if (isSelected) {
                    selectedId = selected.value;

                    editButton.onclick = function() {
                        window.location.href = `{{ url('document/edit') }}/${selectedId}`;
                    };
                    downloadButton.onclick = function() {
                        $('#passwordModal').modal('show');
                    };
                    deleteButton.onclick = function() {
                        if (confirm('¿Está seguro de que desea eliminar este documento?')) {
                            window.location.href = `{{ url('document/delete') }}/${selectedId}`;
                        }
                    };
                }
            });
        });

        // Manejar el envío del formulario del modal
        document.getElementById('passwordForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const password = document.getElementById('passwordInput').value;
            if (password) {
                window.location.href = `{{ url('document/download') }}/${selectedId}?password=${password}`;
            }
        });
    });
</script>

@endsection
