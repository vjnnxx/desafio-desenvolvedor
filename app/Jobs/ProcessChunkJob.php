<?php

namespace App\Jobs;

use App\Models\FileData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessChunkJob implements ShouldQueue
{
    use Queueable;


    private $dataChunk;
    public function __construct($dataChunk)
    {
        $this->dataChunk = $dataChunk;
    }

    // Utiliza o chunk de dados para criar entradas no banco
    public function handle(): void
    {
        try{
            foreach($this->dataChunk as $data){
                FileData::create($data);
            }
        } catch (\Exception $e){
            throw $e;
        }
    }
}
