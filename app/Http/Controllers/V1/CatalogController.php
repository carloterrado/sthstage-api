<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CatalogResource;
use App\Http\Resources\V1\CatalogTireResource;
use App\Http\Resources\V1\CatalogVendorLocationResource;
use App\Http\Resources\V1\CatalogWheelResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CatalogController extends Controller
{

    public function getWheels(Request $request)
    {
        if ((!$request->has('wheel_diameter') && !$request->has('wheel_width')) && ($request->has('brand') || $request->has('mspn'))) {
            $data = DB::connection('tire_connect_api')->table('catalog')
                ->where(['category' => 2])
                ->when($request->has('brand'), function ($query) use ($request) {
                    $query->where('brand', $request->brand);
                })
                ->when($request->has('mspn'), function ($query) use ($request) {
                    $query->where('mspn', $request->mspn);
                })
                ->get();

            return CatalogWheelResource::collection($data);
        }

        if ($request->has('wheel_diameter') && $request->has('wheel_width')) {
            $data = DB::connection('tire_connect_api')->table('catalog')
                ->where([
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
        if ((!$request->has('section_width') && !$request->has('aspect_ratio') && !$request->has('rim_diameter')) && ($request->has('brand') || $request->has('mspn'))) {
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
            return CatalogTireResource::collection($data);
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


        return response()->json([
            'error' => 'Missing Parameter',
            'message' => 'The required parameters for tires are missing in the request.'
        ], 400);
    }

    public function getLocation(Request $request)
    {
        //kuha partnumber sa inventory feed
        if ($request->has('part_number')) {
            $data = DB::connection('tire_connect_api')
                ->table('inventory_feed AS i')
                ->select(
                    'v.id',
                    'v.short_code',
                    'v.name',
                    'v.email',
                    'v.vast_vendor_number',
                    'i.store_location_id',
                    's.addr',
                    's.city',
                    's.state',
                    's.zip_code',
                    's.lat',
                    's.lon',
                    's.phone',
                    's.cut_off'
                )
                ->join('vendor_main AS v', 'v.id', '=', 'i.vendor_main_id')
                ->join('store_location AS s', 's.id', '=', 'i.store_location_id')
                ->where('i.part_number', '=', $request->get('part_number'))
                ->get();

            return CatalogVendorLocationResource::collection($data);
        } else {
            return response()->json([
                'error' => 'Missing Parameter',
                'message' => 'At least one parameter of brand or mspn is required.'
            ], 400);
        }


        //api/v1/catalog/tires/location?part_number={part_number} 
        //hanapin yung vendor main id sa vendor main tas kunin yung id, name, email, vast vendor


        //hanapin yung vendor main id sa store location
    }
    public function getVehicleByMakes(Request $request)
    {

        // $token = "bdd7a30c-7c2e-4982-a236-fa37e0e6dede";

        $requestYear = [
            'Year' => $request->year

        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetMakes?Token=" . $request->token, $requestYear);

        return $responseData = $response->json();

        $makeNames = array_map(function ($make) {
            return ['VehicleMakeName' => $make['VehicleMakeName']];
        }, $responseData['Makes']);

        return response()->json(['Makes' => $makeNames]);
    }


    public function getVehicleByModels(Request $request)
    {

        // $token = "bdd7a30c-7c2e-4982-a236-fa37e0e6dede";

        $requestYear = [
            'Year' => $request->year,
            'VehicleMake' => $request->makes

        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetModels?Token=" . $request->token, $requestYear);

        return $responseData = $response->json();

        $makeNames = array_map(function ($make) {
            return ['VehicleMakeName' => $make['VehicleMakeName']];
        }, $responseData['Makes']);

        return response()->json(['Makes' => $makeNames]);
    }

    public function getVehicleOption(Request $request)
    {

        $requestOption = [
            'Year' => $request->Year,
            'VehicleModel' => $request->VehicleModel
        ];

           return Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("https://api.ridestyler.net/Vehicle/GetConfigurations?Token=" . $request->token, $requestOption)->json();

    }
}