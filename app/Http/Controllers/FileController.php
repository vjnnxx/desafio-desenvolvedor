<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFileRequest;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FileController extends Controller
{

    // Salva o arquivo enviado e registra no banco de dados
    public function upload(UploadFileRequest $request)
    {
        $file = $request->file('file');
        $path = 'uploads/'.$file->getClientOriginalName();
        Storage::disk('public')->put($path, $file);

        File::create([
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
        ]);

        return response()->json(['message' => 'Arquivo enviado com sucesso!'], 200);

    }
}
