<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CatalogInventoryPriceResource;
use App\Http\Resources\V1\CatalogResource;
use App\Http\Resources\V1\CatalogTireResource;
use App\Http\Resources\V1\CatalogWheelResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CatalogController extends Controller
{
    public function tireBrand()
    {
        $data = DB::connection('tire_connect_api')
            ->table('catalog')
            ->where('category', 1)
            ->selectRaw('MAX(brand_id) AS brand_id, COALESCE(brand, "") AS brand')
            ->groupBy('brand')
            ->get();
        return response()->json(['data' => $data]);
    }

    public function wheelBrand()
    {
        $data = DB::connection('tire_connect_api')
            ->table('catalog')
            ->where('category', 2)
            ->selectRaw('MAX(brand_id) AS brand_id, COALESCE(brand, "") AS brand')
            ->groupBy('brand')
            ->get();
        return response()->json(['data' => $data]);
    }


    // 000P-51061-12
    public function getCatalog(Request $request)
    {
        if (!$request->has('brand') && !$request->has('mspn')) {
            return response()->json([
                'error' => 'Missing Parameter',
                'message' => 'At least one parameter of brand or mspn is required.'
            ], 400);
        }

        if ($request->has('section_width') && $request->has('aspect_ratio') && $request->has('rim_diameter')) {
            $data = DB::connection('tire_connect_api')->table('catalog')
                ->where([
                    'section_width' => $request->section_width,
                    'aspect_ratio' => $request->aspect_ratio,
                    'rim_diameter' => $request->rim_diameter
                ])
                ->when($request->has('brand'), function ($query) use ($request) {
                    $query->where('brand', $request->brand);
                })
                ->when($request->has('mspn'), function ($query) use ($request) {
                    $query->where('mspn', $request->mspn);
                })
                ->get();
            return CatalogTireResource::collection($data);
        }

        if ($request->has('wheel_diameter') && $request->has('wheel_width')) {
            $data = DB::connection('tire_connect_api')->table('catalog')
                ->where([
                    'wheel_diameter' => $request->wheel_diameter,
                    'wheel_width' => $request->wheel_width
                ])
                ->when($request->has('brand'), function ($query) use ($request) {
                    $query->where('brand', $request->brand);
                })
                ->when($request->has('mspn'), function ($query) use ($request) {
                    $query->where('mspn', $request->mspn);
                })
                ->get();
            return CatalogWheelResource::collection($data);
        }

        if ((!$request->has('wheel_diameter') || !$request->has('wheel_width')) || (!$request->has('section_width') || !$request->has('aspect_ratio') || !$request->has('rim_diameter'))) {
            return response()->json([
                'error' => 'Missing Parameter',
                'message' => 'Required size parameters'
            ], 400);
        }
    }

    public function tireInventoryPrice(Request $request)
    {
        if (!$request->has('brand') && !$request->has('mspn')) {
            return response()->json([
                'error' => 'Missing Parameter',
                'message' => 'At least one parameter of brand or mspn is required.'
            ], 400);
        }


        if ($request->has('brand') || $request->has('mspn')) {

            $data = DB::connection('tire_connect_api')
                ->table('inventory_feed AS i')
                ->when($request->has('brand'), function ($query) use ($request) {
                    $query->where('n.brand', $request->brand);
                })
                ->select(
                    'n.id',
                    'n.brand',
                    'n.mspn',
                    'n.vendor',
                    'v.name',
                    'n.netnet',
                    'i.qty',
                )

                ->when($request->has('mspn'), function ($query) use ($request) {
                    $query->where('n.mspn', $request->mspn);
                })
                ->join('netnet_price AS n', 'n.mspn', '=', 'i.part_number')
                ->join('vendor_main AS v', 'v.id', '=',  'n.vendor')
                ->distinct()
                ->get();

            // return $data;
            return CatalogInventoryPriceResource::collection($data);
        }



        // return response()->json(['data' => $data]);
    }
}
