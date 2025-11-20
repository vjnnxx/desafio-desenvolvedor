<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFileRequest;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class FileController extends Controller
{
    //Busca arquivos por nome ou data de envio
    public function search(Request $request)
    {
        $name = $request->input('name');
        $date = $request->input('date');

        $result = File::select('filename', DB::raw('DATE_FORMAT(created_at,"%Y-%m-%d") as upload_date'))->whereDate('created_at', $date)->orWhere('filename', $name)->get();

        if($result->isEmpty()){
            return response()->json(['message' => 'Nenhum arquivo encontrado!'], 404);
        }

        return response()->json($result, 200);
    }

    // Salva o arquivo enviado e registra no banco de dados
    public function upload(UploadFileRequest $request)
    {
        $file = $request->file('file');
        $hash = hash_file('sha256', $file->path());

        //Verifica se o arquivo já foi enviado anteriormente
        $existingFile = File::where('file_hash', $hash)->first();
        if ($existingFile) {
            return response()->json(['message' => 'Arquivo repetido!'], 403);
        }
        $path = 'uploads/'.$file->getClientOriginalName();
        Storage::disk('public')->put($path, $file);

        File::create([
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'file_hash' => $hash,
        ]);

        return response()->json(['message' => 'Arquivo enviado com sucesso!'], 200);

    }
}
