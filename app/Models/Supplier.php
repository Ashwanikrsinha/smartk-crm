<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;


    public function segment()
    {
        return $this->belongsTo(Segment::class, 'segment_id')->withDefault(['name' => '']);
    }

   

    public function contacts()
    {
        return $this->hasMany(SupplierContact::class, 'supplier_id');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class, 'supplier_id')->orderBy('visit_date', 'DESC');
    }


    public function types()
    {
        $ids = explode(',', $this->supplier_types);
        return CustomerType::find($ids) ?? null;
    }


    public function createContacts($request){

        foreach ($request->person_name as $i => $name) {
            $this->contacts()->create([
             'name' => $name,
             'birth_date' => $request->birth_date[$i],
             'marriage_date' => $request->marriage_date[$i],
             'contact_number' => $request->contact_number[$i],
             'designation_id' => $request->designation_id[$i],
            ]);
        }
    }
   
}