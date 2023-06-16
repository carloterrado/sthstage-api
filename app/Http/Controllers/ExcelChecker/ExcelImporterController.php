<?php

namespace App\Http\Controllers\ExcelChecker;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImporterController extends Controller
{
    public function index(Request $request)
    {
        $rows = DB::table('catalog')->limit(10)->paginate(10);
        // dd($rows);
        if (empty($rows)) {
            return view('view',  ['empty' => 'The Database is empty.']);
        } else {
            return view('view', ['rows' => $rows]);
        }
    }
    public function Import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:csv,xls,xlsx'
        ]);
        $file = $request->file('excel_file');
        $filePath = $file->getPathname();

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();


        $rows = $worksheet->toArray();
        $worksheet = $rows[0];
        $dataRows = array_slice($rows, 1);
        $collection = collect($dataRows);

        $result = $collection->map(function ($row) use ($worksheet) {
            return array_combine($worksheet, $row);
        });

        $chunks = $result->chunk(10);
        // $chunk = $results->chunk(10);
        // dd($chunk);

        // dd($chunks);

        foreach ($chunks as $chunk) {
            $chunk->map(function ($row) use ($worksheet) {
                return array_combine($worksheet, $row);
            })->each(function ($row) {
                $primaryKey = ['brand' => $row['brand'], 'mspn' => $row['mspn']];
                Catalog::updateOrCreate($primaryKey, $row);
            });
        // dd($chunk);

        }

        return redirect()->back()->with(['rows' => $rows]);
    }
}
