<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFileRequest;
use App\Models\File;
use App\Models\FileData;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Jobs\ProcessFilesJob;

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
        Storage::disk('public')->put($path, file_get_contents($file->path()));

        //Colocar dentro de uma transaction
        try {

            DB::transaction(function () use ($file, $path, $hash) {
                $file_model = File::create([
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'file_hash' => $hash,
                ]);

                ProcessFilesJob::dispatch(Storage::disk('public')->path($path), $file_model->id);
            });

        } catch (\Exception $e) {
            throw $e;
            return response()->json(['message' => 'Erro ao processar o arquivo.'], 500);
        }


        return response()->json(['message' => 'Arquivo enviado com sucesso!'], 200);

    }

    public function searchContent(Request $request)
    {
        $tckrSymb = $request->input('TckrSymb');
        $rptDt = $request->input('RptDt');

        if(!$tckrSymb && !$rptDt){
            $result = FileData::select('RptDt', 'TckrSymb', 'MktNm', 'SctyCtgyNm', 'ISIN', 'CrpnNm')->paginate(1000);
        }

        $result = FileData::select('RptDt', 'TckrSymb', 'MktNm', 'SctyCtgyNm', 'ISIN', 'CrpnNm')->where('TckrSymb', $tckrSymb)->orWhere('RptDt', $rptDt)->paginate(1000);

        return response()->json($result, 200);
    }
}
