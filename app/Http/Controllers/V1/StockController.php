<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OrderList;
class StockController extends Controller
{
    protected $inventory = [];
    protected $user = null;
    protected $order_id = null;

    protected $mfi_username = '2010036';
    protected $mfi_password = 'DEV2010036tq40GzZpTZ0pEDYzq5l7fyID5a8Hq745rM3wXuTT0df5M9BZlDfslSc';
    protected $mfi_url = 'http://test.maxfinkelstein.com:575/Service.asmx?WSDL';
    

    public function stockCheck($parameters)
    {
   
        $create_or_update_orderlist = $this->createOrUpdateOrderList($parameters);

        if ($create_or_update_orderlist != true) {
            return $create_or_update_orderlist;
        }

        $order_id = $this->order_id;

        $return = [];

        $non_api_parameters = [
            'latitude' => $parameters->lat,
            'longitude' => $parameters->lon,
            'brand' => $parameters->brand,
            'part_number' => $parameters->part_number,
        ];

        $parameters->quantity = 1;

        

        $this->getNonApiInventory($non_api_parameters);
        // $this->getWTDStock($parameters);
        // $this->getTWIStock($parameters);
        // $this->getIEStock($parameters);
        // $this->getUSAFStock($parameters);
        // $this->getSimpleTireStock($parameters);
        // $this->getATDStock($parameters);
        // $this->getNTWStock($parameters);
        // $this->getSTLWStock($parameters);
        // $this->getTirehubStock($parameters);
        // $this->getMFIStock($parameters);


        // $return = array_merge($return, $this->inventory);
        // // Non-api vendors
        $return = [
            'inventory' => $this->inventory
        ];

        $return["order_id"] = $order_id;
        return $return;
    }

    public function createOrUpdateOrderList($order_parameters)
    {
        
        $order_list = DB::table('orderList')
            ->join('orderDetails', 'orderList.id', '=', 'orderDetails.order_list_id')
            ->where([['orderDetails.channel_order', $order_parameters->channel], ['orderList.order_status_id', '!=', 7]])
            ->orderBy('orderList.id', 'asc')
            ->first();

        $system_po_number = null;
        if ($order_list) {
            $system_po_number = $order_list->system_po_number;
        }

        if($order_parameters->order_id != null) {
            $order = OrderList::find($order_parameters->order_id);
            if($order->order_status_id != 7) {
                return 'existing order cannot be updated';
            }
            $order->order_status_id = 7;
            $order->user_id = $order_parameters->user;
            $order->system_po_number = $system_po_number != null ? $system_po_number : $order_parameters->channel . '-' . $order->id;
            
            $order->orderDetail->first_name = $order_parameters->first_name;
            $order->orderDetail->last_name = $order_parameters->last_name;
            $order->orderDetail->city = $order_parameters->city;
            $order->orderDetail->state = $order_parameters->state;
            $order->orderDetail->postal_code = $order_parameters->postal_code;
            
            $order->orderDetail->quantity = $order_parameters->quantity;
            $order->orderDetail->brand = $order_parameters->brand;
            $order->orderDetail->part_number = $order_parameters->part_number;
            $order->orderDetail->selling_price = $order_parameters->selling_price;

            $order->orderDetail->channel = $order_parameters->channel;
            $order->orderDetail->channel_order = $order_parameters->channel_order;

            $order->orderDetail->batch = !empty($order_parameters->batch) ? $order_parameters->batch : 0;
            $order->orderDetail->single_bulk = $order_parameters->single_bulk;

            // $order->orderDetail->installer_id = $order_parameters->installer_id;
            $order->orderDetail->installer_id = 60081;

            $json_attributes = json_decode($order->orderDetail->json_attributes, true);
            if(empty($json_attributes)) {
                $json_attributes = [];
            }
            if(!empty($order_parameters->additional_details)) {
                $json_attributes = array_merge($json_attributes, $order_parameters->additional_details);
            }

            if(!empty($json_attributes)) {
                $order->orderDetail->json_attributes = json_encode($json_attributes);
            }
            $order->push();

        } else {
            $order = new OrderList;
            $order->order_status_id = 7;
            $order->user_id = $order_parameters->user;
            if($system_po_number != null) {
                $order->system_po_number = $system_po_number;
                $order->save();
            } else {
                $order->save();
                $order->system_po_number = $order_parameters->channel . '-' . $order->id;
                $order->save();
            }
            
            $orderDetail = new OrderDetail;
            $orderDetail->order_list_id = $order->id;
            $orderDetail->first_name = $order_parameters->first_name;
            $orderDetail->last_name = $order_parameters->last_name;
            $orderDetail->city = $order_parameters->city;
            $orderDetail->state = $order_parameters->state;
            $orderDetail->postal_code = $order_parameters->postal_code;
            $orderDetail->installer_id = 284495;
            
            $orderDetail->quantity = $order_parameters->quantity;
            $orderDetail->brand = $order_parameters->brand;
            $orderDetail->part_number = $order_parameters->part_number;
            $orderDetail->selling_price = $order_parameters->selling_price;

            $orderDetail->channel = $order_parameters->channel;
            $orderDetail->channel_order = $order_parameters->channel_order;
            $orderDetail->json_attributes = json_encode($order_parameters->additional_details);

            $orderDetail->batch = !empty($order_parameters->batch) ? $order_parameters->batch : 0;
            $orderDetail->single_bulk = $order_parameters->single_bulk;
            $orderDetail->save();
            
            //eunise add log bulk or single
            DB::table("LogDetails")
                ->insert([
                    'order_list_id' => $order->id, 
                    'event_id' => 14, 
                    'original_value' => '', 
                    'new_value' => json_encode([
                        "order_id" => $order->id,
                        "user_id" => $order_parameters->user,
                        "added_as" =>  !empty($order_parameters->batch) ? strval($order_parameters->batch) : '0', 
                        'added_at' => date("Y-m-d H:m:i")
                        ])
                    ]);
        }

        $this->order_id = $order->id;
        $this->user = $order_parameters->user;

        return 'success';
    }

