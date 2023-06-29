<?php

namespace App\Http\Controllers;

use App\Http\Controllers\V1\StockController;
use App\Models\Catalog;
use App\Models\Netnet;
use App\Models\OrderList;
use App\Models\StoreLocation;
use App\Models\Strapping;
use App\Models\TransferMapping;
use App\Models\VendorMain;
use App\Models\ZipCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Session\Session;

class PowerSearch extends Controller
{

    protected $stockController;
    public function __construct(StockController $stockController){
        $this->stockController = $stockController;
    }

    public function validation(Request $request)
    {
        $requests = ["part_number", "brand", "qty", "zip", "city"];
        $error = [];
        foreach ($requests as $r => $e) {
            if (empty($request->get($e))) {
                $error[] = $e;
            }
        }

        if (!empty($error)) {
            return [
                'status' => 'error',
                'message' => 'Please enter ' . implode(', ', $error),
                'error_id' => 10
            ];
        }

        if (!empty($request->get('channel'))) {
            $channels = DB::table('channels')
                ->where('channel_code', $request->get('channel'))
                ->first();

            if ($channels == null) {
                return [
                    'status' => 'error',
                    'message' => 'Channel not found',
                    'error_id' => 13
                ];
            }
        } else {
            return [
                'status' => 'error',
                'message' => 'Please specify channel',
                'error_id' => 13
            ];
        }

        if ($request->has("channel_order_no") && !$request->has("reorder")) {
            $db_check_channel_order = DB::table("orderList")
                ->join('orderDetails', 'orderList.id', '=', 'orderDetails.order_list_id')
                ->where([
                    ['orderList.order_status_id', '!=', 7],
                    ['orderDetails.channel_order', $request->get('channel_order_no')],
                    ['orderDetails.part_number', $request->get('part_number')]
                ]);

            $query = [
                ['channel_order_no', $request->get('channel_order_no')],
                ['orders->product_details->mspn', $request->get('part_number')]
            ];
            if ($request->has("save_id")) {
                $query[] = ['id', '!=', $request->get("save_id")];
            }

            $db_saved_orders_check = DB::table("save_inital_order")->where($query);
            if ($db_check_channel_order->exists() || $db_saved_orders_check->exists()) {
                return [
                    'message' => 'Channel Order Number exists',
                    'error_id' => 14
                ];
            }
        } elseif (!$request->has("reorder") && !$request->has("single")) {
            return [
                'message' => 'Please enter Channel Order Number',
                'error_id' => 10
            ];
        }

        if ($request->has('state') && strlen($request->get('state')) > 2) {
            return [
                'message' => 'Invalid state. State needs to be exactly 2 characters',
                'error_id' => 10
            ];
        }

        if (!empty($request->get('promo'))) {
            $promo_list = DB::table('promotions')
                ->where('promo_code', $request->get('promo'))
                ->first();

            if ($promo_list == null) {
                return [
                    'message' => 'Invalid promo code',
                    'error_id' => 10
                ];
            }
        }

        if (!is_numeric($request->get('qty')) || intval($request->get('qty')) < 1) {
            return [
                'message' => 'Invalid quantity',
                'error_id' => 2
            ];
        }

        return ['status' => 'success'];
    }

