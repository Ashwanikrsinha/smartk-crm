<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
      
      if(!$request->ajax()){
        return abort(404);
      }
    
      $request->validate([
        'images.*' => 'mimes:jpg,png,jpeg|max:10240'
      ]); 

      foreach($request->images as $image){
          return $image->storeAs('product-images', uniqid().'-'.$image->getClientOriginalName());
      }
  
    }

    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ProductImage $image = null)
    {   
      if($request->ajax()){
         return Storage::delete($request->image);
      }

      Storage::delete($image->filename);
      $image->delete();
      return back()->with('success', 'Product Image Deleted');
    }
}