<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\NewsImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsImageController extends Controller
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
          return $image->storeAs('news-images', uniqid().'-'.$image->getClientOriginalName());
      }
  
    }

    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, NewsImage $image = null)
    {   
      if($request->ajax()){
         return Storage::delete($request->image);
      }

      Storage::delete($image->filename);
      $image->delete();
      return back()->with('success', 'News Image Deleted');
    }
}