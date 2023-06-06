<?php

namespace App\Http\Controllers;

use App\Models\CatalogSettings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    
    public function showSettings(){
        return view('settings.settings');
    }


    public function submitCatalog(Request $request){

        $catalogs = CatalogSettings::get();

        foreach($catalogs as $catalog){
            $catalog->is_show = 0;
            $catalog->save();
        }

        foreach ($request->input('selectedKeys') as $key){
            $catalog = CatalogSettings::where('id', $key)->update(['is_show' => 1]);
        }

        return redirect()->back();
    }
}
