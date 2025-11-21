<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class ProcessFilesJob implements ShouldQueue
{
    use Queueable;

    private $file_path;
    private $file_id;
    private $extension;

    public function __construct($file_path, $file_id, $extension)
    {
        $this->file_path = $file_path;
        $this->file_id = $file_id;
        $this->extension = $extension;
    }


    public function handle(): void
    {
        // Seleciona o leitor apropriado com base na extensão do arquivo
        switch (strtolower($this->extension)) {
            case 'xlsx':
                $reader = new Xlsx();
                break;
            case 'xls':
                $reader = new Xls();
                break;
            case 'csv':
                $reader = new Csv();
                break;
            default:
                throw new \Exception('Formato de arquivo não suportado.');
        }

        try {
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($this->file_path);
            $sheet = $spreadsheet->getActiveSheet();
            $row_count = $sheet->getHighestRow();
            $data = [];

            if($row_count > 1500){
                $chunk_size = $row_count / 25;
            } else {
                $chunk_size = 1500;
            }

            $headers = ['file_id'];

            // Extraindo os headers para posterior inserção no banco
            foreach($sheet->getRowIterator(2, 2) as $row){
                $rowData = [];
                foreach($row->getCellIterator() as $cell){
                    $headers[] = $cell->getValue();
                }
            }

            $count = 0;

            // Itera sobre as linhas do arquivo e destina os dados para jobs de processamento em chunk
            foreach($sheet->getRowIterator(3) as $row){
                $rowData = [];
                foreach($row->getCellIterator() as $cell){
                    $rowData[] = $cell->getValue();
                }
                $row_data = array_merge([$this->file_id], $rowData);
                $data[] = array_combine($headers, $row_data);
                $count++;

                if($count >= $chunk_size){
                    ProcessChunkJob::dispatch($data);
                    $data = [];
                    $count = 0;
                }
            }

            // Processa dados restantes
            if(!empty($data)){
                ProcessChunkJob::dispatch($data);
            }
        }
        catch (\Exception $e) {
            throw $e;
        }
    }
}