    public function execute(Request $request)
    {



        $validation = $this->validation($request);
        if (!(isset($validation['status']) && strtolower($validation['status']) == 'success')) {
            return response()->json($validation, 422);
        }

        $channel = $request->get('channel');

        $channels = DB::table('channels')
            ->where('channel_code', $request->get('channel'))
            ->first();

        $batch = $request->get('batch');

        $check_zip = $this->getCity($request);
        if (is_array($check_zip) && array_key_exists("message", $check_zip)) {
            return response()->json($check_zip, 422);
        }

        $object = new \stdClass();
        $object->state = $check_zip->state;
        $object->city = $check_zip->city;
        $object->lat = $check_zip->lat;
        $object->lon = $check_zip->lon;

        $object->first_name = $request->get('fname');
        $object->last_name = $request->get('lname');
        $object->phone_number = $request->get('phone_number');
        $object->email_address = $request->get('email_address');
        $object->address1 = $request->get('address1');
        $object->address2 = $request->get('address2');
        $object->city = $request->get('city');
        $object->state = $request->get('state');
        $object->postal_code = $request->get('zip');

        $object->billing_first_name = $request->get('billing_first_name');
        $object->billing_last_name = $request->get('billing_last_name');
        $object->billing_phone_number = $request->get('billing_phone_number');
        $object->billing_email_address = $request->get('billing_email_address');
        $object->billing_address1 = $request->get('billing_address1');
        $object->billing_address2 = $request->get('billing_address2');
        $object->billing_city = $request->get('billing_city');
        $object->billing_state = $request->get('billing_state');
        $object->billing_postal_code = $request->get('billing_postal_code');

        $object->quantity = $request->get('qty');
        $object->brand = $request->get('brand');
        $object->part_number = $request->get('part_number');
        $object->selling_price = $request->get('selling_price');
        $object->channel = $request->get('channel');
        $object->batch = $request->get('batch');
        $object->channel_order = $request->get('channel_order_no');
        $object->single_bulk = $request->has("single") ? 'Single' : 'Bulk';
        $object->user = auth()->user()->id;
        $object->order_id = $request->get("order_id");
        $object->installer_id = $request->get("installer_id");
        $object->additional_details = $request->get("additional_details");

        $order_qty = $request->get('qty');

        $response = $this->stockController->stockCheck($object);


        if (!is_array($response) && $response !== true) {
            return response()->json([
                'message' => $response,
                'error_id' => 10
            ], 422);
        }

        $un_touched_inventory = $response;
        $available_vendor = [];
        $no_stock_vendor = [];

        foreach ($response['inventory'] as $r => $s) {
            if (!is_array($s)) {
                unset($response['inventory'][$r]);
                continue;
            }

            if ((isset($s["message"]) && strtolower($s["message"]) == 'out of stock') || (isset($s["status"]) && strtolower($s["status"]) == 'out of stock')) {
                $store_id = isset($s["store_location"]) ? $s["store_location"] : '';
                $no_stock_vendor[] = ['vendor_main_id' => $s['vendor_id'], 'qty' => 0, 'store_id' => $store_id];
            }


            if (strtolower($s["status"]) != "success") {
                unset($response['inventory'][$r]);
                continue;
            }

            if (!array_key_exists("vendor_id", $s)) {
                unset($response['inventory'][$r]);
                continue;
            }

            if (floatval($s["quantity"]) > 0) {
                if ($request->has('except_stores') && $request->except_stores['vendor_main_id'] == $s['vendor_id'] && $request->except_stores['store_location_id'] == $s['store_location']) {
                    continue;
                }
                $available_vendor[] = $s["vendor_id"];
            } else {
                $no_stock_vendor[] = ['vendor_main_id' => $s['vendor_id'], 'qty' => $s["quantity"], 'store_id' => $s["store_location"]];
                unset($response['inventory'][$r]);
            }
        }
        $searchResult = $this->search($request->part_number, $request->brand);


        if (array_key_exists("message", $searchResult) && !$request->has("dimension")) {
            $return_inventory = $response;
            foreach ($return_inventory as $key => &$inventory) {
                $inventory_city = "";
                $inventory_vendor_name = $inventory["vendor_id"];
                $db_store_location = DB::table("store_location")->where("id", $inventory["store_location"])->first();
                $db_vendor = DB::table("vendor_main")->where("id", $inventory["vendor_id"])->first();
                if ($db_store_location != null && $db_vendor != null) {
                    $inventory_city = $db_store_location->city . ", " . $db_store_location->state;
                    $inventory_vendor_name = $db_vendor->name;
                }
                $inventory["city"] = $inventory_city;
                $inventory["vendor_name"] = $inventory_vendor_name;
            }
            return response()->json(array_merge($searchResult, ["inventory" => $return_inventory]), 422);
        }

        $order_id = "";
        if (array_key_exists("order_id", $un_touched_inventory)) $order_id = $un_touched_inventory["order_id"];

        $product_type = 1;
        if (!$request->has("dimension")) {
            if (!array_key_exists("message", $searchResult)) {
                $searchResult = $searchResult[0];
                $product_type = $searchResult['category'];
                if ($searchResult['category'] == 1) {
                    $filtered_full_size = preg_replace("/[^0-9]+/", "", $searchResult['unformatted_size']);
                    if (($strapping = Strapping::where("search_size", $filtered_full_size)->first()) == null) {
                        $shipping_dimensions = $this->getItemDimension($searchResult['section_width'], $searchResult['aspect_ratio'], $searchResult['rim_diameter']);
                        $shipping_dimensions["order_id"] = $order_id;
                        return response()->json($shipping_dimensions, 422);
                    }
                }
            }
        } elseif ($request->has("product_type")) {
            $product_type = $request->product_type;
        }

        $this->saveMissingData($request, $order_id);

        if (!$request->has("dimension")) {
            if ($product_type == 1) {
                $shipping_dimensions = [];
                $filtered_full_size = preg_replace("/[^0-9]+/", "", $searchResult['unformatted_size']);
                $strapping = Strapping::where("search_size", $filtered_full_size)->first();
                if ($strapping == null) {
                    $shipping_dimensions = $this->getItemDimension($searchResult['section_width'], $searchResult['aspect_ratio'], $searchResult['rim_diameter']);
                    unset($shipping_dimensions["message"]);
                    unset($shipping_dimensions["error_id"]);
                } else {
                    $shipping_dimensions = [
                        "fullsize" => $searchResult['section_width'] . "/" . $searchResult['aspect_ratio'] . "R" . $searchResult['rim_diameter'],
                        "height" => $strapping->height,
                        "length" => $strapping->length,
                        "width" => $strapping->width,
                        "ship_price" => $strapping->final_weight * .71,
                        "weight" => $strapping->final_weight,
                        "product_type" => $searchResult['category'],
                        "model" => $searchResult['model'],
                        "description" => $searchResult['description']
                    ];

                    if (floatval($searchResult['weight_tire']) > 0) {
                        $shipping_dimensions["weight"] = floatval($searchResult['weight_tire']);
                    }
                }
            } else if ($product_type == 2) {
                $strapping = Strapping::where("search_size", preg_replace("/[^0-9]+/", "", $searchResult['wheel_diameter'] . $searchResult['wheel_width']))->first();
                if ($strapping == null) {
                    return response()->json([
                        "message" => "Shipping dimensions missing",
                        "error_id" => "1",
                        "fullsize" => "",
                        "height" => "",
                        "length" => "",
                        "width" => "",
                        "ship_price" => "",
                        "weight" => "",
                        "wheel_width" => $searchResult['wheel_width'],
                        "wheel_diameter" => $searchResult['wheel_diameter'],
                        "product_type" => 2,
                        "line" => __LINE__,
                        "order_id" => $order_id
                    ], 422);
                } else {
                    $shipping_dimensions = [
                        "fullsize" => $searchResult['wheel_diameter'] . "X" . $searchResult['wheel_width'],
                        "height" => $strapping->height,
                        "length" => $strapping->length,
                        "width" => $strapping->width,
                        "ship_price" => $strapping->final_weight * .71,
                        "weight" => $strapping->final_weight,
                        "product_type" => $searchResult['category'],
                        "model" => $searchResult['model'],
                        "description" => $searchResult['description']
                    ];
                }
            } else if ($product_type == 3) {
                $shipping_dimensions = [
                    "fullsize" => '',
                    "height" => $searchResult['height_package'],
                    "length" => $searchResult['length_package'],
                    "width" => $searchResult['width_package'],
                    "weight" => $searchResult['weight_package'],
                    "ship_price" => $searchResult['weight_package'] * .71,
                    "product_type" => $searchResult['category'],
                    "model" => $searchResult['model'],
                    "description" => $searchResult['description']
                ];
            }
        } else {
            $shipping_dimensions = [
                "weight" => $request->dimension["weight"],
                "length" => $request->dimension["length"],
                "width" => $request->dimension["width"],
                "height" => $request->dimension["height"],
                "ship_price" => $request->dimension["price"],
                "fullsize" => "",
                "product_type" => $request->product_type,
                "model" => "",
                "description" => ""
            ];

            if (!array_key_exists("message", $searchResult)) {
                $shipping_dimensions["product_type"] = $searchResult[0]->category;
                $shipping_dimensions["model"] = $searchResult[0]->model;
                $shipping_dimensions["description"] = $searchResult[0]->description;
            }
        }

        if (count($available_vendor) == 0) {
            return response()->json(["message" => "No stock.", "error_id" => "3", "order_id" => $order_id, "shipping_dimensions" => $shipping_dimensions, "inventory" => $un_touched_inventory, "line" => __LINE__], 422);
        }

        $partNumber = $request->part_number;
        $brand = $request->brand;

        if ($request->has("netnet")) {
            $netnet = $request->netnet;
        } else {
            $netnet = Netnet::where([
                ['netnet_price.brand', $brand],
                ['netnet_price.mspn', $partNumber]
            ])
                ->select([
                    'netnet_price.brand',
                    'netnet_price.mspn',
                    'netnet_price.netnet',
                    'netnet_price.invoice',
                    'vendor_main.id'
                ])
                ->leftJoin("vendor_main", 'vendor_main.id', '=', 'netnet_price.vendor')
                ->whereIn('netnet_price.vendor', array_unique($available_vendor))
                ->groupBy(
                    'netnet_price.vendor',
                    'netnet_price.brand',
                    'netnet_price.mspn',
                    'netnet_price.netnet',
                    'netnet_price.invoice',
                    'vendor_main.id'
                )
                ->orderBy('netnet_price.netnet', 'ASC')
                ->get()
                ->toArray();

            $vendor_with_price = [];
            foreach ($netnet as $k => $v) {
                if (in_array($v["id"], $available_vendor)) {
                    $vendor_with_price[] = $v["id"];
                }
            }
            foreach ($response['inventory'] as $i => $j) {
                if (!in_array($j["vendor_id"], $vendor_with_price) && array_key_exists("price", $j) && floatval($j) > 0) {
                    if (!in_array($j["vendor_id"], array_column($netnet, "id"))) {
                        $netnet[] = [
                            "brand" => $brand,
                            "mspn" => $partNumber,
                            "id" => $j["vendor_id"],
                            "netnet" => $j["price"],
                            "invoice" => $j["price"]
                        ];
                    }
                }
            }
        }

        $quantity_allocation = $order_qty;

        // if($request->has("shipping_method")){
        //     $delivery_methods = ["WILL CALL"];
        // } else {
        $delivery_methods = ["DROPSHIP", "DELIVERY", "WILL CALL"];
        // }

        $netnetPrice = [];
        $customerZip = $request->zip;
        $repeat_vendor_array = [];
        $carrier_errors = [];

        foreach ($netnet as $k => $v) {
            foreach ($response['inventory'] as $r => $s) {
                if ($v["id"] == $s["vendor_id"]) {
                    foreach ($delivery_methods as $d => $m) {
                        if ($request->has("shipping_method") && $m == "DROPSHIP") {
                            continue;
                        }

                        if (in_array($m, ["DELIVERY", "WILL CALL"]) && $s["vendor_id"] == 5 && in_array($s["store_location"], [244, 245, 248])) {
                            continue;
                        }

                        if (in_array($m, ["DELIVERY", "WILL CALL"]) && $s["vendor_id"] == 76 && $s["store_location"] != 580) {
                            continue;
                        }

                        if ($m == "WILL CALL" && $v["id"] == 2 && !in_array($s["branch"], ["05", "07"])) {
                            continue;
                        }

                        if ($m == "DELIVERY" && $v["id"] == 2 && !in_array($s["branch"], ["07", "06"])) {
                            continue;
                        }

                        if (in_array($m, ["DELIVERY", "WILL CALL"]) && $v["id"] == 23 && !in_array($s["store_location"], ["134", "135", "136", "137", "138", "139", "140", "141"])) {
                            continue;
                        }

                        $vendor_statement = [
                            ["vendor_main_id", $v["id"]]
                        ];
                        if (array_key_exists("apitype", $s)) {
                            $vendor_statement[] = ["api_type", $s["apitype"]];
                        }

                        $vendor_details = DB::table("vendor_main_details")
                            ->where($vendor_statement)
                            ->first();

                        if ($vendor_details == null)
                            continue;

                        $vendor_delivery_method = json_decode($vendor_details->delivery_method, true);
                        if (!is_array($vendor_delivery_method) || !in_array($m, $vendor_delivery_method)) {
                            continue;
                        }

                        $is_channel_ok = false;
                        if ($batch != null && $m == "WILL CALL" && !(count($vendor_delivery_method) == 1 && $vendor_delivery_method[0] == "WILL CALL")) {
                            continue;
                        }

                        $db_delivery_methods = json_decode($channels->delivery_method, true);
                        if ($product_type == 2 and $request->get('channel') == '101') {
                            $db_delivery_methods[] = 'DROPSHIP';
                            $db_delivery_methods = array_unique($db_delivery_methods);
                        }

                        $assigned_vendors = json_decode($channels->assigned_vendor, true);
                        if (is_array($assigned_vendors)) {
                            if ($m != "DROPSHIP" && array_key_exists($s["vendor_id"], $assigned_vendors)) {
                                if (empty($assigned_vendors[$s["vendor_id"]])) {
                                    $is_channel_ok = true;
                                } else {
                                    if (in_array($s["store_location"], $assigned_vendors[$s["vendor_id"]])) {
                                        $is_channel_ok = true;
                                    } else {
                                        continue;
                                    }
                                }
                            } elseif ($m == "DROPSHIP") {
                                $is_channel_ok = true;
                            } else {
                                continue;
                            }
                        } else {
                            $is_channel_ok = true;
                        }

                        if ($is_channel_ok) {
                            if (!in_array($m, $db_delivery_methods)) {
                                continue;
                            } else /*if($batch !== null) {
                                $delivery_method = [$m];
                            } else*/ if (in_array($m, ["DELIVERY", "WILL CALL"])) {
                                $delivery_method = array_intersect($vendor_delivery_method, array_diff($db_delivery_methods, ["DROPSHIP"]));
                            } elseif ($m == "DROPSHIP") {
                                $delivery_method = ["DROPSHIP"];
                            }
                        }

                        if ($m == "DELIVERY" && $v["id"] == 2 && $s["branch"] == "06") {
                            $delivery_method = ["DELIVERY"];
                        }

                        if ($m == "WILL CALL" && $v["id"] == 2 && $s["branch"] == "05") {
                            $delivery_method = ["WILL CALL"];
                        }
                        $store_location = StoreLocation::where("id", $s["store_location"])->first();
                        if ($store_location == null) {
                            continue;
                        }

                        $vendor = VendorMain::where("id", $v["id"])->first();
                        $store_id = $s["store_location"];

                        if ($m == "WILL CALL" && in_array("DELIVERY" . $s["vendor_id"], $repeat_vendor_array)) {
                            continue;
                        } elseif ($m == "DELIVERY" && in_array("WILL CALL" . $s["vendor_id"], $repeat_vendor_array)) {
                            continue;
                        } else {
                            $repeat_vendor_array[] = $m . $s["vendor_id"];
                        }

                        if ($m == "DELIVERY" && $request->has("shipping_method")) {
                            if ($store_location->state != "CA") {
                                continue;
                            }
                        }

                        if (in_array($m, ["WILL CALL", "DELIVERY"]) && $store_location->state != "CA") {
                            continue;
                        }

                        if ($m == "DELIVERY" || $m == "WILL CALL") {
                            $store_id = "288";
                        }

                        $store_location = StoreLocation::where("id", $store_id)->first();
                        $distance = $this->getDistance($customerZip, ["lat" => $store_location->lat, "lon" => $store_location->lon], $check_zip->city);

                        $source_dc = StoreLocation::where("id", $s["store_location"])->first();

                        if (in_array($m, ["DELIVERY", "WILL CALL"])) {
                            $source_distance = $this->getDistance($store_location->zip_code, ["lat" => $source_dc->lat, "lon" => $source_dc->lon], $store_location->city);
                        } else {
                            $source_distance = $distance;
                        }

                        $speed_rating = "";
                        $full_size = "";
                        $model = "";
                        $description = "";
                        $searchResult = $this->search($request->part_number, $request->brand);
                        if ($product_type == 1) {
                            if (!array_key_exists("message", $searchResult)) {
                                $searchResult = $searchResult[0];
                                $speed_rating = $searchResult['speed_rating'];
                                $full_size = $searchResult['section_width'] . "/" . $searchResult['aspect_ratio'] . "R" . $searchResult['rim_diameter'];
                                $model = $searchResult['model'];
                                $description = $searchResult['description'];
                            }
                        } elseif ($product_type == 2) {
                            if (!array_key_exists("message", $searchResult)) {
                                $searchResult = $searchResult[0];
                                $speed_rating = "";
                                $full_size = intval($searchResult['wheel_diameter']) . "X" . intval($searchResult['wheel_width']);
                                $model = $searchResult['model'];
                                $description = $searchResult['description'];
                            }
                        }

                        $vendor_details->vendor_name = $vendor->name . ' - ' . $source_dc->city . ", " . $source_dc->state;

                        $carrier_details = $this->getCarrierRates(
                            $request,
                            $order_id,
                            $shipping_dimensions,
                            $vendor_details,
                            $store_id,
                            $m,
                            $order_qty,
                            $delivery_method,
                            $distance,
                            $product_type
                        );

                        if (!empty($carrier_details['carrier_errors']['errors'])) {
                            $carrier_errors[] = $carrier_details['carrier_errors']['errors'];
                        }

                        if (empty($carrier_details['carrier_details'])) {
                            continue;
                        }

                        $carrier_details = $carrier_details['carrier_details'];

                        //transfer
                        $transfer_vendor = null;
                        $transfer = TransferMapping::where([["brand", $request->brand], ["vendor_main_id", $v["id"]], ["is_active", 1]])->first();

                        $is_transfer = "n";
                        $price_each = floatval($v["netnet"]);
                        $invoice = floatval($v["invoice"]);
                        if ($transfer != null) {
                            if ($transfer->is_active == 1) {
                                $transferNetnet = Netnet::where([['brand', $request->brand], ['mspn', $request->part_number], ["vendor", $transfer->price_vendor_main_id]])->first();
                                if ($transferNetnet != null) {
                                    if (is_array($speed_ratings = json_decode($transfer->speed_rating))) {
                                        if (in_array($speed_rating, $speed_ratings)) {
                                            $is_transfer = "y";
                                            $price_each = $transferNetnet->netnet;
                                            $invoice = $transferNetnet->invoice;
                                            $transfer_vendor = $transfer->price_vendor_main_id;
                                        }
                                    } else {
                                        $is_transfer = "y";
                                        $price_each = $transferNetnet->netnet;
                                        $invoice = $transferNetnet->invoice;
                                        $transfer_vendor = $transfer->price_vendor_main_id;
                                    }
                                }
                            }
                        }
                        //transfer end

                        $price_with_discount = $price_each;
                        if (in_array($m, ['WILL CALL', 'DELIVERY']) && $v['id'] == 5 && in_array($s["store_location"], [165, 173, 189]) && (in_array($channel, [901]) || strpos($channel, "329") !== false)) {
                            $price_with_discount = $price_each - 3.5;
                        }

                        $store_location_no = "";

                        if (array_key_exists('branch', $s)) {
                            $store_location_no = $s["branch"];
                        }

                        if ($v["id"] == 5) {
                            $store_location_no = 1927109;
                        }

                        // if($v["id"] == 2 || $v["id"] == 14 || $v["id"] == 32 || $v["id"] == 177) {

                        // }

                        if ($v["id"] == 5 && $m == "DROPSHIP") {
                            $store_location_no = 1304048;
                        }

                        $is_local_plus = "n";
                        if (array_key_exists("quantity_breakdown", $s)) {
                            if ($s["quantity_breakdown"]["local"] < 1) {
                                $is_local_plus = "y";
                            }
                        }

                        $carrier_from_csv = 0;
                        if ($channel != null && (in_array($channel, [101, "329ST"])) && $request->has("carrier_found") && $request->carrier_found == "1") {
                            $carrier_from_csv = 1;
                            $carrier_details = [$carrier_details[0]];
                        }

                        $product_details = [
                            "mspn" => $request->part_number,
                            "brand" => $request->brand,
                            "model" => $model,
                            "fullsize" => $full_size,
                            "description" => $description,
                            "product_type" => $product_type,
                            "shipping_dimensions" => $shipping_dimensions
                        ];

                        $netnetPrice[] = [
                            "id" => count($netnetPrice) + 1,
                            "is_active" => 1,
                            "uniq_id" => bin2hex(substr($v["id"] . $source_dc->id . $carrier_details[0]["delivery_method"], 0, 10)),
                            "vendor_details" => [
                                "vendor_id" => $v["id"],
                                "vendor_name" => $vendor->name,
                                "vendor_distance" => $source_distance,
                                "store_city" => $source_dc->city . ", " . $source_dc->state,
                                "store_account" => $store_location_no,
                                "store_location_id" => $source_dc->id,
                                "store_zip" => $source_dc->zip_code,
                                "inv" => intval($s["quantity"]),
                                "is_local_plus" => $is_local_plus,
                            ],
                            "price_details" => [
                                "invoice" => floatval($invoice),
                                "cost_each" => $price_each,
                                "total_cost" => $price_each * $order_qty,
                                "final_cost" => $carrier_details[0]["shipping_cost_total"] + ($price_each * $order_qty),
                                "is_transfer" => $is_transfer,
                                "transfer_vendor" => $transfer_vendor,
                                "qty" => $order_qty,
                                "price_with_discount_each" => $price_with_discount,
                                "price_with_discount" => $carrier_details[0]["shipping_cost_total"] + ($price_with_discount * $order_qty),
                            ],
                            "shipping_details" => [
                                "carrier_options" => $carrier_details,
                                "selected_carrier" => $carrier_details[0]
                            ]
                        ];
                    }
                }
            }
        }

        if ($request->has("not_api") || in_array($type = $request->route('type'), ["all"])) {
            if (empty($netnetPrice)) {
                if ($product_type == 2 && !$request->has("dimension") && false) {
                    return response()->json([
                        "message" => "Part number not found",
                        "error_id" => "1",
                        "fullsize" => "",
                        "height" => "",
                        "length" => "",
                        "width" => "",
                        "ship_price" => "",
                        "weight" => "",
                        "product_type" => 2,
                        "line" => __LINE__,
                        "inventory_check_result" => array_values($un_touched_inventory['inventory']),
                    ], 422);
                }
                if (!isset($shipping_dimensions)) {
                    $shipping_dimensions = [
                        "weight" => 0,
                        "length" => 0,
                        "width" => 0,
                        "height" => 0,
                    ];
                }
                return response()->json(["message" => "No stock.", "error_id" => "3", "order_id" => $order_id, "shipping_dimensions" => $shipping_dimensions, "line" => __LINE__, "inventory" => $un_touched_inventory], 422);
            } else {
                $netnetPrice = $this->sortVendorList($netnetPrice, $channel);

                //order history - check stock
                $new_value = [
                    "order_id"  => $order_id,
                    "user_id"   => auth()->user()->id,
                    "vendors"   => [],
                    "product"   => [
                        "brand"         => $product_details["brand"],
                        "part_number"   => $product_details["mspn"],
                        "description"   => $product_details["description"],
                    ]
                ];

                foreach ($netnetPrice as $vendors_found) {
                    $vendor_id_log = $vendors_found['vendor_details']['vendor_id'];
                    $store_location_id_log = $vendors_found["vendor_details"]["store_location_id"];
                    $carrier_id_log = $vendors_found["shipping_details"]['selected_carrier']["carrier"];

                    $price_each = isset($vendors_found['price_details']['cost_each']) ? $vendors_found['price_details']['cost_each'] : ''; // eunise
                    $vendor_inventory = isset($vendors_found['vendor_details']['inv'])  ? $vendors_found['vendor_details']['inv'] : ''; // eunise
                    $shipping_cost = isset($vendors_found['shipping_details']['selected_carrier']['shipping_cost_each'])  ? $vendors_found['shipping_details']['selected_carrier']['shipping_cost_each'] : ''; // eunise

                    $new_value["vendors"][] = [
                        "order_source" => [
                            "vendor_main_id" => $vendor_id_log,
                            "store_location_id" => $store_location_id_log,
                            "carrier" => $carrier_id_log
                        ],
                        "price_details" => [ // eunise
                            "cost_each" => $price_each,
                            "shipping_cost_each" => $shipping_cost
                        ],
                        "inventory_details" => [ // eunise
                            "quantity" => $vendor_inventory
                        ]
                    ];
                }

                if (count($no_stock_vendor) > 0) {
                    $new_value['other_vendor'] = $no_stock_vendor;
                }

                if ($request->has("dimension")) {
                    $logdimension = $request->dimension;
                } else {
                    $logdimension = $netnetPrice[0]['shipping_details']['selected_carrier']['dimensions'];
                } // eunise

                $initial_size = [
                    "order_id"      => $order_id,
                    "user_id"       => auth()->user()->id,
                    "dimensions"    => $logdimension
                ];

                DB::table("LogDetails")
                    ->insert([
                        [
                            'order_list_id'     => $order_id,
                            'event_id'          => 15,
                            'original_value'    => 'initial_size',
                            'new_value'         => json_encode($initial_size)
                        ],
                        [
                            'order_list_id'     => $order_id,
                            'event_id'          => 8,
                            'original_value'    => 'inventory_status',
                            'new_value'         => json_encode($new_value)
                        ]

                    ]);

                /*if($type == "one"){
                    return response()->json([
                        "product_details" => $product_details,
                        "order_id" => $order_id,
                        "carrier_from_csv" => $carrier_from_csv,
                        "available_vendors" => $netnetPrice[0],
                        "selected_vendors" => $selected_vendors,
                    ]);
                } else*/
                $total = [];
                $selected_vendors = [];
                $unique_vendors = [];
                $selected_vendors_hex = [];
                $repeat = 1;
                if ($request->has("selected_vendors")) {
                    $repeat++;
                    foreach ($request->selected_vendors as $selected_vendor) {
                        if (array_key_exists("is_bypassed", $selected_vendor)) {
                            $selected_vendors[] = $selected_vendor;
                            $quantity_allocation -= $selected_vendor["get_qty"];
                        }
                    }
                }

                for ($i = 0; $i < $repeat; $i++) {
                    foreach ($netnetPrice as &$value) {
                        if ($repeat == 2 && $i == 0) {
                            $found_keys = array_keys(array_column($request->selected_vendors, "vendor_id"), $value["vendor_details"]["vendor_id"]);
                            if (count($found_keys) > 0) {
                                $vendor_found = false;
                                foreach ($found_keys as $fkeys) {
                                    $hex = bin2hex(substr($value["vendor_details"]["vendor_id"] . $value["vendor_details"]["store_location_id"] . $value["shipping_details"]["selected_carrier"]["delivery_method"], 0, 10));
                                    if ($request->selected_vendors[$fkeys]["store_id"] == $value["vendor_details"]["store_location_id"] && $hex == $request->selected_vendors[$fkeys]["uniq_id"]) {
                                        $vendor_found = true;
                                        $assigned_quantity = $request->selected_vendors[$fkeys]["get_qty"] <= $value["vendor_details"]["inv"] ? $request->selected_vendors[$fkeys]["get_qty"] : $value["vendor_details"]["inv"];
                                        $quantity_used_up = false;
                                        $selected_vendors_hex[] = $hex;
                                        break;
                                    }
                                }
                                if (!$vendor_found) {
                                    continue;
                                }
                            } else {
                                continue;
                            }
                        } else {
                            if (in_array(bin2hex(substr($value["vendor_details"]["vendor_id"] . $value["vendor_details"]["store_location_id"] . $value["shipping_details"]["selected_carrier"]["delivery_method"], 0, 10)), $selected_vendors_hex)) {
                                continue;
                            }
                            if (in_array($value["vendor_details"]["vendor_id"] . $value["vendor_details"]["store_location_id"], $unique_vendors)) {
                                continue;
                            }
                            $unique_vendors[] = $value["vendor_details"]["vendor_id"] . $value["vendor_details"]["store_location_id"];
                            $assigned_quantity = $quantity_allocation <= $value["vendor_details"]["inv"] ? $quantity_allocation : $value["vendor_details"]["inv"];
                            // $quantity_allocation -= $assigned_quantity;
                            $quantity_used_up = false;
                        }
                        if ($assigned_quantity < 1) {
                            $quantity_used_up = true;
                        }
                        if (!$quantity_used_up) {
                            $delivery_method_options = $value["shipping_details"]["selected_carrier"]["delivery_method"];
                            if ($delivery_method_options == "DELIVERY") {
                                $store_id = "288";
                            } else {
                                $store_id = $value["vendor_details"]["store_location_id"];
                            }
                            $vendor_statement = [["vendor_main_id", $value["vendor_details"]["vendor_id"]]];
                            if ($value["vendor_details"]["vendor_id"] == 5) {
                                if ($delivery_method_options == "DELIVERY") {
                                    $vendor_statement[] = ["api_type", 2];
                                } else {
                                    $vendor_statement[] = ["api_type", 3];
                                }
                            }

                            $vendor_details = DB::table("vendor_main_details")->where($vendor_statement)->first();

                            $vendor_details->vendor_name = $value["vendor_details"]["store_city"];

                            $carrier_details = $this->getCarrierRates(
                                $request,
                                $order_id,
                                $product_details["shipping_dimensions"],
                                $vendor_details,
                                $store_id,
                                $delivery_method_options,
                                intval($assigned_quantity),
                                $value["shipping_details"]["selected_carrier"]["delivery_options"],
                                floatval($value["shipping_details"]["selected_carrier"]["distance"]),
                                $product_details['product_type']
                            );

                            if (empty($carrier_details)) {
                                continue;
                            }

                            $carrier_details = $carrier_details['carrier_details'];

                            $carrier_from_csv = 0;
                            if ($channel != null && (in_array($channel, [101, "329ST"])) && $request->has("carrier_found") && $request->carrier_found == "1") {
                                $carrier_from_csv = 1;
                                $carrier_details = [$carrier_details[0]];
                            }

                            $selected_vendors[] = [
                                "vendor_id" => $value["vendor_details"]["vendor_id"],
                                "store_id" => $value["vendor_details"]["store_location_id"],
                                "get_qty" => intval($assigned_quantity),
                                "id" => $value["id"],
                                "uniq_id" => bin2hex(substr($value["vendor_details"]["vendor_id"] . $value["vendor_details"]["store_location_id"] . $value["shipping_details"]["selected_carrier"]["delivery_method"], 0, 10)),
                            ];

                            $value["get_qty"] = intval($assigned_quantity);
                            $value["shipping_details"]["carrier_options"] = $carrier_details;
                            $value["shipping_details"]["selected_carrier"] = $carrier_details[0];
                            $value["price_details"]["total_cost"] = round($value["price_details"]["cost_each"] * $assigned_quantity, 2);
                            $value["price_details"]["final_cost"] = round($carrier_details[0]["shipping_cost_total"] + ($value["price_details"]["cost_each"] * $assigned_quantity), 2);
                            $value["price_details"]["qty"] = intval($assigned_quantity);
                            $value["price_details"]["price_with_discount"] = round($carrier_details[0]["shipping_cost_total"] + ($value["price_details"]["price_with_discount_each"] * $assigned_quantity), 2);
                            $quantity_allocation -= $assigned_quantity;
                            if (array_key_exists("shipping_cost_total", $total)) {
                                $total["shipping_cost_total"] += $carrier_details[0]["shipping_cost_total"];
                                $total["total_cost"] += $value["price_details"]["cost_each"] * $value["price_details"]["qty"];
                            } else {
                                $total = [
                                    "shipping_cost_total" => $carrier_details[0]["shipping_cost_total"],
                                    "total_cost" => $value["price_details"]["cost_each"] * $value["price_details"]["qty"]
                                ];
                            }
                        }
                    }
                }

                DB::table("orderDetails")
                    ->where("order_list_id", $order_id)
                    ->update([
                        "original_dimensions" => $product_details["shipping_dimensions"]
                    ]);

                if (!empty($request->get('promo'))) {
                    $promo_table = DB::table("promotions")->where("promo_code", $request->get('promo'))->first();
                    if ($promo_table != null) {
                        if ($promo_table->promo_type_id == 1) {
                            $total['discounted_selling_price'] = $request->get('selling_price') - ($request->get('selling_price') * ($promo_table->discount / 100));
                        } else if ($promo_table->promo_type_id == 2) {
                            $total['discounted_selling_price'] = $request->get('selling_price') - $promo_table->discount;
                        } else if ($promo_table->promo_type_id == 3) {
                            $total['discounted_selling_price'] = $request->get('selling_price') - $promo_table->discount;
                        }
                        $total['test'] = $promo_table->discount;
                    }
                }

                $response = [
                    "product_details" => $product_details,
                    "order_id" => $order_id,
                    "carrier_from_csv" => $carrier_from_csv,
                    "available_vendors" => $netnetPrice,
                    "selected_vendors" => $selected_vendors,
                    "order_total" => $total,
                    "inventory_check_result" => array_values($un_touched_inventory['inventory']),
                    "filled_quantity" => $quantity_allocation,
                    "carrier_errors" => $carrier_errors
                ];
                DB::table('APIresponse')->insert([
                    'orderID' => $order_id,
                    'ApitypeID' => 27,
                    'Request' => json_encode($request->all()),
                    'Response' => json_encode($response)
                ]);
                return response()->json($response);
            }
        } else {
            abort(404);
        }
    }
    public function getCity(Request $request)
    {
        $zip_details = [['zip_code', $request->zip]];
        $zips = ZipCodes::where($zip_details)->get();
        if ($request->has("latitude") && $request->has("longitude")) {
            $objectData = (object) ["city" => $request->city, "lat" => $request->latitude, "lon" => $request->longitude, "state" => $request->state, "zip_code" => $request->zip];
            return $objectData;
        } elseif (count($zips) < 1) {
            $zip_data = $this->getZipData($request);
            if ($zip_data != false) {
                return $zip_data;
            }
            return ["message" => "Zipcode not found.", "error_id" => "11", "line" => __LINE__];
        } elseif (count($zips) > 1) {
            if ($request->has("city")) {
                $zip_details[] = ['city', "like", "%" . preg_replace("/\s+/i", "%", $request->city) . "%"];
                $zip_with_city = DB::table('zip_code')->where($zip_details)->get();
                if (count($zip_with_city) < 1) {
                    $zipcodes = [];
                    foreach ($zips as $key => $value) {
                        $zipcodes[] = $value->city;
                    }
                    $zip_data = $this->getZipData($request);
                    if ($zip_data !== false) {
                        return $zip_data;
                    } else {
                        return ["message" => "City not found.", "error_id" => "6", "cities" => $zipcodes];
                    }
                } else {
                    if ($request->has("state")) { //tan wrong state
                        $zip_details[] = ['state', "like", "%" . preg_replace("/\s+/i", "%", $request->state) . "%"];
                        $zip_with_state = Zipcodes::where($zip_details);
                        if ($zip_with_state->get()->count() < 1) {
                            $zip_data = $this->getZipData($request);
                            if ($zip_data !== false) {
                                return $zip_data;
                            } else {
                                return ["message" => "State not found.", "error_id" => "10"];
                            }
                        } else {
                            return Zipcodes::where($zip_details)->first();
                        }
                    } else {
                        return Zipcodes::where($zip_details)->first();
                    }
                }
            } else {
                return Zipcodes::where($zip_details)->first();
            }

            // $zipcodes = [];
            // foreach($zips as $key => $value){
            //     $zipcodes[] = $value->city;
            // }
            // return ["message"=>"Please specify city.","city"=>$zipcodes,"state"=>$value->state,"error_id"=>"7"];
        } else {
            if ($request->has("batch")) {
                $zip_details[] = ['city', "like", "%" . preg_replace("/\s+/i", "%", $request->city) . "%"];
                $zip_with_city = DB::table('zip_code')->where($zip_details)->get();
                if (count($zip_with_city) < 1) {
                    $zipcodes = [];
                    foreach ($zips as $key => $value) {
                        $zipcodes[] = $value->city;
                    }
                    $zip_data = $this->getZipData($request);
                    if ($zip_data !== false) {
                        return $zip_data;
                    } else {
                        return ["message" => "City not found.", "error_id" => "6", "cities" => $zipcodes];
                    }
                } else {
                    if ($request->has("state")) { //tan wrong state
                        $zip_details[] = ['state', "like", "%" . preg_replace("/\s+/i", "%", $request->state) . "%"];
                        $zip_with_state = DB::table('zip_code')->where($zip_details)->get();
                        if (count($zip_with_state) < 1) {
                            $zip_data = $this->getZipData($request);
                            if ($zip_data !== false) {
                                return $zip_data;
                            } else {
                                return ["message" => "State not found.", "error_id" => "10"];
                            }
                        } else {
                            return DB::table('zip_code')->where($zip_details)->first();
                        }
                    } else {
                        return DB::table('zip_code')->where($zip_details)->first();
                    }
                }
            } else {
                return Zipcodes::where($zip_details)->first();
            }
        }
    }
    public function getItemDimension($section_width, $aspect_ratio, $diameter){
        if(empty($aspect_ratio)) {
            $aspect_ratio = 0;
        }
        if(strlen($section_width) == 3){
            $height = $section_width / 25.4;
            $length = ($section_width * $aspect_ratio / 2540 * 2) + $diameter;
            $width = $length;
        } else {
            $height = (float)$aspect_ratio + (float)$diameter;
            $length = $section_width;
            $width = $section_width;
        }
        $avg_weight = DB::table("strapping")->where([["length",ceil($length)],["height",ceil($height)]])->avg("final_weight");
        if($avg_weight != ""){
            $ship_price = $avg_weight * .71;
        } else {
            $avg_weight = 0;
            $ship_price = 0;
        }
        
        return [
            "message" => "Size not found",
            "error_id" => "1",
            "fullsize" => $section_width."/".$aspect_ratio."R".$diameter,
            "section_width" => $section_width,
            "aspect_ratio" => $aspect_ratio,
            "diameter" => $diameter,
            "height" => ceil($height),
            "length" => ceil($length),
            "width" => ceil($width),
            "ship_price" => $ship_price,
            "weight" => ceil($avg_weight),
            "product_type" => 1,
            "line" => __LINE__,
        ];
    }

