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

        $catalog_key = CatalogSettings::where('is_show', 1)->pluck('catalog_key');
        if ((!$request->has('wheel_diameter') && !$request->has('wheel_width')) && ($request->has('brand') || $request->has('mspn'))) {
            // return $catalog_key;
            $data = DB::connection('tire_connect_api')->table('catalog')
                ->select(...$catalog_key)
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
            $data = DB::connection('tire_connect_api')->table('catalog')
                ->select(...$catalog_key)
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
        $catalog_key = CatalogSettings::where('is_show', 1)->pluck('catalog_key');   
        if ((!$request->has('section_width') && !$request->has('aspect_ratio') && !$request->has('rim_diameter')) && ($request->has('brand') || $request->has('mspn'))) {
            $data = DB::connection('tire_connect_api')
                ->table('catalog')
                ->select(...$catalog_key)
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
            $data = DB::connection('tire_connect_api')->table('catalog')
                ->select(...$catalog_key)
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
        if (!$request->has('mspn')) {
            return response()->json([
                'error' => 'Missing Parameter',
                'message' => 'mspn is required.'
            ], 400);
        }


        if ($request->has('brand') || $request->has('mspn')) {

            $data = DB::connection('tire_connect_api')
                ->table('inventory_feed AS i')
                ->when($request->has('brand'), function ($query) use ($request) {
                    $query->where('n.brand', $request->brand);
                })
                ->select(
                    'i.id',
                    'n.brand',
                    'n.mspn',
                    'i.vendor_main_id',
                    'v.name',
                    'n.netnet',
                    'i.qty',
                    's.city',
                    's.state',
                )

                ->when($request->has('mspn'), function ($query) use ($request) {
                    $query->where('n.mspn', $request->mspn);
                })
                ->join('netnet_price AS n', 'n.mspn', '=', 'i.part_number')
                ->join('vendor_main AS v', 'v.id', '=',  'i.vendor_main_id')
                ->join('store_location AS s', 's.vendor_main_id', '=', 'i.vendor_main_id')
                ->distinct()
                ->get();


            //Many Location
            $combinedData = [];

            foreach ($data as $item) {
                $existingItem = array_filter($combinedData, function ($value) use ($item) {
                    return $value['id'] === $item->id && $value['brand'] === $item->brand && $value['mspn'] === $item->mspn;
                });

                if (!empty($existingItem)) {
                    $index = key($existingItem);
                    $combinedData[$index]['location'][] = [
                        'vendor' => $item->vendor_main_id,
                        'vendor_name' => $item->name . ' ' . '-' . ' ' . $item->city . ',' . $item->state,
                        'price' => $item->netnet,
                        'qty' => $item->qty
                    ];
                } else {
                    $combinedData[] = [
                        'id' => $item->id,
                        'brand' => $item->brand,
                        'mspn' => $item->mspn,
                        'location' => [
                            [
                                'vendor' => $item->vendor_main_id,
                                'vendor_name' => $item->name . ' ' . '-' . ' ' . $item->city . ',' . $item->state,
                                'price' => $item->netnet,
                                'qty' => $item->qty
                            ]
                        ]
                    ];
                }
            }

            return $combinedData;
            // return CatalogInventoryPriceResource::collection($combinedData);

        }


   
    }


    public function getLocation(Request $request)
    {
        //kuha partnumber sa inventory feed
        if ($request->has('part_number')) {
            $data = DB::connection('tire_connect_api')
            ->table('inventory_feed AS i')
            ->select('v.id', 'v.short_code', 'v.name', 'v.email')
            ->selectRaw('GROUP_CONCAT(CONCAT_WS(" ", v.name, s.city, s.state)) AS store_locations')
            ->join('vendor_main AS v', 'v.id', '=', 'i.vendor_main_id')
            ->join('store_location AS s', 's.id', '=', 'i.store_location_id')
            ->where('i.part_number', '=', $request->get('part_number'))
            ->groupBy('v.id', 'v.short_code', 'v.name', 'v.email')
            ->get();

            return CatalogVendorLocationResource::collection($data);
        } else {
            return response()->json([
                'error' => 'Missing Parameter',
                'message' => 'At least one parameter of brand or mspn is required.'
            ], 400);
        }
    }
 

    
    public function getVehicleYear(Request $request){

        $requestYear = [
            'YearMin' => $request->YearMin,
            'YearMax' => $request->YearMax
        ];


        return Http::withHeaders(['Content-Type' => 'application/json'])
        ->post("https://api.ridestyler.net/Vehicle/GetYears?Token=" . $this->vehicleToken, $requestYear)
        ->json();

    }


    public function getVehicleByMakes(Request $request)
    {
        $requestYear = [
            'Year' => $request->Year
        ];

        return Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetMakes?Token=" . $this->vehicleToken, $requestYear)->json();
       
    }


    public function getVehicleByModels(Request $request)
    {
        $requestYear = [
            'Year' => $request->Year,
            'VehicleMake' => $request->VehicleMake

        ];

        return Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetModels?Token=" . $this->vehicleToken, $requestYear)->json();
  
    }


    public function getVehicleConfigurations(Request $request)
    {

        $requestOption = [
            'Year' => $request->Year,
            'VehicleModel' => $request->VehicleModel
        ];

           return Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("https://api.ridestyler.net/Vehicle/GetConfigurations?Token=" . $this->vehicleToken, $requestOption)->json();

    }


    public function getTireOptionDetails(Request $request)
    {

        $requestOption = [
            'VehicleConfiguration' => $request->VehicleConfiguration,
        ];

        return Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetTireOptionDetails?Token=" . $this->vehicleToken, $requestOption)->json();
   
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
       $orderStatus = DB::connection('tire_connect_api')->table('orderList')
       ->select('orderList.po_number', 'orderStatus.status')
       ->where(['orderList.po_number' => $request->po_number, 'orderList.user_id' => $request->user_id])
       ->leftJoin('orderStatus', 'orderList.order_status_id', '=', 'orderStatus.id')
       ->get();
       
        return response()->json($orderStatus);
    }

    


}