    public function getNonApiInventory($arguments)
    {
        $non_api_vendor = DB::table("inventory_feed")
            ->select(
                'inventory_feed.*', 
                'vendor_main.is_active as vendor_status', 
                DB::raw("ROUND(69*DEGREES(ACOS(COS(RADIANS(".$arguments['latitude'].")) * COS(RADIANS(store_location.lat)) * COS(RADIANS(".$arguments['longitude'].") - RADIANS(store_location.lon)) + SIN(RADIANS(".$arguments['latitude'].")) * SIN(RADIANS(store_location.lat)))),2) AS distance_in_miles")
            )
            ->join('vendor_main', 'vendor_main.id','=','inventory_feed.vendor_main_id')
            ->join("store_location", function($join) {
                $join->on('store_location.vendor_main_id', '=', 'inventory_feed.vendor_main_id');
                $join->on('store_location.id', '=', 'inventory_feed.store_location_id');
            })
            ->where([
                ["store_location.is_active", 1], 
                ["inventory_feed.is_active", 1], 
                ["inventory_feed.part_number", $arguments['part_number']], 
                ["inventory_feed.brand", $arguments['brand']]
            ])
            ->orderByRaw('FIELD(inventory_feed.vendor_main_id,6) desc,`distance_in_miles` ASC')
            ->whereNotIn("inventory_feed.vendor_main_id", [5, 2, 14, 29, 13, 23, 32])
            ->take(40)
            ->get();
        
        foreach($non_api_vendor as $vendors){
            if($vendors->qty > 0 && $vendors->vendor_status == 1){
                if($vendors->vendor_main_id == 13){
                    // if($atv_stores > 21){
                        continue;
                    // } else {
                    //     $atv_stores++;
                    // }
                }
                $this->inventory[] = [
                    'status' => 'success',
                    'vendor_id' => $vendors->vendor_main_id,
                    'store_location' => $vendors->store_location_id,
                    'quantity' => intval($vendors->qty)
                ];
            } elseif($vendors->vendor_status != 1) {
                $this->inventory[] = [
                    'status' => 'failed',
                    'vendor_id' => $vendors->vendor_main_id,
                    'store_location' => $vendors->store_location_id,
                    'message' => 'Vendor is disabled',
                ];
            } else {
                $this->inventory[] = [
                    'status' => 'failed',
                    'vendor_id' => $vendors->vendor_main_id,
                    'store_location' => $vendors->store_location_id,
                    'message' => 'Out of stock',
                ];
            }
        }
    }
}
