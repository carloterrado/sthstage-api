<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CatalogInventoryPriceResource;
use App\Http\Resources\V1\CatalogVendorLocationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Token;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;

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


        $bearerToken = request()->bearerToken();
        
        $tokenId = (new Parser(new JoseEncoder()))->parse($bearerToken)->claims()
            ->all()['jti'];
        $client = Token::find($tokenId)->client;
        $excludeColumns = json_decode($client->catalog_column_settings);
        $additionalColumnsToExclude = ['updated_at', 'created_at'];
        $tableColumns = Schema::getColumnListing('catalog');
      

        if ((!$request->has('section_width') && !$request->has('aspect_ratio') && !$request->has('rim_diameter')) && ($request->has('brand') || $request->has('mspn'))) {
            $data = DB::table('catalog')
            ->where('category', 1)
            ->when($request->has('brand'), function ($query) use ($request) {
                $query->where('brand', $request->brand);
            })
            ->when($request->has('mspn'), function ($query) use ($request) {
                $query->where('mspn', $request->mspn);
            })
            ->select(array_diff($tableColumns,array_merge($excludeColumns, $additionalColumnsToExclude)))
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
                ->select(array_diff($tableColumns,array_merge($excludeColumns, $additionalColumnsToExclude)))
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
        if ($request->has('location_id')) {
            $data = DB::table('store_location')
                ->where('id', $request->get('location_id'))
                ->get();
        } else {
            $data = DB::table('store_location')
                ->get();
        }

        return CatalogVendorLocationResource::collection($data);
    }


    public function getVehicleYear()
    {

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


        $makes = collect($response['Makes'])->pluck('VehicleMakeName')->toArray();

        return response()->json(['Makes' => $makes]);
    }


    public function getVehicleByModels(Request $request)
    {
        $requestYear = [
            'Year' => $request->year,
            'MakeName' => $request->make
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetModels?Token=" . $this->vehicleToken, $requestYear)->json();


        $models = collect($response['Models'])->pluck('VehicleModelName')->toArray();

        return response()->json(['Models' => $models]);
    }


    public function getVehicleConfigurations(Request $request)
    {
        $requestOption = [
            'Year' => $request->year,
            'MakeName' => $request->make,
            'ModelName' => $request->model
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetConfigurations?Token=" . $this->vehicleToken, $requestOption)->json();

        $options = collect($response['Configurations'])->pluck('VehicleConfigurationName')->toArray();
        return response()->json(['Options' => $options]);
    }


    public function getTiresByVehicle(Request $request)
    {
        $exactMatch = $request->year . ' ' . $request->make . ' ' . $request->model . ' ' . $request->option;
        $requestOption = [
            'Search' => $exactMatch,
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetTireOptionDetails?Token=" . $this->vehicleToken, $requestOption)->json();
        return response()->json($response);
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

    public function getWheelsByVehicle(Request $request)
    {
        //needed request
        $exactMatch = $request->year . ' ' . $request->make . ' ' . $request->model . ' ' . $request->option;
        $requestOption = [
            'Search' => $exactMatch
        ];

        //get configID from getdescription endpoint using request
        $responseGetDesc = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetDescriptions?Token=" . $this->vehicleToken, $requestOption)->json();
        $configID = [
            'VehicleConfiguration' => collect($responseGetDesc['Descriptions'])->pluck('ConfigurationID')->implode(',')
        ];
      
        
        $getFitments = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetFitments?Token=" . $this->vehicleToken, $configID)->json();
           
            // $fitmentBoltPatternID = [
            //     'VehicleFitment_BoltPatternID' => collect($getFitments['Fitments'])->pluck('VehicleFitment_BoltPatternID')->implode(',')
            // ];
         

            // $fitmentBoltPatternID = collect($getFitments['Fitments'])->map(function ($fitment) {
            //     return [
            //         'VehicleFitment_BoltPatternID' => $fitment['VehicleFitment_BoltPatternID'],
            //     ];
            // })->first();
   
            // return $fitmentBoltPatternID;
            // $boltPatternOption = [
            //     'VehicleConfiguration' => $configID['VehicleConfiguration'],
            //     // 'FitmentFilters' => [
                    
            //     //     // 'BoltPattern' => 23,
            //     //     'Offset' =>46.0
                       
            //     // ]
            // ];




        //getboltpatterns, fitments, tireoption using configID
        $getBoltPatterns = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Wheel/GetBoltPatterns?Token=" . $this->vehicleToken, $configID)->json();

            return $getBoltPatterns;

        $getTireOptDetails = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetTireOptionDetails?Token=" . $this->vehicleToken, $configID)->json();
        
        //get inside diameter from gettireoptiondetails
        $insideDiameter = $insideDiameter = $getTireOptDetails['Details'][0]['Front']['InsideDiameter'];
        

        //get the details needed
        $details = [
            'Fitments' => collect($getFitments['Fitments'])->map(function($detail) {
                return [
                    'VehicleFitment_BoltPatternID' => $detail['VehicleFitment_BoltPatternID'],
                    'VehicleFitmentHub' => $detail['VehicleFitmentHub'],
                    'VehicleFitmentWidthMin' => $detail['VehicleFitmentWidthMin'],
                    'VehicleFitmentWidthMax' => $detail['VehicleFitmentWidthMax'],
                    'VehicleFitmentOffsetMin' => $detail['VehicleFitmentOffsetMin'],
                    'VehicleFitmentOffsetMax' => $detail['VehicleFitmentOffsetMax'],
                ];
            }),
            'BoltPatterns' => collect($getBoltPatterns['BoltPatterns'])->map(function($detail) {
                return [
                    'BoltPatternSpacingMM' => $detail['BoltPatternSpacingMM'],
                    'BoltPatternBoltCount' => $detail['BoltPatternBoltCount']
                ];
            }),

            'InsideDiameter' => $insideDiameter
        ];




        return response()->json($details);
    }

    public function getBoltPatterns(Request $request)
    {
        $requestOption = [
            'VehicleConfiguration' => $request->vehicleConfiguration,
            'FitmentFilters' => [
                [
                    'BoltPattern' => 23
                ]
            ],
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
