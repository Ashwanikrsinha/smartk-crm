<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\QuotationAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuotationAttachmentController extends Controller
{
    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
      
      // validation

      foreach($request->attachments as $attachment){
          return $attachment->storeAs('quotation-attachments', uniqid().'-'.$attachment->getClientOriginalName());
      }
  
    }

    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, QuotationAttachment $attachment = null)
    {   
      if($request->ajax()){
         return Storage::delete($request->attachment);
      }

      Storage::delete($attachment->filename);
      $attachment->delete();
      return back()->with('success', 'Quotation Attachment Deleted');
    }
}