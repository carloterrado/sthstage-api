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
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;




class CatalogController extends Controller
{
    protected $vehicleToken;
    protected $excludeColumns;
    protected $additionalColumnsToExclude;
    protected $tableColumns;
    protected $client;

    public function __construct()
    {
        $credential = [
            'Username' => env('RIDESTYLER_USERNAME'),
            'Password' => env('RIDESTYLER_PASSWORD'),
        ];
        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Auth/Start", $credential);

        $this->vehicleToken = $response->json('Token');


        $bearerToken = request()->bearerToken();

        $tokenId = (new Parser(new JoseEncoder()))->parse($bearerToken)->claims()
            ->all()['jti'];
        $this->client = Token::find($tokenId)->client;
        $this->excludeColumns = json_decode($this->client->catalog_column_settings);
        $this->additionalColumnsToExclude = ['updated_at', 'created_at'];
        $this->tableColumns = Schema::getColumnListing('catalog');
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
                ->select(array_diff($this->tableColumns, array_merge($this->excludeColumns, $this->additionalColumnsToExclude)))
                ->get();

            return response()->json(['data' => $data]);
        }

        if ($request->has('wheel_diameter') && $request->has('wheel_width')) {

            $rules = [
                'wheel_diameter' => 'required|numeric',
                'wheel_width' => 'required|numeric',
            ];

            $validatedData = $request->validate($rules);

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
                ->select(array_diff($this->tableColumns, array_merge($this->excludeColumns, $this->additionalColumnsToExclude)))
                ->get();
            return response()->json(['data' => $data]);
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
                ->where('category', 1)
                ->when($request->has('brand'), function ($query) use ($request) {
                    $query->where('brand', $request->brand);
                })
                ->when($request->has('mspn'), function ($query) use ($request) {
                    $query->where('mspn', $request->mspn);
                })
                ->select(array_diff($this->tableColumns, array_merge($this->excludeColumns, $this->additionalColumnsToExclude)))
                ->get();

            return response()->json(['data' => $data]);
        }

        if ($request->has('section_width') && $request->has('aspect_ratio') && $request->has('rim_diameter')) {

            $rules = [
                'section_width' => 'required|numeric',
                'aspect_ratio' => 'required|numeric',
                'rim_diameter' => 'required|numeric',
            ];

            $validatedData = $request->validate($rules);

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
                ->select(array_diff($this->tableColumns, array_merge($this->excludeColumns, $this->additionalColumnsToExclude)))
                ->get();
            return response()->json(['data' => $data]);
        }


