<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function upload(Request $request)
    {

        $file = $request->file('file');

        Storage::disk('public')->put('uploads/'.$file->getClientOriginalName(), $file);
        return response()->json(['message' => 'Arquivo enviado com sucesso!'], 200);

    }
}
