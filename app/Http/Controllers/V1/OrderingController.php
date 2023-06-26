<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PowerSearch;
use App\Models\Catalog;
use App\Models\Netnet;
use App\Models\OrderDetail;
use App\Models\OrderList;
use App\Models\StoreLocation;
use App\Models\Strapping;
use App\Models\TransferMapping;
use App\Models\VendorMain;
use App\Models\ZipCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Session\Session as SessionSession;

class OrderingController extends Controller
{

    protected $powerSearch;
    public function __construct(PowerSearch $powerSearch)
    {
        $this->powerSearch = $powerSearch;
    }




    public function postQuote(Request $request)
    {

        $request->merge([
            'channel' => '329ST',
            'not_api' => false,
            'brand' => strtoupper($request->get('brand')),
            'part_number' => strtoupper($request->get('part_number')),
        ]);

        $checkPartNumberInDatabase = Catalog::where('brand', $request->brand)->where('mspn', $request->part_number)->exists();

        if (!$checkPartNumberInDatabase) {
            return response()->json([
                "status" => "fail",
                "message" => "Product not found"
            ]);
        }

        if ($request->has('store_location_id')) {
            $checkStoreLocationId = StoreLocation::where('id', $request->store_location_id)->where('is_active', 1)->exists();
            if (!$checkStoreLocationId) {
                return response()->json([
                    "status" => "fail",
                    "message" => "Store location not found"
                ]);
            }
        }



        $search_result = $this->powerSearch->execute($request);

        $data = $search_result->getData();
        $message = $data->message ?? false;

        if ($message) {
            return response()->json([
                "status" => "fail",
                "message" => "No stock."
            ]);
        }

        if ($request->has('store_location_id')) {
            $matchedVendor = null;
            foreach ($data->available_vendors as $vendor) {
                if ($vendor->vendor_details->store_location_id === $request->store_location_id) {
                    $matchedVendor = $vendor;
                    break;
                }
            }

            if ($matchedVendor) {
                return response()->json([
                    "status" => "success",
                    "message" => "",
                    "store_location_id" => $request->store_location_id,
                    "shipping_price" => $data->order_total->shipping_cost_total,
                ]);
            } else {

                return response()->json([
                    "status" => "fail",
                    "message" => "No stock."
                ]);
            }
        } else {
            return response()->json([
                "status" => "success",
                "message" => "",
                "store_location_id" => $data->available_vendors[0]->vendor_details->store_location_id,
                "shipping_price" => $data->order_total->shipping_cost_total,
            ]);
        }
    }
}
