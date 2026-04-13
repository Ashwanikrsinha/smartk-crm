<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use App\Http\Requests\TransportRequest;

class TransportController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Transport::class, 'transport');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $transports = Transport::select();
  
            return DataTables::of($transports)
                  ->addColumn('action', function ($transport) {
                      return view('transports.buttons')->with(['transport' => $transport]);
                  })->make(true);
        }

        return view('transports.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('transports.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TransportRequest $request)
    {
        Transport::create($request->validated());
        return  redirect()->route('transports.index')->with('success', 'Transport Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transport $transport
     * @return \Illuminate\Http\Response
     */
    public function show(Transport $transport)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transport $transport
     * @return \Illuminate\Http\Response
     */
    public function edit(Transport $transport)
    {
        return view('transports.edit', compact('transport'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transport $transport
     * @return \Illuminate\Http\Response
     */
    public function update(TransportRequest $request, Transport $transport)
    {
        $transport->update($request->validated());
        return  back()->with('success', 'Transport Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transport $transport
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transport $transport)
    {
        $transport->delete();
        return back()->with('success', 'Transport Deleted');
    }
}