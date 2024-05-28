<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;

class DocumentoController extends Controller
{
    public function upload(Request $request)
    {
        // Validar que se haya subido un archivo
        $request->validate([
            'documento' => 'required|mimes:pdf|max:10240', // Max 10MB
            'password' => 'required|string',
            'recovery_question' => 'required|string',
            'recovery_answer' => 'required|string',
        ]);

        $user = auth()->user();

        // Verificar si el usuario existe en la base de datos local
        $localUser = DB::table('usuarios')->where('CorreoElectronico', $user->email)->first();

        // Encriptar el archivo
        $documentoEncriptado = $this->encryptFile($request->file('documento'), $request->input('password'));

        // Guardar el archivo encriptado
        $documentoPath = 'documentos/' . uniqid() . '.enc';
        Storage::put($documentoPath, $documentoEncriptado);

        // Guardar los datos del documento en la base de datos
        $documentoId = DB::table('documentos')->insertGetId([
            'UsuarioID' => $localUser->id,
            'NombreArchivo' => $request->file('documento')->getClientOriginalName(),
            'FechaCarga' => now(),
            'RutaArchivo' => $documentoPath,
        ]);

        // Guardar la pregunta y respuesta de recuperación
        DB::table('preguntas_recuperacion')->insert([
            'documento_id' => $documentoId,
            'pregunta' => $request->input('recovery_question'),
            'respuesta' => hash('sha256', $request->input('recovery_answer')),
            'contrasenia' => encrypt($request->input('password')),
        ]);

        // Redirigir de vuelta a la URL /documentos
        return redirect('/documentos')->with('success', 'Documento subido correctamente.');
    }

    
    public function recoverPassword(Request $request)
    {
        // Validar los datos del request
        $request->validate([
            'document_id' => 'required|integer',
            'recovery_answer' => 'required|string',
        ]);
    
        // Buscar la pregunta de recuperación en la base de datos
        $preguntaRecuperacion = DB::table('preguntas_recuperacion')->where('documento_id', $request->input('document_id'))->first();
    
        // Verificar si la respuesta es correcta
        if ($preguntaRecuperacion && hash_equals($preguntaRecuperacion->respuesta, hash('sha256', $request->input('recovery_answer')))) {
            $password = decrypt($preguntaRecuperacion->contrasenia);
            return response()->json([
                'success' => true,
                'password' => $password,
                'recovery_question' => $preguntaRecuperacion->pregunta
            ]);
        }
    
        return response()->json([
            'success' => false,
            'message' => 'Respuesta incorrecta. No se puede recuperar la contraseña.'
        ]);
    }

    
    public function getRecoveryQuestion(Request $request)
    {
        // Validar los datos del request
        $request->validate([
            'document_id' => 'required|integer',
        ]);

        // Buscar la pregunta de recuperación en la base de datos
        $preguntaRecuperacion = DB::table('preguntas_recuperacion')->where('documento_id', $request->input('document_id'))->first();

        if ($preguntaRecuperacion) {
            return response()->json([
                'success' => true,
                'recovery_question' => $preguntaRecuperacion->pregunta
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se encontró la pregunta de recuperación para el documento seleccionado.'
        ]);
    }



    public function download(Request $request)
    {
        // Validar que se haya proporcionado el ID del documento y la contraseña
        $request->validate([
            'document_id' => 'required|integer',
            'password' => 'required|string',
        ]);
    
        // Buscar el documento en la base de datos
        $documento = DB::table('documentos')->where('id', $request->input('document_id'))->first();
    
        // Verificar si el documento existe
        if ($documento) {
            // Obtener la ruta del archivo
            $filePath = storage_path('app/' . $documento->RutaArchivo);
    
            // Verificar si el archivo existe en el almacenamiento
            if (file_exists($filePath)) {
                // Obtener la contraseña del request
                $password = $request->input('password');
    
                // Desencriptar el archivo
                $decryptedContent = $this->decryptFile($filePath, $password);
    
                if ($decryptedContent !== false) {
                    // Crear una respuesta HTTP con el contenido desencriptado
                    return response()->streamDownload(function () use ($decryptedContent) {
                        echo $decryptedContent;
                    }, $documento->NombreArchivo);
                } else {
                    // Si la contraseña es incorrecta, redirigir con un mensaje de error
                    return redirect()->back()->with('error', 'Contraseña incorrecta. No se puede descargar el documento.');
                }
            } else {
                return redirect()->back()->with('error', 'Archivo no encontrado en el almacenamiento.');
            }
        }
    
        return redirect()->back()->with('error', 'Documento no encontrado.');
    }

    private function encryptFile($file, $password)
    {
        $fileContent = file_get_contents($file);

        // Método de cifrado
        $method = 'AES-256-CBC';
        // Generar un IV
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));

        // Encriptar el contenido del archivo
        $encryptedContent = openssl_encrypt($fileContent, $method, $password, 0, $iv);
        
        // Guardar el IV junto con el contenido encriptado
        return base64_encode($iv . $encryptedContent);
    }

    private function decryptFile($filePath, $password)
    {
        $fileContent = file_get_contents($filePath);
        $fileContent = base64_decode($fileContent);

        // Método de cifrado
        $method = 'AES-256-CBC';
        // Obtener el tamaño del IV
        $ivLength = openssl_cipher_iv_length($method);

        // Extraer el IV y el contenido encriptado
        $iv = substr($fileContent, 0, $ivLength);
        $encryptedContent = substr($fileContent, $ivLength);

        // Desencriptar el contenido del archivo
        $decryptedContent = openssl_decrypt($encryptedContent, $method, $password, 0, $iv);

        return $decryptedContent;
    }

    public function delete(Request $request)
    {
        $documento = DB::table('documentos')->where('id', $request->input('documentId'))->first();

        if ($documento) {
            Storage::delete($documento->RutaArchivo);

            DB::table('documentos')->where('id', $documento->id)->delete();

            return redirect()->back()->with('success', 'Documento eliminado correctamente.');
        }

        return redirect()->back()->with('error', 'Documento no encontrado.');
    }
}
