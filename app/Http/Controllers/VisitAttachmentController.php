<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\VisitAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VisitAttachmentController extends Controller
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
        'attachments.*' => 'mimes:jpg,png,jpeg,doc,pdf,docx,xlsx,xls|max:10240'
      ]); 

      foreach($request->attachments as $attachment){
          return $attachment->storeAs('visit-attachments', uniqid().'-'.$attachment->getClientOriginalName());
      }
  
    }

    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, VisitAttachment $attachment = null)
    {   
      if($request->ajax()){
         return Storage::delete($request->attachment);
      }

      Storage::delete($attachment->filename);
      $attachment->delete();
      return back()->with('success', 'Visit Attachment Deleted');
    }
}