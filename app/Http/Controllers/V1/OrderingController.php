<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderingController extends Controller
{
    public function postQuote(Request $request)
    {
        // validate all the fields
        $request->validate([
            'fname' => 'required|alpha:ascii',
            'lname' => 'required|alpha:ascii',
            'address1' => 'required|string',
            'address2' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postalCode' => 'required|numeric',
            'country' => 'required|alpha:ascii',
            'brandName' => 'required|string',
            'partNumber' => 'required|string',
            'phoneNumber' => 'required|integer',
            'emailAddress' => 'required|email',
            'channel_order_no' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'store_location_id' => 'integer',
        ]);

        // get all the store location id with available product quantity
        $stores = DB::table('inventory_feed')
            ->where('qty', '>=', $request->quantity)
            ->where('part_number', $request->partNumber)
            ->where('brand', $request->brandName)
            ->where('is_active', 1)
            ->when($request->has('store_location_id'), function ($query) use ($request) {
                $query->where('store_location_id', $request->store_location_id);
            })
            ->pluck('store_location_id')
            ->toArray();

        // check if there is no data then get all product where quantity is lower than the request
        if (empty($stores)) {
            $lowerQuantities = DB::table('inventory_feed')
                ->where('qty', '<', $request->quantity)
                ->where('part_number', $request->partNumber)
                ->where('brand', $request->brandName)
                ->where('is_active', 1)
                ->when($request->has('store_location_id'), function ($query) use ($request) {
                    $query->where('store_location_id', $request->store_location_id);
                })
                ->pluck('qty')
                ->toArray();
            //  if there is any product with lower quantities then display the maximun quantity available
            if (!empty($lowerQuantities)) {
                $availableQuantity = [max($lowerQuantities)];
                $quantity = implode(', ', $availableQuantity);

                $response = [
                    'success' => false,
                    'message' => 'The requested quantity of ' . $request->quantity . ' is currently unavailable, as there is only a stock of ' . $quantity . ' available.',
                ];
            }
            else {
                $response = [
                    'success' => false,
                    'message' => 'No matching product found',
                ];
            }
        } else {
            $response = [
                'success' => true,
                'store_location_ids' => $stores,
            ];
        }
        return response()->json($response);
    }


    public function execute(Request $request)
    {
        $validation = $this->validation($request);
        if(!(isset($validation['status']) && strtolower($validation['status']) == 'success')) {
            return response()->json($validation, 422);
        }
        
        $request->brand = strtoupper($request->get('brand'));
        $request->part_number = strtoupper($request->get('part_number'));
        $channel = $request->get('channel');

        $channels = DB::table('channels')
                ->where('channel_code', $request->get('channel'))
                ->first();
        
        $batch = $request->get('batch');
        
        $check_zip = $this->getCity($request);
        if(is_array($check_zip) && array_key_exists("message", $check_zip)){
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
        if(!is_array($response) && $response !== true) {
            return response()->json([
                'message' => $response,
                'error_id' => 10
            ], 422);
        }
        
        $un_touched_inventory = $response;
        $available_vendor = [];
        $no_stock_vendor = [];
        
        foreach($response['inventory'] as $r => $s){
            if(!is_array($s)){
                unset($response['inventory'][$r]);
                continue;
            }

            if((isset($s["message"]) && strtolower($s["message"]) == 'out of stock') || (isset($s["status"]) && strtolower($s["status"]) == 'out of stock') ){
                $store_id = isset($s["store_location"]) ? $s["store_location"] : '';
                $no_stock_vendor[] = ['vendor_main_id' => $s['vendor_id'], 'qty' => 0, 'store_id' => $store_id];
            }
            

            if(strtolower($s["status"]) != "success"){
                unset($response['inventory'][$r]);
                continue;
            }

            if(!array_key_exists("vendor_id", $s)){
                unset($response['inventory'][$r]);
                continue;
            }
            
            if(floatval($s["quantity"]) > 0){
                if($request->has('except_stores') && $request->except_stores['vendor_main_id'] == $s['vendor_id'] && $request->except_stores['store_location_id'] == $s['store_location']){
                    continue;
                }
                $available_vendor[] = $s["vendor_id"];
            } else {
                $no_stock_vendor[] = ['vendor_main_id' => $s['vendor_id'], 'qty' => $s["quantity"], 'store_id' => $s["store_location"]];
                unset($response['inventory'][$r]);
            }
        }
        
        $searchResult = $this->search($request->part_number, $request->brand);
        if(array_key_exists("message", $searchResult) && !$request->has("dimension")) {
            $return_inventory = $response;
            foreach($return_inventory as $key => &$inventory) {
                $inventory_city = "";
                $inventory_vendor_name = $inventory["vendor_id"];
                $db_store_location = DB::table("store_location")->where("id",$inventory["store_location"])->first();
                $db_vendor = DB::table("vendor_main")->where("id",$inventory["vendor_id"])->first();
                if($db_store_location != null && $db_vendor != null) {
                    $inventory_city = $db_store_location->city.", ".$db_store_location->state;
                    $inventory_vendor_name = $db_vendor->name;
                }
                $inventory["city"] = $inventory_city;
                $inventory["vendor_name"] = $inventory_vendor_name;
            }
            return response()->json(array_merge($searchResult, ["inventory" => $return_inventory]), 422);
        }
        
        $order_id = "";
        if(array_key_exists("order_id",$un_touched_inventory)) $order_id = $un_touched_inventory["order_id"];
        
        $product_type = 1;
        if(!$request->has("dimension")) {
            if(!array_key_exists("message", $searchResult)){
                $searchResult = $searchResult[0];
                $product_type = $searchResult['category'];
                if($searchResult['category'] == 1) {
                    $filtered_full_size = preg_replace("/[^0-9]+/","",$searchResult['unformatted_size']);
                    if(($strapping = Strapping::where("search_size",$filtered_full_size)->first()) == null){
                        $shipping_dimensions = $this->getItemDimension($searchResult['section_width'], $searchResult['aspect_ratio'], $searchResult['rim_diameter']);
                        $shipping_dimensions["order_id"] = $order_id;
                        return response()->json($shipping_dimensions, 422);
                    }
                }
            }
        } elseif($request->has("product_type")) {
            $product_type = $request->product_type;
        }
        
        $this->saveMissingData($request, $order_id);
        
        if(!$request->has("dimension")) {
            if($product_type == 1) {
                $shipping_dimensions = [];
                $filtered_full_size = preg_replace("/[^0-9]+/","",$searchResult['unformatted_size']);
                $strapping = Strapping::where("search_size", $filtered_full_size)->first();
                if($strapping == null){
                    $shipping_dimensions = $this->getItemDimension($searchResult['section_width'], $searchResult['aspect_ratio'], $searchResult['rim_diameter']);
                    unset($shipping_dimensions["message"]);
                    unset($shipping_dimensions["error_id"]);
                } else {
                    $shipping_dimensions = [
                        "fullsize" => $searchResult['section_width']."/".$searchResult['aspect_ratio']."R".$searchResult['rim_diameter'],
                        "height" => $strapping->height,
                        "length" => $strapping->length,
                        "width" => $strapping->width,
                        "ship_price" => $strapping->final_weight * .71,
                        "weight" => $strapping->final_weight,
                        "product_type" => $searchResult['category'],
                        "model" => $searchResult['model'],
                        "description" => $searchResult['description']
                    ];

                    if(floatval($searchResult['weight_tire']) > 0) {
                        $shipping_dimensions["weight"] = floatval($searchResult['weight_tire']);
                    }
                }
            } else if($product_type == 2) {
                $strapping = Strapping::where("search_size",preg_replace("/[^0-9]+/","",$searchResult['wheel_diameter'].$searchResult['wheel_width']))->first();
                if($strapping == null){
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
                        "fullsize" => $searchResult['wheel_diameter']."X".$searchResult['wheel_width'],
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
            } else if($product_type == 3) {
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

            if(!array_key_exists("message", $searchResult)){
                $shipping_dimensions["product_type"] = $searchResult[0]->category;
                $shipping_dimensions["model"] = $searchResult[0]->model;
                $shipping_dimensions["description"] = $searchResult[0]->description;
            }
        }
        
        if(count($available_vendor) == 0) {
            return response()->json(["message"=>"No stock.","error_id"=>"3","order_id"=>$order_id,"shipping_dimensions"=>$shipping_dimensions,"inventory"=>$un_touched_inventory,"line"=>__LINE__], 422);
        }
        
        $partNumber = $request->part_number;
        $brand = $request->brand;
        
        if($request->has("netnet")){
            $netnet = $request->netnet;
        } else {
            $netnet = Netnet::where([['netnet_price.brand',$brand],['netnet_price.mspn', $partNumber]])
                ->select(['netnet_price.brand', 'netnet_price.mspn', 'netnet_price.netnet', 'netnet_price.invoice', 'vendor_main.id'])
                ->leftJoin("vendor_main",'vendor_main.id', '=', 'netnet_price.vendor')
                ->whereIn('netnet_price.vendor', array_unique($available_vendor))
                ->groupBy('netnet_price.vendor')
                ->orderBy('netnet_price.netnet', 'ASC')
                ->get()
                ->toArray();
            
            $vendor_with_price = [];
            foreach($netnet as $k => $v){
                if(in_array($v["id"],$available_vendor)){
                    $vendor_with_price[] = $v["id"];
                }
            }
            foreach($response['inventory'] as $i => $j){
                if(!in_array($j["vendor_id"], $vendor_with_price) && array_key_exists("price",$j) && floatval($j) > 0){
                    if(!in_array($j["vendor_id"],array_column($netnet,"id"))){
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
            $delivery_methods = ["DROPSHIP","DELIVERY","WILL CALL"];
        // }

        $netnetPrice = [];
        $customerZip = $request->zip;
        $repeat_vendor_array = [];
        $carrier_errors = [];
        foreach($netnet as $k => $v){
            foreach($response['inventory'] as $r => $s){
                if($v["id"] == $s["vendor_id"]){
                    foreach($delivery_methods as $d => $m){
                        if($request->has("shipping_method") && $m == "DROPSHIP"){
                            continue;
                        }
                        
                        if(in_array($m, ["DELIVERY", "WILL CALL"]) && $s["vendor_id"] == 5 && in_array($s["store_location"], [244,245,248])){
                            continue;
                        }
                        
                        if(in_array($m, ["DELIVERY", "WILL CALL"]) && $s["vendor_id"] == 76 && $s["store_location"] != 580) {
                            continue;
                        }
                        
                        if($m == "WILL CALL" && $v["id"] == 2 && !in_array($s["branch"],["05","07"])){
                            continue;
                        }
                        
                        if($m == "DELIVERY" && $v["id"] == 2 && !in_array($s["branch"],["07","06"])){
                            continue;
                        }
                        
                        if(in_array($m, ["DELIVERY", "WILL CALL"]) && $v["id"] == 23 && !in_array($s["store_location"], ["134", "135", "136", "137", "138", "139", "140", "141"])){
                            continue;
                        }
                        
                        $vendor_statement = [
                            ["vendor_main_id", $v["id"]]
                        ];
                        if(array_key_exists("apitype", $s)){
                            $vendor_statement[] = ["api_type", $s["apitype"]];
                        }

                        $vendor_details = DB::table("vendor_main_details")
                            ->where($vendor_statement)
                            ->first();
                        
                        if($vendor_details == null)
                            continue;
                        
                        $vendor_delivery_method = json_decode($vendor_details->delivery_method, true);
                        if(!is_array($vendor_delivery_method) || !in_array($m, $vendor_delivery_method)){
                            continue;
                        }
                        
                        $is_channel_ok = false;
                        if($batch != null && $m == "WILL CALL" && !(count($vendor_delivery_method) == 1 && $vendor_delivery_method[0] == "WILL CALL")){
                            continue;
                        }

                        $db_delivery_methods = json_decode($channels->delivery_method,true);
                        if($product_type == 2 and $request->get('channel') == '101') {
                            $db_delivery_methods[] = 'DROPSHIP';
                            $db_delivery_methods = array_unique($db_delivery_methods);
                        }
                        
                        $assigned_vendors = json_decode($channels->assigned_vendor,true);
                        if(is_array($assigned_vendors)){
                            if($m != "DROPSHIP" && array_key_exists($s["vendor_id"], $assigned_vendors)){
                                if(empty($assigned_vendors[$s["vendor_id"]])){
                                    $is_channel_ok = true;
                                } else {
                                    if(in_array($s["store_location"],$assigned_vendors[$s["vendor_id"]])){
                                        $is_channel_ok = true;
                                    } else {
                                        continue;
                                    }
                                }
                            } elseif($m == "DROPSHIP") {
                                $is_channel_ok = true;
                            } else {
                                continue;
                            }
                        } else {
                            $is_channel_ok = true;
                        }
                        
                        if($is_channel_ok) {
                            if(!in_array($m, $db_delivery_methods)){
                                continue;
                            } else /*if($batch !== null) {
                                $delivery_method = [$m];
                            } else*/ if(in_array($m,["DELIVERY","WILL CALL"])) {
                                $delivery_method = array_intersect($vendor_delivery_method, array_diff($db_delivery_methods, ["DROPSHIP"]));
                            } elseif($m == "DROPSHIP") {
                                $delivery_method = ["DROPSHIP"];
                            }
                        }

                        if($m == "DELIVERY" && $v["id"] == 2 && $s["branch"] == "06"){	
                            $delivery_method = ["DELIVERY"];	
                        }
                        
                        if($m == "WILL CALL" && $v["id"] == 2 && $s["branch"] == "05"){
                            $delivery_method = ["WILL CALL"];
                        }
                        $store_location = StoreLocation::where("id", $s["store_location"])->first();
                        if($store_location == null){
                            continue;
                        }
                        
                        $vendor = VendorMain::where("id", $v["id"])->first();
                        $store_id = $s["store_location"];
                        
                        if($m == "WILL CALL" && in_array("DELIVERY".$s["vendor_id"],$repeat_vendor_array)){
                            continue;
                        } elseif($m == "DELIVERY" && in_array("WILL CALL".$s["vendor_id"],$repeat_vendor_array)){
                            continue;
                        } else {
                            $repeat_vendor_array[] = $m.$s["vendor_id"];
                        }
                        
                        if($m == "DELIVERY" && $request->has("shipping_method")){
                            if($store_location->state != "CA"){
                                continue;
                            }
                        }
                        
                        if(in_array($m,["WILL CALL","DELIVERY"]) && $store_location->state != "CA"){
                            continue;
                        }

                        if($m == "DELIVERY" || $m == "WILL CALL") {
                            $store_id = "288";
                        }
                        
                        $store_location = StoreLocation::where("id", $store_id)->first();
                        $distance = $this->getDistance($customerZip, ["lat" => $store_location->lat, "lon" => $store_location->lon], $check_zip->city);
                        
                        $source_dc = StoreLocation::where("id", $s["store_location"])->first();
                        
                        if(in_array($m, ["DELIVERY", "WILL CALL"])){
                            $source_distance = $this->getDistance($store_location->zip_code, ["lat" => $source_dc->lat, "lon" => $source_dc->lon], $store_location->city);
                        } else {
                            $source_distance = $distance;
                        }
                        
                        $speed_rating = "";
                        $full_size = "";
                        $model = "";
                        $description = "";
                        $searchResult = $this->search($request->part_number, $request->brand);
                        if($product_type == 1){
                            if(!array_key_exists("message", $searchResult)){
                                $searchResult = $searchResult[0];
                                $speed_rating = $searchResult['speed_rating'];
                                $full_size = $searchResult['section_width']."/".$searchResult['aspect_ratio']."R".$searchResult['rim_diameter'];
                                $model = $searchResult['model'];
                                $description = $searchResult['description'];
                            } 
                        } elseif($product_type == 2) {
                            if(!array_key_exists("message", $searchResult)){
                                $searchResult = $searchResult[0];
                                $speed_rating = "";
                                $full_size = intval($searchResult['wheel_diameter'])."X".intval($searchResult['wheel_width']);
                                $model = $searchResult['model'];
                                $description = $searchResult['description'];
                            }
                        }

                        $vendor_details->vendor_name = $vendor->name . ' - ' . $source_dc->city .", ".$source_dc->state;

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
                        
                        if(!empty($carrier_details['carrier_errors']['errors'])){
                            $carrier_errors[] = $carrier_details['carrier_errors']['errors'];
                        }
                        
                        if(empty($carrier_details['carrier_details'])){
                            continue;
                        }

                        $carrier_details = $carrier_details['carrier_details'];
                        
                        //transfer
                        $transfer_vendor = null;
                        $transfer = TransferMapping::where([["brand", $request->brand],["vendor_main_id",$v["id"]],["is_active",1]])->first();
                        
                        $is_transfer = "n";
                        $price_each = floatval($v["netnet"]);
                        $invoice = floatval($v["invoice"]);
                        if($transfer != null){
                            if($transfer->is_active == 1){
                                $transferNetnet = Netnet::where([['brand', $request->brand],['mspn', $request->part_number],["vendor",$transfer->price_vendor_main_id]])->first();
                                if($transferNetnet != null){
                                    if(is_array($speed_ratings = json_decode($transfer->speed_rating))){
                                        if(in_array($speed_rating,$speed_ratings)){
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
                        if(in_array($m,['WILL CALL','DELIVERY']) && $v['id'] == 5 && in_array($s["store_location"],[165,173,189]) && (in_array($channel,[901]) || strpos($channel, "329") !== false)) {
                            $price_with_discount = $price_each - 3.5;
                        }
                        
                        $store_location_no = "";
                        
                        if(array_key_exists('branch', $s)) {
                            $store_location_no = $s["branch"];
                        }
                        
                        if($v["id"] == 5) {
                            $store_location_no = 1927109;
                        }
                        
                        // if($v["id"] == 2 || $v["id"] == 14 || $v["id"] == 32 || $v["id"] == 177) {
                            
                        // }
                        
                        if($v["id"] == 5 && $m == "DROPSHIP"){
                            $store_location_no = 1304048;
                        }
                        
                        $is_local_plus = "n";
                        if(array_key_exists("quantity_breakdown",$s)){
                            if($s["quantity_breakdown"]["local"] < 1){
                                $is_local_plus = "y";
                            }
                        }
                        
                        $carrier_from_csv = 0;
                        if($channel != null && (in_array($channel,[101,"329ST"])) && $request->has("carrier_found") && $request->carrier_found == "1"){
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
                            "id" => count($netnetPrice)+1,
                            "is_active" => 1,
                            "uniq_id" => bin2hex(substr($v["id"].$source_dc->id.$carrier_details[0]["delivery_method"],0,10)),
                            "vendor_details" => [
                                "vendor_id" => $v["id"],
                                "vendor_name" => $vendor->name,
                                "vendor_distance" => $source_distance,
                                "store_city" => $source_dc->city.", ".$source_dc->state,
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
        
        if($request->has("not_api") || in_array($type = $request->route('type'),["all"])){
            if(empty($netnetPrice)){
                if($product_type == 2 && !$request->has("dimension") && false){
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
                if(!isset($shipping_dimensions)){
                    $shipping_dimensions = [
                        "weight" => 0,
                        "length" => 0,
                        "width" => 0,
                        "height" => 0,
                    ];
                }
                return response()->json(["message"=>"No stock.","error_id"=>"3","order_id"=>$order_id,"shipping_dimensions"=>$shipping_dimensions,"line"=>__LINE__,"inventory"=>$un_touched_inventory], 422);
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

                foreach($netnetPrice as $vendors_found){
                    $vendor_id_log = $vendors_found['vendor_details']['vendor_id'];
                    $store_location_id_log = $vendors_found["vendor_details"]["store_location_id"];
                    $carrier_id_log = $vendors_found["shipping_details"]['selected_carrier']["carrier"];
                    
                    $price_each = isset($vendors_found['price_details']['cost_each']) ?$vendors_found['price_details']['cost_each'] : ''; // eunise
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
                
                if(count($no_stock_vendor) > 0){
                    $new_value['other_vendor'] = $no_stock_vendor;
                }
                
                if($request->has("dimension")){
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
                if($request->has("selected_vendors")){
                    $repeat++;
                    foreach($request->selected_vendors as $selected_vendor){
                        if(array_key_exists("is_bypassed",$selected_vendor)){
                            $selected_vendors[] = $selected_vendor;
                            $quantity_allocation -= $selected_vendor["get_qty"];
                        }
                    }
                }
                
                for($i = 0; $i < $repeat; $i++) {
                    foreach ($netnetPrice as &$value) {
                        if($repeat == 2 && $i == 0){
                            $found_keys = array_keys(array_column($request->selected_vendors,"vendor_id"), $value["vendor_details"]["vendor_id"]);
                            if(count($found_keys) > 0){
                                $vendor_found = false;
                                foreach($found_keys as $fkeys){
                                    $hex = bin2hex(substr($value["vendor_details"]["vendor_id"].$value["vendor_details"]["store_location_id"].$value["shipping_details"]["selected_carrier"]["delivery_method"],0,10));
                                    if($request->selected_vendors[$fkeys]["store_id"] == $value["vendor_details"]["store_location_id"] && $hex == $request->selected_vendors[$fkeys]["uniq_id"]){
                                        $vendor_found = true;
                                        $assigned_quantity = $request->selected_vendors[$fkeys]["get_qty"] <= $value["vendor_details"]["inv"] ? $request->selected_vendors[$fkeys]["get_qty"] : $value["vendor_details"]["inv"];
                                        $quantity_used_up = false;
                                        $selected_vendors_hex[] = $hex;
                                        break;
                                    }
                                }
                                if(!$vendor_found){
                                    continue;
                                }
                            } else {
                                continue;
                            }
                        } else {
                            if(in_array(bin2hex(substr($value["vendor_details"]["vendor_id"].$value["vendor_details"]["store_location_id"].$value["shipping_details"]["selected_carrier"]["delivery_method"],0,10)),$selected_vendors_hex)){
                                continue;
                            }
                            if(in_array($value["vendor_details"]["vendor_id"].$value["vendor_details"]["store_location_id"], $unique_vendors)){
                                continue;
                            }
                            $unique_vendors[] = $value["vendor_details"]["vendor_id"].$value["vendor_details"]["store_location_id"];
                            $assigned_quantity = $quantity_allocation <= $value["vendor_details"]["inv"] ? $quantity_allocation : $value["vendor_details"]["inv"];
                            // $quantity_allocation -= $assigned_quantity;
                            $quantity_used_up = false;
                        }
                        if($assigned_quantity < 1){
                            $quantity_used_up = true;
                        }
                        if(!$quantity_used_up){
                            $delivery_method_options = $value["shipping_details"]["selected_carrier"]["delivery_method"];
                            if($delivery_method_options == "DELIVERY"){
                                $store_id = "288";
                            } else {
                                $store_id = $value["vendor_details"]["store_location_id"];
                            }
                            $vendor_statement = [["vendor_main_id",$value["vendor_details"]["vendor_id"]]];
                            if($value["vendor_details"]["vendor_id"] == 5){
                                if($delivery_method_options == "DELIVERY"){
                                    $vendor_statement[] = ["api_type",2];
                                } else {
                                    $vendor_statement[] = ["api_type",3];
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
                            
                            if(empty($carrier_details)){
                                continue;
                            }

                            $carrier_details = $carrier_details['carrier_details'];

                            $carrier_from_csv = 0;
                            if($channel != null && (in_array($channel,[101,"329ST"])) && $request->has("carrier_found") && $request->carrier_found == "1"){
                                $carrier_from_csv = 1;
                                $carrier_details = [$carrier_details[0]];
                            }

                            $selected_vendors[] = [
                                "vendor_id" => $value["vendor_details"]["vendor_id"],
                                "store_id" => $value["vendor_details"]["store_location_id"],
                                "get_qty" => intval($assigned_quantity),
                                "id" => $value["id"],
                                "uniq_id" => bin2hex(substr($value["vendor_details"]["vendor_id"].$value["vendor_details"]["store_location_id"].$value["shipping_details"]["selected_carrier"]["delivery_method"],0,10)),
                            ];
                            
                            $value["get_qty"] = intval($assigned_quantity);
                            $value["shipping_details"]["carrier_options"] = $carrier_details;
                            $value["shipping_details"]["selected_carrier"] = $carrier_details[0];
                            $value["price_details"]["total_cost"] = round($value["price_details"]["cost_each"] * $assigned_quantity,2);
                            $value["price_details"]["final_cost"] = round($carrier_details[0]["shipping_cost_total"] + ($value["price_details"]["cost_each"] * $assigned_quantity),2);
                            $value["price_details"]["qty"] = intval($assigned_quantity);
                            $value["price_details"]["price_with_discount"] = round($carrier_details[0]["shipping_cost_total"] + ($value["price_details"]["price_with_discount_each"] * $assigned_quantity),2);
                            $quantity_allocation -= $assigned_quantity;
                            if(array_key_exists("shipping_cost_total",$total)){
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

                if(!empty($request->get('promo'))) {
                    $promo_table = DB::table("promotions")->where("promo_code", $request->get('promo'))->first();
                    if($promo_table != null){
                        if($promo_table->promo_type_id == 1){
                            $total['discounted_selling_price'] = $request->get('selling_price') - ($request->get('selling_price') * ($promo_table->discount / 100));
                        } else if($promo_table->promo_type_id == 2) {
                            $total['discounted_selling_price'] = $request->get('selling_price') - $promo_table->discount;
                        } else if($promo_table->promo_type_id == 3) {
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
}