    public function getGSO($data){
        return 10;
        $curl = curl_init();
        $headers = [];
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.gso.com/Rest/v1/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Username: WholesaleTireWS",
                "Password: qseRddQrlc"
            ),
            CURLOPT_HEADERFUNCTION => function($curl, $header) use (&$headers){
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2)
                    return $len;
                
                $headers[strtolower(trim($header[0]))][] = trim($header[1]);
                
                return $len;
            }
        ));
        
        $response = curl_exec($curl);
        curl_close($curl);
        if($response == "\"Authorized\""){
            $curl = curl_init();
            
            $request = [
                'AccountNumber' => '73710', 
                'DeclaredValue' => 0, 
                'CODValue' => false, 
                'SignatureCode' => 'SIG_NOT_REQD', 
                'DeliveryAddressType' => 'R', 
                'ShipDate' => date("m-d-Y"), 
                'PackageWeight' => $data["PackageWeight"], 
                'Length' => $data["Length"], 
                'Width' => $data["Width"], 
                'Height' => $data["Height"], 
                'OriginZip' => $data["OriginZip"], 
                'DestinationZip' => $data["DestinationZip"], 
            ];
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.gso.com/Rest/v1/RatesAndTransitTimes",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($request),
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Accept-Encoding: gzip",
                    "token: ".$headers["token"][0],
                    "Cookie: SERVERID=EQXWSA05|X7vJ7|X7vEN"
                ),
            ));
            
            $response = curl_exec($curl);
            
            DB::table('APIresponse')->insert([
                'orderID' => $data["order_id"],
                'ApitypeID' => 20,
                'Request' => preg_replace("/\s+/i","", json_encode($request)),
                'Response' => $response
            ]);
            
            $response = json_decode($response,true);
            
            curl_close($curl);
            if(is_array($response) && array_key_exists("DeliveryServiceTypes",$response) && is_array($response["DeliveryServiceTypes"])){
                foreach($response["DeliveryServiceTypes"] as $types => $type){
                    if($type["ServiceCode"] == "CPS"){
                        return $type["ShipmentCharges"]["TotalCharge"];
                    }
                }
            }
            if($response['ErrorCount'] > 0){
                $msg =  array_column($response["ErrorDetail"], 'ErrorDescription');
                $error_msg = implode(",", $msg);

                $error_log = [
                    'order_id' => $data["order_id"],
                    "user_id" =>  auth()->user()->id,
                    "carrier" => "GSO",
                    "api_error" => $error_msg
                ];

                DB::table("LogDetails")
                ->insert(
                    ['order_list_id' => $data["order_id"], 'event_id' => 19, 'original_value' => 'carrier_api_error', 'new_value' => json_encode($error_log)]
                );

            }
            return ["status" => "error","message" => $response];
        } else {
            return ["status" => "error","message" => $response,"additional_error_details"=>"authorize error"];
        }
    }

    public function brandCheck(Request $request,$powersearch = ""){
        if($powersearch == ""){
            $powersearch = $request->powersearch;
        }
        
        $searchResult = $this->search($request->powersearch);
        if(array_key_exists("message", $searchResult)){
            return response()->json($searchResult, 422);
        } else {
            return $searchResult;
        }
    }

    public function searchSize(Request $request) {
        $collection = DB::table('catalog')
            ->select("catalog.brand","catalog.mspn","section_width","aspect_ratio","rim_diameter","description","model","image_url_full",DB::raw("sum(inventory_feed.qty) as qty"),DB::raw("MIN(`netnet_price`.`netnet`) as cost"))
            ->leftJoin("inventory_feed", function($join) {
                $join->on('catalog.brand', '=', 'inventory_feed.brand');
                $join->on('catalog.mspn', '=', 'inventory_feed.part_number');
            })
            ->leftJoin('vendor_main', 'vendor_main.id','=','inventory_feed.vendor_main_id')
            ->leftJoin("store_location", function($join) {
                $join->on('store_location.vendor_main_id', '=', 'inventory_feed.vendor_main_id');
                $join->on('store_location.id', '=', 'inventory_feed.store_location_id');
            })
            ->leftJoin("netnet_price", function($join) {
                $join->on('netnet_price.vendor', '=', 'inventory_feed.vendor_main_id');
                $join->on('netnet_price.brand', '=', 'inventory_feed.brand');
                $join->on('netnet_price.mspn', '=', 'inventory_feed.part_number');
            })
            ->where([["vendor_main.is_active",1],["store_location.is_active",1],["inventory_feed.is_active",1],[DB::raw("REGEXP_REPLACE(CONCAT(section_width,aspect_ratio,rim_diameter), '[^0-9]', '')"),$request->size]])
        	->whereNotNull('netnet_price.mspn')
            ->groupBy(DB::raw("CONCAT(inventory_feed.brand,inventory_feed.part_number)"))
            ->get();
        
        $tire_library = DB::table('tire_library')
            ->select("tire_library.brand","tire_library.mspn","section_width","aspect_ratio","rim_diameter","description","model",DB::raw("\"\" as image_url_full"),DB::raw("sum(inventory_feed.qty) as qty"),DB::raw("MIN(`netnet_price`.`netnet`) as cost"))
            ->leftJoin("inventory_feed", function($join) {
                $join->on('tire_library.brand', '=', 'inventory_feed.brand');
                $join->on('tire_library.mspn', '=', 'inventory_feed.part_number');
            })
            ->leftJoin('vendor_main', 'vendor_main.id','=','inventory_feed.vendor_main_id')
            ->leftJoin("store_location", function($join) {
                $join->on('store_location.vendor_main_id', '=', 'inventory_feed.vendor_main_id');
                $join->on('store_location.id', '=', 'inventory_feed.store_location_id');
            })
            ->leftJoin("netnet_price", function($join) {
                $join->on('netnet_price.vendor', '=', 'inventory_feed.vendor_main_id');
                $join->on('netnet_price.brand', '=', 'inventory_feed.brand');
                $join->on('netnet_price.mspn', '=', 'inventory_feed.part_number');
            })
            ->where([["vendor_main.is_active",1],["store_location.is_active",1],["inventory_feed.is_active",1],[DB::raw("REGEXP_REPLACE(CONCAT(section_width,aspect_ratio,rim_diameter), '[^0-9]', '')"),$request->size]])
        	->whereNotNull('netnet_price.mspn')
            ->groupBy(DB::raw("CONCAT(inventory_feed.brand,inventory_feed.part_number)"))
            ->get();
        
        $collection = $collection->merge($tire_library);
        $unique = $collection->unique(function ($item) {
            return $item->brand.$item->mspn;
        });
        return $unique->values()->all();
    }

    public function getZipData(Request $request)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.zipcodeapi.com/rest/D0bSYebU5VuwkGdkvOI6ypQxKplXNyfjbr1LgcgXJCnoAk4LFmI4oBv1HtNR0xry/info.json/' . $request->get("zip"),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);

        curl_close($curl);
        if (isset($info['http_code']) && $info['http_code'] == 200) {
            $zip_data = json_decode($response);
            if ($zip_data->lat == null || $zip_data->lng == null) {
                return false;
            }
            $zip = new ZipCodes;
            $zip->zip_code = $request->get("zip");
            $zip->lat = $zip_data->lat;
            $zip->lon = $zip_data->lng;
            $zip->city = preg_replace("/\s+/i", " ", $request->get("city"));
            $zip->state = $zip_data->state;
            $zip->save();

            return $zip;
        } else {
            return false;
        }
    }

    public function search($partNumber = "", $brand = "")
    {
        $value = [['mspn', $partNumber]];
        if ($brand != "") {
            $value[] = ['brand', $brand];
        }

        $result = Catalog::where($value)
            ->select(
                'wheel_diameter',
                'wheel_width',
                'category',
                'speed_rating',
                'description',
                'section_width',
                'aspect_ratio',
                'rim_diameter',
                'brand',
                'mspn',
                'model',
                'weight_package',
                'weight_tire',
                'height_package',
                'length_package',
                'width_package',
                DB::raw('CONCAT(section_width,aspect_ratio,rim_diameter) as unformatted_size'),
                DB::raw('CONCAT(CAST(`section_width` AS DECIMAL),CAST(`aspect_ratio` AS DECIMAL),CAST(`rim_diameter` AS DECIMAL)) as fullsize')
            )
            ->groupBy(
                'brand',
                'wheel_diameter',
                'wheel_width',
                'category',
                'speed_rating',
                'description',
                'section_width',
                'aspect_ratio',
                'rim_diameter',
                'mspn',
                'model',
                'weight_package',
                'weight_tire',
                'height_package',
                'length_package',
                'width_package'
            )
            ->get();

        if (count($result) == 1) {
            $res = $result;
            if (($res[0]->unformatted_size == '' || $res[0]->unformatted_size == null) && $res[0]->category == 1) {
                $tire_library = DB::table('tire_library')
                    ->where('brand', $res[0]->brand)
                    ->where('mspn', $res[0]->mspn)
                    ->first();
                if ($tire_library) {
                    $res[0]->section_width = $tire_library->section_width;
                    $res[0]->aspect_ratio = $tire_library->aspect_ratio;
                    $res[0]->rim_diameter = $tire_library->rim_diameter;
                    $res[0]->unformatted_size = $tire_library->section_width . '/' . $tire_library->aspect_ratio . 'R' . $tire_library->rim_diameter;
                    $res[0]->fullsize = $tire_library->section_width . $tire_library->aspect_ratio . $tire_library->rim_diameter;
                }
                return $res->toArray();
            } else {
                return $res->toArray();
            }
        } elseif (count($result) > 1) {
            $brand = [];
            foreach ($result as $value) {
                $brand[] = $value->brand;
            }
            return ["message" => "Please specify brand", ["brand" => $brand], "error_id" => "5"];
        } else {
            $result = DB::table('tire_library')
                ->where($value)
                ->select(
                    DB::raw('"" as wheel_diameter'),
                    DB::raw('"" as wheel_width'),
                    DB::raw('1 as category'),
                    DB::raw('"" as speed_rating'),
                    'description',
                    'section_width',
                    'aspect_ratio',
                    'rim_diameter',
                    'brand',
                    'mspn',
                    'model',
                    DB::raw('"" as weight_package'),
                    DB::raw('CONCAT(`section_width`,"/",`aspect_ratio`,"R",`rim_diameter`) as unformatted_size'),
                    DB::raw('CONCAT(`section_width`,`aspect_ratio`,`rim_diameter`) as fullsize'),
                    DB::raw('0 as weight_tire'),
                    DB::raw('"" as height_package'),
                    DB::raw('"" as length_package'),
                    DB::raw('"" as width_package')
                )
                ->groupBy('brand')
                ->get();
            if (count($result) == 1) {
                return $result->toArray();
            } elseif (count($result) > 1) {
                $brand = [];
                foreach ($result as $value) {
                    $brand[] = $value->brand;
                }
                return ['message' => 'Please specify brand', ['brand' => $brand], 'error_id' => 5];
            } else {
                return ['message' => 'Part number not found', 'error_id' => 12];
            }
        }
    }

    public function saveMissingData(Request $request, $order_id)
    {
        $orderDetails = DB::table("orderDetails")->where("order_list_id", $order_id)->first();
        if ($orderDetails != null && $order_id != "") {
            $missing_data = json_decode($orderDetails->missing_data, true) ?? [];
            if ($request->has("dimension")) {
                if (!empty($missing_data)) {
                    $missing_data["dimension"] = $request->dimension;
                } else {
                    $missing_data = ["dimension" => $request->dimension];
                }
            }

            if ($request->has("size")) {
                if (array_key_exists("clean_size", $request->size)) {
                    $size = $request->size["clean_size"];
                } else {
                    if (array_key_exists("section_width", $request->size)) {
                        $section_width = floatval($request->size["section_width"]);
                        $aspect_ratio = floatval($request->size["aspect_ratio"]) == 0 ? "" : "/" . floatval($request->size["aspect_ratio"]);
                        $diameter = floatval($request->size["diameter"]);
                        $size = $section_width . $aspect_ratio . "R" . $diameter;
                    } else {
                        $size = $request->size["wheel_diameter"] . "X" . $request->size["wheel_width"];
                    }
                }
                if (!empty($missing_data)) {
                    $missing_data["size"] = $size;
                } else {
                    $missing_data = ["size" => $size];
                }
            }

            if ($request->has("product_type")) {
                if (!empty($missing_data)) {
                    $missing_data["product_type"] = $request->product_type;
                } else {
                    $missing_data = ["product_type" => $request->product_type];
                }
            }

            if ($request->has("latitude") && $request->has("longitude")) {
                if (!empty($missing_data)) {
                    $missing_data["latitude"] = $request->latitude;
                    $missing_data["longitude"] = $request->longitude;
                } else {
                    $missing_data = ["latitude" => $request->latitude, "longitude" => $request->longitude];
                }
            }

            if ($request->has("latitude")) {
                if (!empty($missing_data)) {
                    $missing_data["netnet"] = $request->netnet;
                } else {
                    $missing_data = ["netnet" => $request->netnet];
                }
            }

            if ($request->has("brand_id")) {
                if (!empty($missing_data)) {
                    $missing_data["brand_id"] = $request->brand_id;
                    $missing_data["vast_description"] = $request->vast_description;
                    $missing_data["account_code"] = $request->account_code;
                } else {
                    $missing_data = ["brand_id" => $request->brand_id, "vast_description" => $request->vast_description, "account_code" => $request->account_code];
                }
            }
            DB::table("orderDetails")->where("order_list_id", $order_id)->update(["missing_data" => json_encode($missing_data)]);
        }
    }

    public function sortVendorList($netnetPrice,$channel)
    {
        $price_with_discount = [];
        $distance = [];
        foreach($netnetPrice as $key => $value) {
            $price_with_discount[$key] = $value['price_details']['price_with_discount'];
            $distance[$key] = floatval(str_replace(",","",$value['shipping_details']['carrier_options'][0]['distance']));
            $vendor_distance[$key] = floatval(str_replace(",","",$value['vendor_details']['vendor_distance']));
        }
        $unsort_netnetPrice = $netnetPrice;
        array_multisort($price_with_discount, SORT_ASC, $distance, SORT_ASC, $vendor_distance, SORT_ASC, $netnetPrice);

        $atd = [];
        // if($netnetPrice[0]["vendor_details"]["vendor_id"] == 5 && in_array($netnetPrice[0]["vendor_details"]["store_location_id"],[165,173,189]) && (in_array($channel,[901]) || strpos($channel, "329") !== false)){
        //     $delivery_method = $netnetPrice[0]["shipping_details"]['selected_carrier']['delivery_options'];
        //     if(in_array("WILL CALL",$delivery_method) || in_array("DELIVERY",$delivery_method)){
        //         $atd[] = $netnetPrice[0];
        //         unset($netnetPrice[0]);
        //     } else {
        //         $final_price = [];
        //         $distance = [];
        //         $is_active = [];
        //         foreach($unsort_netnetPrice as $key => $value) {
        //             $final_price[$key] = $value['price_details']['final_cost'];
        //             $distance[$key] = floatval(str_replace(",","",$value['shipping_details']['carrier_options'][0]['distance']));
        //             $vendor_distance[$key] = floatval(str_replace(",","",$value['vendor_details']['vendor_distance']));
        //         }
        //         array_multisort($final_price, SORT_ASC, $distance, SORT_ASC, $vendor_distance, SORT_ASC, $unsort_netnetPrice);
        //         $netnetPrice = $unsort_netnetPrice;
        //     }
        // } else {
            $final_price = [];
            $distance = [];
            foreach($unsort_netnetPrice as $key => $value) {
                $final_price[$key] = $value['price_details']['final_cost'];
                $distance[$key] = floatval(str_replace(",","",$value['shipping_details']['carrier_options'][0]['distance']));
                $vendor_distance[$key] = floatval(str_replace(",","",$value['vendor_details']['vendor_distance']));
            }
            array_multisort($final_price, SORT_ASC, $distance, SORT_ASC, $vendor_distance, SORT_ASC, $unsort_netnetPrice);
            $netnetPrice = $unsort_netnetPrice;
        // }
        
        $wtd = [];
        foreach($netnetPrice as $key => $value){
            if(in_array($value["vendor_details"]["vendor_id"], [6, 82])){
                $wtd[] = $value;
                unset($netnetPrice[$key]);
            }
        }
        
        $vendor_distance = [];
        $final_price = [];
        $distance = [];
        foreach($wtd as $key => $value) {
            $final_price[$key] = $value['price_details']['final_cost'];
            $distance[$key] = floatval(str_replace(",","",$value['shipping_details']['carrier_options'][0]['distance']));
            $vendor_distance[$key] = floatval(str_replace(",","",$value['vendor_details']['vendor_distance']));
        }
        array_multisort($final_price, SORT_ASC, $distance, SORT_ASC, $vendor_distance, SORT_ASC, $wtd);
        
        $atv = [];
        foreach($netnetPrice as $key => $value){
            if($value["vendor_details"]["vendor_id"] == 13){
                $atv[] = $value;
                unset($netnetPrice[$key]);
            }
        }
        $final_sort = array_merge($atd, $wtd, $netnetPrice, $atv);

        if($channel == 901) {
            $atd_sth_key = false;
            $atd_connect = false;
            foreach($final_sort as $key => $vendors) {
                if($vendors["vendor_details"]["vendor_id"] == 5) {
                    if($vendors["vendor_details"]["store_account"] == 1304048) { // ATD STH
                        $atd_sth_key = $key;
                    }
                    
                    if($atd_sth_key !== false && $atd_sth_key < $key && $vendors["vendor_details"]["store_account"] == 1927109) { // ATD Connect
                        $atd_connect = [$vendors];
                        unset($final_sort[$key]);
                        break;
                    }
                }
            }
            
            if($atd_sth_key !== false && $atd_connect !== false) {
                array_splice($final_sort, $atd_sth_key, 0, $atd_connect);
            }
        }
        return array_values($final_sort);
    }

    public function getDistance($to, $from, $cityTo = ""){
        $zipToValue = [['zip_code', $to]];
        if($cityTo != "")
            $zipToValue[] = ['city', $cityTo];
        $earthRadius = 3959;
    	$zipTo = ZipCodes::where($zipToValue)->first();
    	if(!$zipTo){
    	    return false;
    	}
    	
        $latFrom = deg2rad($from["lat"]);
        $lonFrom = deg2rad($from["lon"]);
        $latTo = deg2rad($zipTo->lat);
        $lonTo = deg2rad($zipTo->lon);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    public function getCarrierRates(
        Request $request,
        $order_id,
        $shipping_dimensions,
        $vendor_details,
        $store_id,
        $delivery_method,
        $assigned_quantity,
        $delivery_method_options,
        $distance,
        $product_type
    )
    {
        $carrier_errors = [
            'errors' => []
        ];
        $carrier_details = [];
        if(in_array($delivery_method, ["DELIVERY", "WILL CALL"])){
            $is_vendor_strappable = 1;
        } else {
            $is_vendor_strappable = $vendor_details->is_strap;
        }

        $order_data = OrderList::find($order_id);

        $storeLocation = StoreLocation::where("id", $store_id)->first();
        $channels = DB::table("channels")->where("channel_code", $order_data->orderDetail->channel)->first();
        if($channels->is_strap == 1 && $is_vendor_strappable == 1){
            $is_vendor_strappable = 1;
        } else {
            $is_vendor_strappable = 0;
        }
        
        $carrier_dimensions = [];
        $carriers = DB::table("shippingCarrier")->where("status", 1)->get();
        $dimension_multiplier = 1;
        if($product_type == 3) {
            $dimension_multiplier = intval($assigned_quantity);
            $assigned_quantity = 1;
        }
        if($carriers != null && $channels != null && is_array($vendor_carriers = json_decode($vendor_details->carriers, true)) && is_array($channel_carriers = json_decode($channels->carriers, true))){
            if($request->has("shipping_method")){
                $vendor_carriers = array_merge($vendor_carriers, [7]);
                $channel_carriers = array_merge($channel_carriers, [7]);
            }

            $kuehne_nagel_carriers = [];
            foreach($carriers as $carrier){
                $carrier_delivery_method = json_decode($carrier->delivery_method, true);
                if(is_array($carrier_delivery_method) && $carrier->status == 1){
                    if($vendor_details->vendor_main_id == 2 && $carrier->id == 3) {
                        $carrier_delivery_method = array_merge($carrier_delivery_method, ['DROPSHIP']);
                    }

                    if(in_array($delivery_method, $carrier_delivery_method) && in_array($carrier->id, $vendor_carriers) && in_array($carrier->id, $channel_carriers)){
                        if(is_array(json_decode($carrier->channels, true)) && !in_array($channels->channel_code, json_decode($carrier->channels, true))){
                            continue;
                        }
                        
                        $shipping_price = [];
                        $is_ltl = "n";
                        if(!$request->has("shipping_method") && $carrier->id == 3 && $shipping_dimensions["product_type"] == 1 && in_array(trim($order_data->orderDetail->state), ["CA", "AZ", "NV", "NM", "OR", "WA", "ID", "UT"])){
                            $data = [
                                "PackageWeight" => floor($shipping_dimensions["weight"]) * $dimension_multiplier,
                                "Length" => floor($shipping_dimensions["length"]),
                                "Width" => floor($shipping_dimensions["width"]),
                                "Height" => floor($shipping_dimensions["height"]) * $dimension_multiplier,
                                "OriginZip" => substr($storeLocation->zip_code,0,5),
                                "DestinationZip" => $order_data->orderDetail->postal_code,
                                "order_id" => $order_id
                            ];
                            $gso_price = $this->getGSO($data);
                            
                            if(is_array($gso_price)){
                                continue;
                            }
                            $is_strap = "n";
                            $shipping_price[] = ["shipping_price_basis" => "single", "price" => $gso_price];
                            
                            if($shipping_dimensions["weight"] < 75 && $shipping_dimensions["length"] <= 31.9 && $assigned_quantity >= 2){
                                $data = [
                                    "PackageWeight" => floor($shipping_dimensions["weight"]) * 2,
                                    "Length" => floor($shipping_dimensions["length"]),
                                    "Width" => floor($shipping_dimensions["width"]),
                                    "Height" => floor($shipping_dimensions["height"]) * 2,
                                    "OriginZip" => substr($storeLocation->zip_code,0,5),
                                    "DestinationZip" => $order_data->orderDetail->postal_code,
                                    "order_id" => $order_id
                                ];
                                $gso_price_strap = $this->getGSO($data);
                                if(!is_array($gso_price_strap)){
                                    $shipping_price[] = ["shipping_price_basis" => "strap", "price" => $gso_price_strap];
                                    $is_strap = "y";
                                }
                            }
                        } elseif(!$request->has("shipping_method") && in_array(intval($carrier->id), [1, 9])) {
                            $data = [
                                'to' => [
                                    'StreetLines' => [],
                                    'City' => $order_data->orderDetail->city,
                                    'StateOrProvinceCode' => $order_data->orderDetail->state,
                                    'PostalCode' => $order_data->orderDetail->postal_code,
                                    'CountryCode' => 'US',
                                    'Residential' => true
                                ],
                                'from' => [
                                    'StreetLines' => [$storeLocation->addr],
                                    'City' => $storeLocation->city,
                                    'StateOrProvinceCode' => $storeLocation->state,
                                    'PostalCode' => substr($storeLocation->zip_code,0,5),
                                    'CountryCode' => 'US'
                                ],
                                'dimension' => [
                                    'SequenceNumber' => 1,
                                    'GroupPackageCount' => 1,
                                    'Weight' => [
                                        'Value' => floatval($shipping_dimensions["weight"]) * $dimension_multiplier,
                                        'Units' => 'LB'
                                    ],
                                    'Dimensions' => [
                                        'Length' => $shipping_dimensions["length"],
                                        'Width' => $shipping_dimensions["width"],
                                        'Height' => $shipping_dimensions["height"] * $dimension_multiplier,
                                        'Units' => 'IN'
                                    ]
                                ],
                                "order_id" => $order_id,
                                "shipping_price_basis" => 'LTL',
                                "qty" => $assigned_quantity
                            ];

                            if($carrier->id == 1) {
                                $data['shipping_price_basis'] = 'single';
                                $fedex_rates = $this->getFedExRates($data);
                            } elseif($carrier->id == 9) {
                                $data['shipping_price_basis'] = 'LTL';
                                $fedex_rates = $this->getFedExRates($data, true);
                            }

                            if(array_key_exists("message", $fedex_rates)){
                                $carrier_errors['errors'][] = [
                                    'vendor_name' => $vendor_details->vendor_name,
                                    'error_message' => $fedex_rates['message'],
                                    'carrier_name' => $carrier->name
                                ];
                                continue;
                            }

                            if($carrier->id == 1) {
                                $is_strap = "n";
                                $shipping_price[] = $fedex_rates;
    
                                $strap = false;
                                if($delivery_method == "DROPSHIP" && $vendor_details->vendor_main_id == 5 && $shipping_dimensions["weight"] <= 30){
                                    $strap = true;
                                } elseif($shipping_dimensions["weight"] <= 23 && $shipping_dimensions["length"] < 31.9) {
                                    $strap = true;
                                }
                                
                                if($strap == true && $shipping_dimensions["product_type"] == 1){
                                    $data["dimension"]["Weight"]["Value"] = floatval($shipping_dimensions["weight"]) * 2;
                                    $data["dimension"]["Dimensions"]["Height"] = $shipping_dimensions["height"] * 2;
                                    $data["shipping_price_basis"] = 'strap';
                                    $data["qty"] =  isset($request->qty) ? $request->qty : '';
                                    $fedex_rates_strap = $this->getFedExRates($data);
                                    if(!array_key_exists("message", $fedex_rates_strap)){
                                        $shipping_price[] = $fedex_rates_strap;
                                        $is_strap = "y";
                                    }
                                }
                            } elseif($carrier->id == 9) {
                                $is_strap = "n";
                                $fedex_rates["price"] = $fedex_rates["price"] / $assigned_quantity;
                                $shipping_price[] = $fedex_rates;
    
                                $strap = false;
                                $is_ltl = "y";
                            }
                        } elseif(!$request->has("shipping_method") && $carrier->id == 10 && $vendor_details->vendor_main_id == 5 && $delivery_method == "DROPSHIP" && !(in_array($order_data->orderDetail->channel,[101,901]) || strpos($order_data->orderDetail->channel, "329") !== false)) {
                            $cur_ship_price = 0;
                            if($shipping_dimensions["weight"] >= 0 && $shipping_dimensions["weight"] <= 30){
                                $cur_ship_price = 13;
                            } elseif($shipping_dimensions["weight"] >= 31 && $shipping_dimensions["weight"] <= 60) {
                                $cur_ship_price = 20.5;
                            } elseif($shipping_dimensions["weight"] >= 61 && $shipping_dimensions["weight"] <= 125) {
                                $cur_ship_price = 31;
                            } elseif($shipping_dimensions["weight"] > 125) {
                                $cur_ship_price = 42.5;
                            }
                            $is_strap = "n";
                            $shipping_price[] = ["shipping_price_basis" => "single", "price" => $cur_ship_price ];
                            if($delivery_method == "DROPSHIP" && $vendor_details->vendor_main_id == 5 && $shipping_dimensions["weight"] <= 30){
                                $shipping_price[] = ["shipping_price_basis" => "strap", "price" => $cur_ship_price];
                                $is_strap = "y";
                            }
                        } elseif(!$request->has("shipping_method") && $carrier->id == 12 && $vendor_details->vendor_main_id == 23 && !(in_array($order_data->orderDetail->channel,[101]) || strpos($order_data->orderDetail->channel, "329") !== false)) {
                            $check_flat_rate_table = DB::table('tirehub_flatrate_item_id')->where([['brand', '!=', $order_data->orderDetail->brand], ['part_number', '!=', $order_data->orderDetail->part_number]])->first();
                            if($check_flat_rate_table == null || $distance > 500) {
                                continue;
                            }
                            $cur_ship_price = 10;
                            $is_strap = "n";
                            $shipping_price[] = ["shipping_price_basis" => "single", "price" => $cur_ship_price ];
                        } elseif($carrier->id == 7) {
                            $shipping_price[] = ["shipping_price_basis" => "single", "price" => 0];
                            $is_strap = "n";
                        } elseif(!$request->has("shipping_method") && $carrier->company == 'KuehneNagel' && !empty($carrier->carrier_code)) {
                            $kuehne_nagel_carriers[$carrier->name . ' - ' . $carrier->carrier_code] = $carrier;
                            continue;
                        } else {
                            continue;
                        }
                        
                        $dimension = [
                            "carrier" => $carrier->name,
                            "length" => $shipping_dimensions["length"],
                            "width" => $shipping_dimensions["width"],
                            "height" => $shipping_dimensions["height"],
                            "weight" => $shipping_dimensions["weight"],
                            "price" => $shipping_price,
                            "is_strappable" => $is_strap,
                            "carrier_id" => $carrier->id,
                            "is_ltl" => $is_ltl,
                            "delivery_method" => array_intersect($carrier_delivery_method, $delivery_method_options)
                        ];
                        $carrier_dimensions[] = $dimension;
                    }
                }
            }
            if(!empty($kuehne_nagel_carriers)) {
            
                $kuehne_nagel_carrier_rates = $this->getKuehneNagelRates([
                    'weight' => $shipping_dimensions["weight"], 
                    'length' => $shipping_dimensions["length"], 
                    'width' => $shipping_dimensions["width"], 
                    'height' => $shipping_dimensions["height"],
                    "qty" => $assigned_quantity,
                    "order_id" => $order_id
                ], $kuehne_nagel_carriers);

                foreach($kuehne_nagel_carrier_rates as $rates) {
                    $carrier_data = $rates['carrier_data'];
                    unset($rates['carrier_data']);
                    $dimension = [
                        "carrier" => $carrier_data->name,
                        "length" => $shipping_dimensions["length"],
                        "width" => $shipping_dimensions["width"],
                        "height" => $shipping_dimensions["height"],
                        "weight" => $shipping_dimensions["weight"],
                        "price" => [$rates],
                        "is_strappable" => 'n',
                        "carrier_id" => $carrier_data->id,
                        "is_ltl" => 'y',
                        "delivery_method" => array_intersect(json_decode($carrier_data->delivery_method, true), $delivery_method_options)
                    ];
                    $carrier_dimensions[] = $dimension;
                }
            }
        } else {
            return false;
        }
        
        // if(in_array($vendor_details->vendor_main_id, [201, 56])) {
        //     dd($carrier_dimensions);
        // }
        if(!empty($carrier_dimensions)) {
            foreach($carrier_dimensions as $carrier_dimension => $item){
                $weight = 0;
                $sequence = 1;
                $ship_price = 0;
                
                if(is_array($item["price"]) && ($key = array_search('strap', array_column($item["price"], 'shipping_price_basis'))) !== false && $item["is_strappable"] == "y" && $is_vendor_strappable == 1){
                    for($i = 1;$i <= (($assigned_quantity - ($assigned_quantity % 2))/2);$i++){
                        if(array_key_exists("strap_price",$item) && $item["strap_price"] != null){
                            $cur_ship_price = $item["strap_price"];
                        } else {
                            if(is_array($item["price"]) && ($key = array_search('strap', array_column($item["price"], 'shipping_price_basis'))) !== false){
                                $cur_ship_price = $item["price"][$key]["price"];
                            } elseif(is_array($item["price"]) && ($key = array_search('single', array_column($item["price"], 'shipping_price_basis'))) !== false) {
                                $cur_ship_price = $item["price"][$key]["price"] * 2;
                            } else {
                                $cur_ship_price = $item["price"] * 2;
                            }
                        }
                        
                        if($channels->channel_code != null && (in_array($channels->channel_code,[101,901]) || strpos($channels->channel_code, "329") !== false)){
                            $cur_ship_price = 0;
                        }
                        
                        $shipping_dimensions[$carrier_dimension][] = [
                            "sequence"      => $sequence,
                            "is_strapped"   => "y",
                            "length"        => floatval($item["length"]),
                            "width"         => floatval($item["width"]),
                            "height"        => floatval($item["height"]) * 2,
                            "weight"        => floatval($item["weight"]) * 2
                        ];
                        
                        $ship_price += $cur_ship_price;
                        $sequence++;
                        $weight += (floatval($item["weight"]) * 2) / 2;
                    }
                    
                    for($i = 1; $i <= ($assigned_quantity % 2); $i++){
                        $cur_ship_price = is_array($item["price"])?$item["price"][0]["price"]:$item["price"];
                
                        if($channels->channel_code != null && (in_array($channels->channel_code,[101,901]) || strpos($channels->channel_code, "329") !== false)){
                            $cur_ship_price = 0;
                        }
                        
                        $shipping_dimensions[$carrier_dimension][] = [
                            "sequence"      => $sequence,
                            "is_strapped"   => "n",
                            "length"        => floatval($item["length"]),
                            "width"         => floatval($item["width"]),
                            "height"        => floatval($item["height"]),
                            "weight"        => floatval($item["weight"])
                        ];
                        
                        $ship_price += is_array($item["price"])?$item["price"][0]["price"]:$item["price"];
                        $sequence++;
                        $weight += floatval($item["weight"]);
                    }
                } else {
                    for($i = 1;$i <= $assigned_quantity;$i++){
                        $cur_ship_price = $item["price"];
                        if(is_array($item["price"]) && empty($item["price"])){
                            $cur_ship_price = 0;
                        } elseif(is_array($item["price"]) && ($key = array_search('single', array_column($item["price"], 'shipping_price_basis'))) !== false) {
                            $cur_ship_price = $item["price"][$key]["price"];
                        } elseif(is_array($item["price"]) && ($key = array_search('LTL', array_column($item["price"], 'shipping_price_basis'))) !== false) {
                            $cur_ship_price = $item["price"][$key]["price"];
                        } else {
                            $cur_ship_price = 0;
                        }

                        if($channels->channel_code != null && (in_array($channels->channel_code,[101,901]) || strpos($channels->channel_code, "329") !== false)){
                            $cur_ship_price = 0;
                        }
                        
                        $shipping_dimensions[$carrier_dimension][] = [
                            "sequence"      =>  $sequence,
                            "is_strapped"   =>  "n",
                            "length"        =>  floatval($item["length"]),
                            "width"         =>  floatval($item["width"]),
                            "height"        =>  floatval($item["height"]) * $dimension_multiplier,
                            "weight"        =>  floatval($item["weight"]) * $dimension_multiplier
                        ];
                        
                        $ship_price += $cur_ship_price;
                        $sequence++;
                        $weight += floatval($item["weight"]);
                    }
                }
                
                $data_to_push = [
                    "weight" => $weight,
                    "carrier" => $item["carrier"],
                    "carrier_id" => $item["carrier_id"],
                    "dimensions" => $shipping_dimensions[$carrier_dimension],
                    "shipping_cost_total" => $ship_price,
                    "shipping_cost_each" => $ship_price < 1 ? 0 : $ship_price / $assigned_quantity,
                    "delivery_options" => array_values($item["delivery_method"]),
                    "delivery_method" => array_values($item["delivery_method"])[0],
                    "distance" => $distance
                ];

                if($item["carrier"] == "FEDEX"){
                    $data_to_push["is_ltl"] = $shipping_dimensions[$carrier_dimension][0]["weight"] < 150?"n":"y";
                } else {
                    $data_to_push["is_ltl"] = "n";
                }
                $carrier_details[] = $data_to_push;
            }

            if(!empty($carrier_details)){
                $sort_carrier_price = [];
                foreach($carrier_details as $key => $value) {
                    $sort_carrier_price[$key] = floatval(str_replace(",","",$value['shipping_cost_total']));
                }
                array_multisort($sort_carrier_price, SORT_ASC, $carrier_details);
            }
        }
        
        return [
            'carrier_errors' => $carrier_errors,
            'carrier_details' => $carrier_details
        ];
    }

    public function getFedExRates($data, $ltl = false)
    {
        ini_set("default_socket_timeout", 5);
        
        if($ltl && $data['shipping_price_basis'] != "strap") {
            $no_of_tires_per_pallet = 65 / floor($data['dimension']['Dimensions']['Height']);
            
            if(floor($no_of_tires_per_pallet) > 0) {
                $no_pallet = ceil($data['qty'] / (int)$no_of_tires_per_pallet);
                $weight_dimension = ($data['dimension']['Weight']['Value'] * (int)$no_of_tires_per_pallet) + 25;
                $height_dimension =  $data['dimension']['Dimensions']['Height'] * (int)$no_of_tires_per_pallet;
            } else {
                $no_pallet = ceil($data['qty']);
                $weight_dimension = $data['dimension']['Weight']['Value'] + 25;
                $height_dimension =  $data['dimension']['Dimensions']['Height'];
            }

            return [
                "shipping_price_basis" => "LTL", 
                "price" => 10, 
                "no_pallet" => $no_pallet, 
                "dimensions" => [ 
                    'Length' => $data['dimension']['Dimensions']['Length'],
                    'Width' => $data['dimension']['Dimensions']['Width'],
                    'Height' => $height_dimension, 
                    "Weight" => $weight_dimension
                ]
            ];
            
            $client = new \SoapClient(base_path()."/FedEx/RateService_v28.2.wsdl", array('trace' => 1,'encoding'=>'ISO-8859-1','keep_alive' => false));
            $requests = [
                'WebAuthenticationDetail' => [
                    'UserCredential' => [
                        'Key' => '1tT2gA5mS6PVE7A7',
                        'Password' => 'Wkm9i3CdeZSyGpX8nLeImgy3z'
                    ],
                ],
                'ClientDetail' => [
                    'AccountNumber' => '251201463',
                    'MeterNumber' => '114403971'
                ],
                'TransactionDetail' => [
                    "CustomerTransactionId" => "Rate request for strapping guide"
                ],
                'Version' => [
                    'ServiceId' => 'crs', 
                    'Major' => '28', 
                    'Intermediate' => '0', 
                    'Minor' => '0'
                ],
                "ReturnTransitAndCommit" => true,
                "RequestedShipment" => [
                    "ServiceType" => "FEDEX_FREIGHT_ECONOMY",
                    // "Shipper" => [
                    //     "Address" => [
                    //         'StreetLines' => ['4490 Ayers Ave.'],
                    //         'City' => 'Vernon',
                    //         'StateOrProvinceCode' => 'CA',
                    //         'PostalCode' => '90058',
                    //         'CountryCode' => 'US'
                    //     ]
                    // ],
                    "Shipper" => [ //Origin
                        "Address" => $data['from']
                    ],
                    "Recipient" => [
                        "Address" => $data['to']
                    ],
                    "ShippingChargesPayment" => [
                        'PaymentType' => 'SENDER',
                        'Payor' => [
                            'ResponsibleParty' => [
                                'AccountNumber' => 998108926,
                                'Contact' => null,
                                'Address' => [
                                    'CountryCode' => 'US'
                                ]
                            ]
                        ]
                    ],
                    'RequestedPackageLineItems' => [
                        "SequenceNumber" => 1,
                        "Weight" => [
                            "Units" => "LB",
                            "Value" => $weight_dimension,
                        ],
                        'Dimensions' => [
                            'Length' => 40,
                            'Width' => 48,
                            'Height' => $height_dimension,
                            'Units' => 'IN'
                        ],
                        "PhysicalPackaging" => "PALLET",
                        "AssociatedFreightLineItems" => [
                            "Id" => 1
                        ]
                    ],
                    'FreightShipmentDetail' => [
                        // 'FedExFreightAccountNumber' => 715076098,
                        'FedExFreightBillingContactAndAddress' => [
                            'Address'=> [
                                'StreetLines' => ['4490 Ayers Ave.'],
                                'City' => 'Vernon',
                                'StateOrProvinceCode' => 'CA',
                                'PostalCode' => '90058',
                                'CountryCode' => 'US'
                            ]
                        ],
                        
                        'Role' => 'SHIPPER',
                        'LineItems' => [
                            'Id' => 1,
                            'FreightClass' => 'CLASS_070',
                            'Packaging' => 'PALLET',
                            'Weight' => [
                                'Value' => $weight_dimension,
                                'Units' => 'LB'
                            ],
                            
                            'Dimensions' => [
                                'Length' => 40,
                                'Width' => 48,
                                'Height' => $height_dimension,
                                'Units' => 'IN'
                            ]
                        ],
                        'AlternateBilling' => [
                            'AccountNumber' => 998108926,
                            'Contact' => [
                               'PersonName' => 'Online Tires',
                               'CompanyName' => '',
                               'PhoneNumber' => '877-465-8473'
                            ],
                            'Address'=> [
                                'StreetLines' => ['4490 Ayers Ave.'],
                                'City' => 'Vernon',
                                'StateOrProvinceCode' => 'CA',
                                'PostalCode' => '90058',
                                'CountryCode' => 'US'
                            ]
                        ]
                    ]
                ]
            ];

            $now = microtime(true);
            $response = $client->getRates($requests);
            try {
                // dd($requests["RequestedShipment"]["FreightShipmentDetail"]["LineItems"]["Weight"]);
                // dd($response);
                DB::table('APIresponse')->insert([
                    'orderID' => $data["order_id"],
                    'ApitypeID' => 26,
                    'Request' => json_encode($requests),
                    'Response' => json_encode($response),
                    'elapsed_time' => microtime(true) - $now
                ]);
                if(isset($response->HighestSeverity) && ( $response->HighestSeverity == "ERROR")){
                    //$response = json_decode($response);
                    if(is_array($response->Notifications)){
                        $error_msg = $response->Notifications[0]->Message;
    
                       // $error_msg .= ','.$response->Notifications[1]->Message;
                    }else{
                        $error_msg = $response->Notifications->Message;
    
                    }
                
    
                    $error_log = [
                        'order_id' => $data["order_id"],
                        "user_id" =>  auth()->user()->id,
                        "carrier" => "Fedex",
                        "api_error" => $error_msg
                    ];
    
                    DB::table("LogDetails")
                    ->insert(
                        ['order_list_id' => $data["order_id"], 'event_id' => 19, 'original_value' => 'carrier_api_error', 'new_value' => json_encode($error_log)]
                    );
                    
                }
                return [
                    "shipping_price_basis" => "LTL", 
                    "price" => $response->RateReplyDetails->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount, 
                    "no_pallet" => $no_pallet, 
                    "dimensions" => [ 
                        'Length' => $data['dimension']['Dimensions']['Length'],
                        'Width' => $data['dimension']['Dimensions']['Width'],
                        'Height' => $height_dimension, 
                        "Weight" => $weight_dimension
                    ]
                ];
            } catch (\Exception $e) {
                if(!is_array($response->Notifications)) {
                    $response->Notifications = [$response->Notifications];
                }

                $errors = [];
                foreach($response->Notifications as $notification) {
                    $errors[] = $notification->Message;
                }
                return ["message" => implode(' ', $errors), "response"=> $client->__getLastResponse(), "request"=> $client->__getLastRequest()];
            }
        } else {
            return ["shipping_price_basis" => $data['shipping_price_basis'], "price" => 10];
            $serviceType = 'GROUND_HOME_DELIVERY';
            
            $client = new \SoapClient(base_path()."/FedEx/RateService_v28.2.wsdl", array('trace' => 1,'encoding'=>'ISO-8859-1','keep_alive' => false));
            $requests = [
                'WebAuthenticationDetail' => [
                    'ParentCredential' => [
                        'Key' => '1tT2gA5mS6PVE7A7',
                        'Password' => 'Wkm9i3CdeZSyGpX8nLeImgy3z'
                    ],
                    'UserCredential' => [
                        'Key' => '1tT2gA5mS6PVE7A7',
                        'Password' => 'Wkm9i3CdeZSyGpX8nLeImgy3z'
                    ],
                ],
                'ClientDetail' => [
                    'AccountNumber' => '251201463',
                    'MeterNumber' => '114403971'
                ],
                'TransactionDetail' => [
                    "CustomerTransactionId" => "Rate request for strapping guide"
                ],
                'Version' => [
                    'ServiceId' => 'crs', 
                    'Major' => '28', 
                    'Intermediate' => '0', 
                    'Minor' => '0'
                ],
                "ReturnTransitAndCommit" => true,
                "RequestedShipment" => [
                    "DropoffType" => "REGULAR_PICKUP",
                    "ShipTimestamp" => date('c'),
                    "ServiceType" => $serviceType,
                    "PackagingType" => "YOUR_PACKAGING",
                    "Shipper" => [
                        "Contact" => [
                            'PersonName' => 'Online Tires',
                            'CompanyName' => '',
                            'PhoneNumber' => '877-465-8473'
                        ],
                        "Address" => [
                            'StreetLines' => ['4490 Ayers Ave.'],
                            'City' => 'Vernon',
                            'StateOrProvinceCode' => 'CA',
                            'PostalCode' => '90058',
                            'CountryCode' => 'US'
                        ]
                    ],
                    "Origin" => [
                        "Address" => $data['from']
                    ],
                    "Recipient" => [
                        "Contact" => [
                            'PersonName' => 'Sender Name',
                            'CompanyName' => '',
                            'PhoneNumber' => '877-465-8473'
                        ],
                        
                    ],
                    "ShippingChargesPayment" => [
                        'PaymentType' => 'SENDER',
                        'Payor' => [
                            'ResponsibleParty' => [
                                'AccountNumber' => 251201463,
                                'CountryCode' => 'US'
                            ]
                        ]
                    ],
                    "PackageCount" => 1,
                    
                ]
            ];
            $requests["RequestedShipment"]["Recipient"]["Address"] = $data['to'];
            $requests["RequestedShipment"]["RequestedPackageLineItems"] = $data['dimension'];
            
            $now = microtime(true);
            $response = $client->getRates($requests);
            try{
                
                DB::table('APIresponse')->insert([
                    'orderID' => $data["order_id"],
                    'ApitypeID' => 25,
                    'Request' => json_encode($requests),
                    'Response' => json_encode($response),
                    'elapsed_time' => microtime(true) - $now
                ]);
    
                if(isset($response->HighestSeverity) && ( $response->HighestSeverity == "ERROR")){
                    //$response = json_decode($response);
                    if(is_array($response->Notifications)){
                        $error_msg = $response->Notifications[0]->Message;
    
                       // $error_msg .= ','.$response->Notifications[1]->Message;
                    }else{
                        $error_msg = $response->Notifications->Message;
    
                    }
                
    
                    $error_log = [
                        'order_id' => $data["order_id"],
                        "user_id" =>  auth()->user()->id,
                        "carrier" => "Fedex",
                        "api_error" => $error_msg
                    ];
    
                    DB::table("LogDetails")
                    ->insert(
                        ['order_list_id' => $data["order_id"], 'event_id' => 19, 'original_value' => 'carrier_api_error', 'new_value' => json_encode($error_log)]
                    );
                    
                }
                return ["shipping_price_basis" => $data['shipping_price_basis'], "price" => $response->RateReplyDetails->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount];
            } catch (\Exception $e) {
                if(!is_array($response->Notifications)) {
                    $response->Notifications = [$response->Notifications];
                }
                
                $errors = [];
                foreach($response->Notifications as $notification) {
                    $errors[] = $notification->Message;
                }
                return ["message" => implode(' ', $errors), "response" => $client->__getLastResponse(), "request" => json_encode($requests)];
            }
        }
    }

    public function getProductDetails(Request $request){
        return Catalog::where([['mspn',$request->part_number],['brand',$request->brand]])->first();
    }

    public function getKuehneNagelRates($data, $enabled_carriers)
    {
        if(empty($enabled_carriers)) {
            return [];
        }
        $days = 0;
        if(date('N', strtotime(date('Y-m-d'))) >= 6) {
            $days = date('N', strtotime(date('Y-m-d'))) == 6 ? 2 : 1;
        }

        $no_of_tires_per_pallet = 65 / floor($data['height']);
        
        if(floor($no_of_tires_per_pallet) > 0) {
            $no_pallet = ceil($data['qty'] / intval($no_of_tires_per_pallet));
            $weight_dimension = ($data['weight'] * intval($no_of_tires_per_pallet)) + 25;
            $height_dimension =  $data['height'] * intval($no_of_tires_per_pallet);
            $price_multiplier = $data['qty'];
        } else {
            $no_pallet = $data['qty'];
            $weight_dimension = $data['weight'] + 25;
            $height_dimension =  $data['height'];
            $price_multiplier = 1;
        }

        $request = [
            'UserName' => 'ATVTireInc-ws',
            'ClientCode' => 'MA000',
            'AuthenticationID' => 'ad58125c-10f4-4bf7-9d48-04ed505fbba9',
            'OriginZip' => '90058',
            'DestinationZip' => '90058',
            'ShipmentDate' => date('Y-m-d\TH:i:s', strtotime(date('Y-m-d') . '00:00:00 + ' . $days . ' days')),
            'Items' => [
                [
                    'ItemClass' => '110',
                    'Weight' => $weight_dimension,
                    'Length' => $data['length'],
                    'Width' => $data['width'],
                    'Height' => $height_dimension,
                ]
            ]
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://lmirest.retransfreight.com/api/RateQuote/RateQuote',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($request),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        $info = curl_getinfo($curl);

        // if($data['qty'] > 1) {
        //     dd($data, $weight_dimension, $height_dimension, $no_of_tires_per_pallet, $request, json_decode($response));
        // }

        DB::table('APIresponse')->insert([
            'orderID' => $data["order_id"],
            'ApitypeID' => 46,
            'Request' => preg_replace("/\s+/i","", json_encode($request)),
            'Response' => $response,
            'elapsed_time' => $info['total_time']
        ]);

        $return_values = [];
        if(isset($info['http_code']) && $info['http_code'] == 200) {
            $response = json_decode($response);
            if(property_exists($response, 'Carriers')) {
                foreach($response->Carriers as $values) {
                    if(array_key_exists($values->Carrier, $enabled_carriers)) {
                        $return_values[] = [
                            "carrier_data" => $enabled_carriers[$values->Carrier],
                            "shipping_price_basis" => "LTL", 
                            "price" => $values->NetAmount / $price_multiplier, 
                            "no_pallet" => $no_pallet, 
                            "dimensions" => [ 
                                'Length' => $data['length'],
                                'Width' => $data['width'],
                                'Height' => $height_dimension, 
                                "Weight" => $weight_dimension
                            ]
                        ];
                    }
                }
            }
        }
        
        return $return_values;
    }

    public function orderStoreData(Request $request)
    {
        $item = $request->all();
        $id = $item["id"];
        $session = new Session();
        $session->set($id, $item["orderData"]);
        return $session->get($id);
    }

    public function getSizes()
    {
        $db_sizes = DB::table("strapping")->get();
        return response()->json($db_sizes);
    }

    public function getStores()
    {
        $db_sizes = DB::table("store_location")->get();
        return response()->json($db_sizes);
    }

}
