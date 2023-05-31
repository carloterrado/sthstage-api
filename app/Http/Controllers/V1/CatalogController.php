<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CatalogResource;
use App\Http\Resources\V1\CatalogTireResource;
use App\Http\Resources\V1\CatalogWheelResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CatalogController extends Controller
{
   
    public function getWheels(Request $request)
    {
        if ((!$request->has('wheel_diameter') && !$request->has('wheel_width')) && ($request->has('brand') || $request->has('mspn'))) {
            $data = DB::connection('tire_connect_api')->table('catalog')
                ->where([
                    'category' => 2,
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

        if (($request->has('wheel_diameter') && $request->has('wheel_width')) && (!$request->has('brand') || !$request->has('mspn'))) {
            $data = DB::connection('tire_connect_api')->table('catalog')
                ->where([
                    'category' => 2,
                    'wheel_diameter' => $request->wheel_diameter,
                    'wheel_width' => $request->wheel_width,
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

        return response()->json([
            'error' => 'Missing Parameter',
            'message' => 'The required parameters for wheels are missing in the request.'
        ], 400);
    }

    public function getTires(Request $request)
    {
        if((!$request->has('section_width') && !$request->has('aspect_ratio') && !$request->has('rim_diameter')) && ($request->has('brand') || $request->has('mspn'))){
            $data = DB::connection('tire_connect_api')
            ->table('catalog')
            ->where(['category' => 1])
            ->when($request->has('brand'), function ($query) use ($request) {
                $query->where('brand', $request->brand);
            })
            ->when($request->has('mspn'), function ($query) use ($request) {
                $query->where('mspn', $request->mspn);
            })
            ->get();
        return response()->json(['data' => $data]);
        }

        if($request->has('section_width') && $request->has('aspect_ratio') && $request->has('rim_diameter')){
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

        if(!$request->has('brand') && !$request->has('mspn') && !$request->has('section_width') && !$request->has('aspect_ratio') && !$request->has('rim_diameter')){
            return response()->json([
                'error' => 'Missing Parameter',
                'message' => 'Required parameters'
            ], 400);
        }else{
            return response()->json([
                'error' => 'Missing Parameter',
                'message' => 'Missing size parameters'
            ], 400);
        }
    }
}
