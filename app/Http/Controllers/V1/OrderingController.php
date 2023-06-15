<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderingController extends Controller
{
    public function postQuote(Request $request)
    {
        $request->validate([
            'fname' => 'required|alpha:ascii',
            'lname' => 'required|alpha:ascii',
            'address1' => 'required|string',
            'address2' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postalCode' => 'required|numeric',
            'country' => 'required|alpha:ascii',
            'quantity' => 'required|integer|min:1',
            'brandname' => 'required|string',
            'partNumber' => 'required|string',
            'phoneNumber' => 'required|integer|size:10',
        ]);
    }
}
