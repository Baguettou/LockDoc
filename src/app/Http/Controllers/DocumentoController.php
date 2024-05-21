<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Encryption\Encrypter;

class DocumentoController extends Controller
{
    
    public function upload(Request $request)
    {
        // Validar que se haya subido un archivo
        $request->validate([
            'documento' => 'required|mimes:pdf|max:10240', // Max 10MB
            'password' => 'required|string',
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
        DB::table('documentos')->insert([
            'UsuarioID' => $localUser->id,
            'NombreArchivo' => $request->file('documento')->getClientOriginalName(),
            'FechaCarga' => now(),
            'RutaArchivo' => $documentoPath,
        ]);

        // Redirigir de vuelta a la URL /documentos
        return redirect('/documentos')->with('success', 'Documento subido correctamente.');
    }

    public function edit($id)
    {
        // Aquí puedes implementar la lógica para editar el documento
        // Por ejemplo, podrías redirigir a una vista de edición pasando el documento como parámetro
        $documento = DB::table('documentos')->where('id', $id)->first();
        return view('document.edit', compact('documento'));
    }

    public function download(Request $request, $id)
    {
        // Buscar el documento en la base de datos
        $documento = DB::table('documentos')->where('id', $id)->first();

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


    public function delete($id)
    {
        // Buscar el documento en la base de datos
        $documento = DB::table('documentos')->where('id', $id)->first();

        // Verificar si el documento existe
        if ($documento) {
            // Eliminar el archivo del almacenamiento
            Storage::delete($documento->RutaArchivo);

            // Eliminar el registro de la base de datos
            DB::table('documentos')->where('id', $id)->delete();

            // Redirigir con un mensaje de éxito
            return redirect()->back()->with('success', 'El documento se ha eliminado correctamente.');
        }

        // Si el documento no existe, redirigir con un mensaje de error
        return redirect()->back()->with('error', 'El documento no se puede eliminar.');
    }
}
