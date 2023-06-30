<?php

namespace App\Jobs;

use App\Models\Catalog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\File;

class ExcelQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $tries = 3;

    /**
     * The path of the batch chunks.
     *
     * @var array
     */
    protected $batchChunks;

    /**
     * Create a new job instance.
     *
     * @param  array  $batchChunks
     * @return void
     */
    public function __construct(array $batchChunks)
    {
        $this->batchChunks = $batchChunks;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '10G');

        foreach ($this->batchChunks as $chunkPath) {
            $chunkSpreadsheet = IOFactory::load($chunkPath);
            $chunkWorksheet = $chunkSpreadsheet->getActiveSheet();
            $requiredColumns = ['brand', 'category'];

            $rows = $chunkWorksheet->toArray();
            $headerRow = array_shift($rows); // Remove the header row from the rows array

            foreach ($rows as $row) {
                $data = array_combine($headerRow, $row);
                if ($this->validateRequiredColumns($data, $requiredColumns)) {
                    $existingRow = Catalog::where('brand', $data['brand'])
                        ->where('mspn', $data['mspn'])
                        ->first();

                    if ($existingRow) {
                        // Row already exists in the database
                        $shouldUpdate = false;
                        foreach ($requiredColumns as $column) {
                            if ($data[$column] != $existingRow->$column) {
                                // Updated data found in one of the required columns, update the row
                                $shouldUpdate = true;
                                break;
                            }
                        }
                        if (!$shouldUpdate) {
                            // No updates found in required columns, skip this row
                            continue;
                        }
                    }

                    $primaryKey = ['brand' => $data['brand'], 'mspn' => $data['mspn']];
                    Catalog::updateOrCreate($primaryKey, $data);
                }
            }
            File::delete($chunkPath);
        }
    }

    /**
     * Validate the presence of required columns in the data.
     *
     * @param  array  $data
     * @param  array  $requiredColumns
     * @return bool
     */
    private function validateRequiredColumns($data, $requiredColumns)
    {
        foreach ($requiredColumns as $column) {
            if (empty($data[$column])) {
                return false;
            }
        }
        return true;
    }
}
