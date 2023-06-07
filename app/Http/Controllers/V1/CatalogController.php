<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CatalogInventoryPriceResource;
use App\Http\Resources\V1\CatalogResource;
use App\Http\Resources\V1\CatalogTireResource;
use App\Http\Resources\V1\CatalogVendorLocationResource;
use App\Http\Resources\V1\CatalogWheelResource;
use App\Models\CatalogSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CatalogController extends Controller
{
    protected $vehicleToken;

    public function __construct()
    {
        $credential = [
            'Username' => 'ejay@atvtireinc.com',
            'Password' => 'palekey67'
        ];
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Auth/Start", $credential);

        $this->vehicleToken = $response->json('Token');
    }


    public function getWheels(Request $request)
    {


        if ((!$request->has('wheel_diameter') && !$request->has('wheel_width')) && ($request->has('brand') || $request->has('mspn'))) {
            // return $catalog_key;
            $data = DB::table('catalog')

                ->where(['category' => 2])
                ->when($request->has('brand'), function ($query) use ($request) {
                    $query->where('brand', $request->brand);
                })
                ->when($request->has('mspn'), function ($query) use ($request) {
                    $query->where('mspn', $request->mspn);
                })
                ->get();

            return response()->json($data);
        }

        if ($request->has('wheel_diameter') && $request->has('wheel_width')) {
            $data = DB::table('catalog')
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
            return response()->json($data);
        }

        return response()->json([
            'error' => 'Missing Parameter',
            'message' => 'The required parameters for wheels are missing in the request.'
        ], 400);
    }


    public function getTires(Request $request)
    {
       
        if ((!$request->has('section_width') && !$request->has('aspect_ratio') && !$request->has('rim_diameter')) && ($request->has('brand') || $request->has('mspn'))) {
            $data = DB::table('catalog')
                ->where(['category' => 1])
                ->when($request->has('brand'), function ($query) use ($request) {
                    $query->where('brand', $request->brand);
                })
                ->when($request->has('mspn'), function ($query) use ($request) {
                    $query->where('mspn', $request->mspn);
                })
                ->get();
            return response()->json($data);
        }

        if ($request->has('section_width') && $request->has('aspect_ratio') && $request->has('rim_diameter')) {
            $data = DB::table('catalog')
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
            return response()->json($data);
        }


        return response()->json([
            'error' => 'Missing Parameter',
            'message' => 'The required parameters for tires are missing in the request.'
        ], 400);
    }

    //Get inventory price location
    public function inventoryPrice(Request $request)
    {
        if ($request->has('brand') && $request->has('mspn')) {

            $data = DB::table('inventory_feed AS i')
            ->select(
                'i.brand',
                'i.part_number',
                'i.vendor_main_id',
                'i.store_location_id',
                'n.netnet',
                'i.qty',
            )
            ->join('netnet_price AS n', function ($join) {
                $join->on('n.brand', '=', 'i.brand')
                    ->on('n.mspn', '=', 'i.part_number')
                    ->on('n.vendor', '=', 'i.vendor_main_id');
            })
            ->join(DB::raw('(SELECT MIN(id) as min_id FROM netnet_price GROUP BY brand, mspn, vendor) AS sub'), function ($join) {
                $join->on('n.id', '=', 'sub.min_id');
            })
            ->where('i.part_number', $request->mspn)
            ->where('i.brand', $request->brand)
            ->get();

        } else {
            return response()->json([
                'error' => 'Missing Parameter',
                'message' => 'mspn is required.'
            ], 400);
        }


        return  CatalogInventoryPriceResource::make($data);
    }


    public function getLocation(Request $request)
    {
        if($request->has('location_id')){
            $data = DB::table('store_location')
            ->where('id', $request->get('location_id'))
            ->get();
        } else {
            $data = DB::table('store_location')
            ->get();
        }

        return CatalogVendorLocationResource::collection($data);
    }

    
    public function getVehicleYear(){

        return Http::withHeaders(['Content-Type' => 'application/json'])
        ->post("https://api.ridestyler.net/Vehicle/GetYears?Token=" . $this->vehicleToken)
        ->json();

    }


    public function getVehicleByMakes(Request $request)
    {
        $requestYear = [
            'Year' => $request->year
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetMakes?Token=" . $this->vehicleToken, $requestYear)
            ->json();

        $makes = collect($response['Makes'])->map(function ($make) {
            return [
                'VehicleMakeID' => $make['VehicleMakeID'],
                'VehicleMakeName' => $make['VehicleMakeName']
            ];
        });

        return response()->json(['Makes' => $makes]);
    }


    public function getVehicleByModels(Request $request)
    {
        $requestYear = [
            'Year' => $request->year,
            'VehicleMake' => $request->vehicleMake

        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetModels?Token=" . $this->vehicleToken, $requestYear)->json();

        $models = collect($response['Models'])->map(function ($model) {
            return [
                'VehicleModelID' => $model['VehicleModelID'],
                'VehicleModelName' => $model['VehicleModelName']
            ];
        });

        return response()->json(['Models' => $models]);
    }


    public function getVehicleConfigurations(Request $request)
    {

        $requestOption = [
            'Year' => $request->year,
            'VehicleModel' => $request->vehicleModel
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetConfigurations?Token=" . $this->vehicleToken, $requestOption)->json();

        $options = collect($response['Configurations'])->map(function ($model) {
            return [
                'VehicleOptionID' => $model['VehicleConfigurationID'],
                'VehicleOptionName' => $model['VehicleConfigurationName']
            ];
        });

        return response()->json(['Options' => $options]);
    }


    public function getTiresByVehicle(Request $request)
    {

        $requestOption = [
            'VehicleConfiguration' => $request->vehicleOptionID,
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetTireOptionDetails?Token=" . $this->vehicleToken, $requestOption)->json();

        $details = collect($response['Details'])->map(function ($detail) {
            return [
                'Width' => $detail['Front']['Width'],
                'AspectRatio' => $detail['Front']['AspectRatio'],
                'InsideDiameter' => $detail['Front']['InsideDiameter'],
            ];
        });
        $data = DB::table('catalog')
            ->whereIn('section_width', $details->pluck('Width'))
            ->whereIn('aspect_ratio', $details->pluck('AspectRatio'))
            ->whereIn('rim_diameter', $details->pluck('InsideDiameter'))
            ->when($request->has('brand'), function ($query) use ($request) {
                $query->where('brand', $request->brand);
            })
            ->when($request->has('mspn'), function ($query) use ($request) {
                $query->where('mspn', $request->mspn);
            })
            ->get();

        return response()->json($data);
    }


    public function getBoltPatterns(Request $request)
    {
        $requestOption = [
            'WheelBrand' => $request->WheelBrand,
        ];

        return Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Wheel/GetBoltPatterns?Token=" . $this->vehicleToken, $requestOption)->json();
    }


    public function getFitments(Request $request)
    {
        $requestOption = [
            "VehicleConfiguration" => $request->VehicleConfiguration
        ];
        return Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetFitments?Token=" . $this->vehicleToken, $requestOption)->json();
    }


    public function getOrderStatus(Request $request)
    {
       $orderStatus = DB::table('orderList')
       ->select('orderList.po_number', 'orderStatus.status')
       ->where(['orderList.po_number' => $request->po_number, 'orderList.user_id' => $request->user_id])
       ->leftJoin('orderStatus', 'orderList.order_status_id', '=', 'orderStatus.id')
       ->get();
       
        return response()->json($orderStatus);
    }
}