        return response()->json([
            'error' => 'Missing Parameter',
            'message' => 'The required parameters for tires are missing in the request.'
        ], 400);
    }

    public function getVehicles(Request $request)
    {


        $year = $request->year;
        $make = $request->make;
        $model = $request->model;
        $feature = $request->feature;
        $config = $request->config;


        //for makes
        $requestMakes = [
            'Year' => $year,
            'MakeName' => $make,
        ];

        $getMakes = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetMakes?Token=" . $this->vehicleToken, $requestMakes)
            ->json();

        $makeID = $getMakes['Makes'][0]['VehicleMakeID'];


        //for model
        $requestModel = [
            'Year' => $year,
            'MakeName' => $make,
            'ModelName' => $model
        ];

        $getModel = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetModels?Token=" . $this->vehicleToken, $requestModel)->json();

        $modelID = $getModel['Models'][0]['VehicleModelID'];


        //for features
        $requestForFeat = [
            'Selection' => [
                'year:' . $year,
                'make:' . $makeID,
                'model:' . $modelID
            ]
        ];

        $getSelection = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/Select?Token=" . $this->vehicleToken, $requestForFeat)
            ->json();


        $featureValue = collect($getSelection['Menu']['Options'])
            ->map(function ($option) use ($feature) {
                if ($option['Label'] === $feature) {
                    return $option['Value'];
                }
            })
            ->filter()
            ->first();


        //for config
        $requestForConfig = [
            'Selection' => [
                'year:' . $year,
                'make:' . $makeID,
                'model:' . $modelID,
                'features:' . $featureValue
            ]
        ];

        $getSelection = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/Select?Token=" . $this->vehicleToken, $requestForConfig)
            ->json();


        $configValue = collect($getSelection['Menu']['Options'])
            ->map(function ($option) use ($config) {
                if ($option['Label'] === $config) {
                    return $option['Value'];
                }
            })
            ->filter()
            ->first();


        $selections = [
            $year ? 'year:' . $year : null,
            $make ? 'make:' . $makeID : null,
            $model ? 'model:' . $modelID : null,
            $feature ? 'features:' . $featureValue : null,
            $config ? 'config:' . $configValue : null
        ];

        $requestOption = [
            'Selection' => array_filter($selections)
        ];



        $getSelection = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/Select?Token=" . $this->vehicleToken, $requestOption)
            ->json();


        $response = [
            'Data' => [
                collect($getSelection['Menu']['Options'])->pluck('Label')
            ]
        ];


        return response()->json($response);
    }


    public function getVehicleYear()
    {

        return Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetYears?Token=" . $this->vehicleToken)
            ->json();
    }


    public function getVehicleByMakes(Request $request)
    {

        $request->validate([
            'year' => 'required',
            // 'make' => 'required',
        ]);
        $requestYear = [
            'Year' => $request->year
        ];

        $request->validate([
            'year' => 'required|integer'
        ]);

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetMakes?Token=" . $this->vehicleToken, $requestYear)
            ->json();

        if (empty($response) || !isset($response['Makes']) || empty($response['Makes'])) {
            return response()->json(['message' => 'No Response']);
        }


        $makes = collect($response['Makes'])->pluck('VehicleMakeName')->toArray();

        return response()->json(['Makes' => $makes]);
    }


    public function getVehicleByModels(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'make' => 'required',
        ]);

        $requestYear = [
            'Year' => $request->year,
            'MakeName' => $request->make
        ];

        $request->validate([
            'year' => 'required|integer',
            'make' => 'required'
        ]);

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetModels?Token=" . $this->vehicleToken, $requestYear)->json();

        if (empty($response) || !isset($response['Models']) || empty($response['Models'])) {
            return response()->json(['message' => 'No Response']);
        }


        $models = collect($response['Models'])->pluck('VehicleModelName')->toArray();

        return response()->json(['Models' => $models]);
    }

    public function getVehicleConfigurations(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'make' => 'required',
            'model' => 'required',
        ]);

        $requestOption = [
            'Year' => $request->year,
            'MakeName' => $request->make,
            'ModelName' => $request->model
        ];

        $request->validate([
            'year' => 'required|integer',
            'make' => 'required',
            'model' => 'required'
        ]);

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetDescriptions?Token=" . $this->vehicleToken, $requestOption)->json();

        if (empty($response) || !isset($response['Options']) || empty($response['Options'])) {
            return response()->json(['message' => 'No Response']);
        }

        $options = collect($response['Descriptions'])->pluck('TrimDescription')->flatten()->toArray();
        return response()->json(['Options' => $options]);
    }

    public function getVehicleSize(Request $request)
    {

        $request->validate([
            'year' => 'required|integer',
            'make' => 'required',
            'model' => 'required',
            'option' => 'required',
        ]);
        $request->year . ' ' . $request->make . ' ' . $request->model . ' ' . $request->option;
        $exactMatch =
            $requestOption = [
                'Search' => $exactMatch,
            ];

        //get configID from getdescription endpoint using request
        $responseGetDesc = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetDescriptions?Token=" . $this->vehicleToken, $requestOption)->json();

        // Check if responseGetDesc contains data
        if (!empty($responseGetDesc['Descriptions'])) {
            $configID = [
                'VehicleConfiguration' => collect($responseGetDesc['Descriptions'])->pluck('ConfigurationID')->implode(',')
            ];

            $getTireOptDetails = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("https://api.ridestyler.net/Vehicle/GetTireOptionDetails?Token=" . $this->vehicleToken, $configID)->json();

            $getFitments = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("https://api.ridestyler.net/Vehicle/GetFitments?Token=" . $this->vehicleToken, $configID)->json();

            // Generate wheel sizes
            $wheelArr = [];
            for ($i = $getFitments['Fitments'][0]['VehicleFitmentWidthMin']; $i <= $getFitments['Fitments'][0]['VehicleFitmentWidthMax']; $i += 0.5) {
                $str = $getTireOptDetails['Details'][0]['Front']['InsideDiameter'] . 'x' . $i;
                array_push($wheelArr, $str);
            }

            $response = [
                'Size' => [
                    'Tires' => collect($getTireOptDetails['Details'])->pluck('Front.Size'),
                    'Wheels' => $wheelArr,
                ]
            ];

            return response()->json($response);
        }

        // No response or empty data
        return response()->json(['message' => 'No response']);
    }


    public function getTiresByVehicle(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'make' => 'required',
            'model' => 'required',
            'option' => 'required',
            'size' => 'required',
        ]);


        $exactMatch = $request->year . ' ' . $request->make . ' ' . $request->model . ' ' . $request->option;
        $requestOption = [
            'Search' => $exactMatch,
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetTireOptionDetails?Token=" . $this->vehicleToken, $requestOption)->json();

        $details = collect($response['Details'])->map(function ($detail) {
            return [
                'full_size' => $detail['Front']['Size'],
            ];
        });

        $filteredSize = $details->filter(function ($detail) use ($request) {
            return $detail['full_size'] === $request->size;
        })->values();

        if ($filteredSize->isEmpty()) {
            // No response or empty data
            return response()->json(['message' => 'No response']);
        }


        $data = DB::table('catalog')
            ->whereIn('full_size', $filteredSize->pluck('full_size'))
            ->select(array_diff($this->tableColumns, array_merge($this->excludeColumns, $this->additionalColumnsToExclude)))
            ->get();

        return response()->json(['data' => $data]);
    }

    public function getWheelsByVehicle(Request $request)
    {

        $validator =  $request->validate([
            'year' => 'required|integer',
            'make' => 'required',
            'model' => 'required',
            'option' => 'required',
            'size' => 'required',
        ]);

        //needed request
        $exactMatch = $request->year . ' ' . $request->make . ' ' . $request->model . ' ' . $request->option;
        $requestOption = [
            'Search' => $exactMatch
        ];

        //get configID from getdescription endpoint using request
        $responseGetDesc = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post("https://api.ridestyler.net/Vehicle/GetDescriptions?Token=" . $this->vehicleToken, $requestOption)->json();

        if (!empty($responseGetDesc['Descriptions'])) {

            $configID = [
                'VehicleConfiguration' => collect($responseGetDesc['Descriptions'])->pluck('ConfigurationID')->implode(',')
            ];


            $getFitments = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("https://api.ridestyler.net/Vehicle/GetFitments?Token=" . $this->vehicleToken, $configID)->json();
            // return $getFitments;

            //getboltpatterns, fitments, tireoption using configID
            $getBoltPatterns = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("https://api.ridestyler.net/Wheel/GetBoltPatterns?Token=" . $this->vehicleToken, $configID)->json();

            // return $getBoltPatterns;

            $getTireOptDetails = Http::withHeaders(['Content-Type' => 'application/json'])
                ->post("https://api.ridestyler.net/Vehicle/GetTireOptionDetails?Token=" . $this->vehicleToken, $configID)->json();

            //get inside diameter from gettireoptiondetails
            $insideDiameter = $getTireOptDetails['Details'][0]['Front']['InsideDiameter'];



            $catalog = DB::table('catalog')
                ->where('center_bore', '>=', $getFitments['Fitments'][0]['VehicleFitmentHub'])
                ->whereBetween('wheel_width', [$getFitments['Fitments'][0]['VehicleFitmentWidthMin'], $getFitments['Fitments'][0]['VehicleFitmentWidthMax']])
                ->whereBetween('offset', [$getFitments['Fitments'][0]['VehicleFitmentOffsetMin'], $getFitments['Fitments'][0]['VehicleFitmentOffsetMax']])
                ->where('bolt_circle_diameter_1', '=', $getBoltPatterns['BoltPatterns'][1]['BoltPatternSpacingMM'])
                ->where('bolt_pattern_1', '=', $getBoltPatterns['BoltPatterns'][1]['BoltPatternBoltCount'])
                ->where('wheel_diameter', '=', $insideDiameter)
                ->where('full_size', $request->size)
                ->select(array_diff($this->tableColumns, array_merge($this->excludeColumns, $this->additionalColumnsToExclude)))
                ->get();



            return response()->json(['data' => $catalog]);
        }

        return response()->json(['message' => 'No response']);
    }

    // Get location details
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

        if ($data->isEmpty()) {
            return response()->json(['message' => 'No Response']);
        }

        return CatalogVendorLocationResource::collection($data);
    }

    //Get inventory price location
    public function inventoryPrice(Request $request)
    {
        $request->validate([
            'brand' => 'required',
            'mspn' => 'required',
        ]);

        $bearerToken = request()->bearerToken();

        $tokenId = (new Parser(new JoseEncoder()))->parse($bearerToken)->claims()
            ->all()['jti'];
        $client = Token::find($tokenId)->client;
        // USE this variable when client column is created in oauth_clients table
        $clientChannel = json_decode($client->channel);

        $firstData = DB::table('inventory_upload_logs')
            ->select('id')
            ->orderBy('created_at', 'asc')
            ->first();


        $data = DB::table('inventory_feed AS i')
            ->select(
                'i.brand',
                'i.part_number',
                'i.vendor_main_id',
                'i.store_location_id',
                'n.selling_price',
                'i.qty',
            )
            ->join('inventory_channel_selling_price AS n', function ($join) {
                $join->on('n.brand', '=', 'i.brand')
                    ->on('n.part_number', '=', 'i.part_number');
            })
            ->join(DB::raw('(SELECT MIN(id) as min_id FROM inventory_channel_selling_price GROUP BY brand, part_number, channel) AS sub'), function ($join) {
                $join->on('n.id', '=', 'sub.min_id');
            })
            ->where('i.part_number', $request->mspn)
            ->where('i.brand', $request->brand)
            // Change the default 329 value by $clientChannel variable 
            ->where('n.channel', 329)
            ->where('n.upload_id', $firstData->id)
            ->get();

        if ($data->isEmpty()) {
            // No response or empty data
            return response()->json(['message' => 'No response']);
        }

        return  CatalogInventoryPriceResource::make($data);
    }


    public function getOrderStatus(Request $request)
    {
        $orderStatus = DB::table('orderList')
            ->select('orderList.po_number', 'orderStatus.status')
            ->where(['orderList.po_number' => $request->po_number, 'orderList.user_id' => $request->user_id])
            ->leftJoin('orderStatus', 'orderList.order_status_id', '=', 'orderStatus.id')
            ->get();

        if ($orderStatus->isEmpty()) {
            // No response or empty data
            return response()->json(['message' => 'No response']);
        }

        return response()->json($orderStatus);
    }
}
