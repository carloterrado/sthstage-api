<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CatalogResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CatalogController extends Controller
{
    public function tires(Request $request)
    { 
      
        // Check if any of the parameters exist in the request
        if (!$request->has('size_dimensions') && !$request->has('brand') && !$request->has('mspn')) {
            return response()->json(['error' => 'At least one parameter of size, brand, or sku is required.'], 400);
        }

        $data = DB::table('catalog')
            ->where('category', 1)
            ->when($request->has('size_dimensions'), function ($query) use ($request) {
                $query->where('size_dimensions', $request->size_dimensions);
            })
            ->when($request->has('brand'), function ($query) use ($request) {
                $query->where('brand', $request->brand);
            })
            ->when($request->has('mspn'), function ($query) use ($request) {
                $query->where('mspn', $request->mspn);
            })
            ->get();

        return CatalogResource::collection($data);
    }

    public function wheels(Request $request)
    {
         // Check if any of the parameters exist in the request
         if (!$request->has('size_dimensions') && !$request->has('brand') && !$request->has('mspn')) {
            return response()->json(['error' => 'At least one parameter of size, brand, or sku is required.'], 400);
        }

        $data = DB::table('catalog')
            ->where('category', 2)
            ->when($request->has('size_dimensions'), function ($query) use ($request) {
                $query->where('size_dimensions', $request->size_dimensions);
            })
            ->when($request->has('brand'), function ($query) use ($request) {
                $query->where('brand', $request->brand);
            })
            ->when($request->has('mspn'), function ($query) use ($request) {
                $query->where('mspn', $request->mspn);
            })
            ->get();


        return CatalogResource::collection($data);
    }
}
