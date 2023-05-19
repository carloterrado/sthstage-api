<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CatalogResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CatalogController extends Controller
{
    public function tires()
    {
        $response = DB::table('channels')->select('carriers')->get();
        echo '<pre>';
        print_r($response);
        die;
        ini_set('max_execution_time', 500);
   
        return new StreamedResponse(function () {
            $pageSize = 200; // Number of records to process per chunk
            $offset = 0;
            $totalRecords = DB::table('catalog')
                ->where('category', 2)
                ->count();

            $responseCallback = function () use ($pageSize, &$offset) {
                $data = DB::table('catalog')
                    ->where('category', 1)
                    ->orderBy('id')
                    ->offset($offset)
                    ->limit($pageSize)
                    ->get();

                $catalogResource = CatalogResource::collection($data);
                echo $catalogResource->toResponse(request())->getContent();

                $offset += $pageSize;
            };

            while (true) {
                $responseCallback();
                ob_flush();
                flush();

                if ($offset >= $totalRecords) { // Determine the total number of records
                    break;
                }
            }
        });
    }

    public function tiresBySize(Request $request)
    {
        $data = DB::table('catalog')
        ->where(['category' => 1, 'full_size' => $request->full_size])
        // ->select('id','unq_id','category')
        ->get();

        return CatalogResource::collection($data);
    }
    public function tiresByBrand(Request $request)
    {
        $data = DB::table('catalog')
        ->where(['category' => 1, 'brand' => $request->brand]) 
        // ->select('id','unq_id','category')
        ->get();

        return CatalogResource::collection($data);
    }
    public function wheels()
    {
        return new StreamedResponse(function () {
            $pageSize = 200; // Number of records to process per chunk
            $offset = 0;
            $totalRecords = DB::table('catalog')
                ->where('category', 2)
                ->count();

            $responseCallback = function () use ($pageSize, &$offset) {
                $data = DB::table('catalog')
                    ->where('category', 2)
                    ->orderBy('id')
                    ->offset($offset)
                    ->limit($pageSize)
                    ->get();

                $catalogResource = CatalogResource::collection($data);
                echo $catalogResource->toResponse(request())->getContent();

                $offset += $pageSize;
            };

            while (true) {
                $responseCallback();
                ob_flush();
                flush();

                if ($offset >= $totalRecords) { // Determine the total number of records
                    break;
                }
            }
        });
    }

    public function wheelsBySize(Request $request)
    {
        $data = DB::table('catalog')
        ->where(['category' => 2, 'size_dimensions' => $request->size_dimensions])
        // ->select('id','unq_id','category')
        ->get();

        return CatalogResource::collection($data);
    }
    public function wheelsByBrand(Request $request)
    {
        $data = DB::table('catalog')
        ->where(['category' => 2, 'brand' => $request->brand])
        // ->select('id','unq_id','category')
        ->get();

        return CatalogResource::collection($data);
    }
}
