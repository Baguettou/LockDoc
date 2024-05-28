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
            <button id="openUploadModal" class="btn btn-primary">Subir Documento</button>

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
                            <button id="recoverPasswordButton" class="btn btn-secondary mt-2" disabled>Recuperar Contraseña</button>
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
                <form id="passwordForm" method="POST" action="{{ route('document.download') }}">
                    @csrf
                    <input type="hidden" id="documentId" name="document_id">
                    <div class="form-group">
                        <label for="passwordInput">Contraseña:</label>
                        <input type="password" class="form-control" id="passwordInput" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Aceptar</button>
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
                <h5 class="modal-title" id="uploadModalLabel">Subir Documento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" method="POST" action="{{ route('document.upload') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="documentInput">Documento:</label>
                        <input type="file" class="form-control" id="documentInput" name="documento" required>
                    </div>
                    <div class="form-group">
                        <label for="uploadPasswordInput">Contraseña:</label>
                        <input type="password" class="form-control" id="uploadPasswordInput" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="recoveryQuestion">Pregunta de recuperación:</label>
                        <select class="form-control" id="recoveryQuestion" name="recovery_question" required>
                            <option value="¿Cuál es el nombre de tu primera mascota?">¿Cuál es el nombre de tu primera mascota?</option>
                            <option value="¿Cuál es tu ciudad de nacimiento?">¿Cuál es tu ciudad de nacimiento?</option>
                            <option value="¿Cuál es el nombre de tu mejor amigo en la infancia?">¿Cuál es el nombre de tu mejor amigo en la infancia?</option>
                            <option value="¿Cuál es tu comida favorita?">¿Cuál es tu comida favorita?</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="recoveryAnswer">Respuesta:</label>
                        <input type="text" class="form-control" id="recoveryAnswer" name="recovery_answer" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Subir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para recuperación de contraseña -->
<div class="modal fade" id="recoveryModal" tabindex="-1" role="dialog" aria-labelledby="recoveryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recoveryModalLabel">Recuperar Contraseña</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="recoveryForm" method="POST" action="{{ route('document.recover_password') }}">
                    @csrf
                    <input type="hidden" id="recoveryDocumentId" name="document_id">
                    <div class="form-group">
                        <label for="recoveryAnswerInput">Respuesta a la pregunta de recuperación:</label>
                        <input type="text" class="form-control" id="recoveryAnswerInput" name="recovery_answer" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Recuperar Contraseña</button>
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
        const recoverPasswordButton = document.getElementById('recoverPasswordButton');
        const openUploadModalButton = document.getElementById('openUploadModal');
        let selectedId = null;

        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                const selected = document.querySelector('input[name="selected_document"]:checked');
                const isSelected = !!selected;

                editButton.disabled = !isSelected;
                downloadButton.disabled = !isSelected;
                deleteButton.disabled = !isSelected;
                recoverPasswordButton.disabled = !isSelected;

                if (isSelected) {
                    selectedId = selected.value;

                    editButton.onclick = function() {
                        document.getElementById('editDocumentId').value = selectedId;
                        document.getElementById('editForm').submit();
                    };
                    downloadButton.onclick = function() {
                        document.getElementById('documentId').value = selectedId;
                        $('#passwordModal').modal('show');
                    };
                    deleteButton.onclick = function() {
                        if (confirm('¿Está seguro de que desea eliminar este documento?')) {
                            document.getElementById('deleteDocumentId').value = selectedId;
                            document.getElementById('deleteForm').submit();
                        }
                    };
                    recoverPasswordButton.onclick = function() {
                        document.getElementById('recoveryDocumentId').value = selectedId;
                        $('#recoveryModal').modal('show');
                    };
                }
            });
        });

        // Abrir el modal de carga de documentos
        openUploadModalButton.addEventListener('click', function() {
            $('#uploadModal').modal('show');
        });
    });

</script>

<!-- Formularios ocultos para editar y eliminar -->
<form id="editForm" method="POST" action="{{ route('document.edit') }}">
    @csrf
    <input type="hidden" id="editDocumentId" name="documentId">
</form>
<form id="deleteForm" method="POST" action="{{ route('document.delete') }}">
    @csrf
    <input type="hidden" id="deleteDocumentId" name="documentId">
</form>

@endsection
