<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\State;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    
    public function __construct()
    {
        $this->authorizeResource(Company::class, 'company');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
    
        $validatedData = $request->validate([
            'name' => 'required|max:150',
            'logo' => 'nullable|image|mimes:jpg,png,jpeg',
            'address' => 'required|max:200',
            'phone_number' => 'required|max:150',
            'email' => 'nullable|max:150',
            'gst_number' => 'nullable|max:150',
            'terms' => 'nullable|max:6000',
        ]);

        if ($request->hasFile('logo')) {
            $validatedData['logo'] = $request->logo->store('logo');

            if (isset($company->logo)) {
                Storage::delete($company->logo);
            }
        }
       
        $company->update($validatedData);
        return  back()->with('success', 'Company Updated');
    }
}
