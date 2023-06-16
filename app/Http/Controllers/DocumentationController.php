<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    //

    public function home()
    {

        return view('documentations.document');
    }

    // Vehicle API
    public function getyears()
    {

        return view('documentations.vehicle-api.get-years');
    }

    public function getmakes()
    {
        return view('documentations.vehicle-api.get-makes');
    }
    public function getmodels()
    {
        return view('documentations.vehicle-api.get-models');
    }
    public function getoptions()
    {
        return view('documentations.vehicle-api.get-options');
    }
    public function getsize()
    {
        return view('documentations.vehicle-api.get-size');
    }


    // Wheel API

    public function wheelgetbrand()
    {
        return view('documentations.wheel-api.get-brand');
    }
    public function wheelgetmspn()
    {
        return view('documentations.wheel-api.get-mspn');
    }
    public function wheelgetsize()
    {
        return view('documentations.wheel-api.get-size');
    }
    public function getwheelsbyvehicle()
    {
        return view('documentations.wheel-api.get-wheels-by-vehicle');
    }


     // Tire API

     public function tiregetbrand()
     {
         return view('documentations.tire-api.get-brand');
     }
     public function tiregetmspn()
     {
         return view('documentations.tire-api.get-mspn');
     }
     public function tiregetsize()
     {
         return view('documentations.tire-api.get-size');
     }
     public function gettiresbyvehicle()
     {
         return view('documentations.tire-api.get-tires-by-vehicle');
     }

    //  Inventory API

    public function getlocation()
    {
        return view('documentations.inventory-api.get-location');
    }
    public function getinventorybylocation()
    {
        return view('documentations.inventory-api.get-price-by-location');
    }

    
}
