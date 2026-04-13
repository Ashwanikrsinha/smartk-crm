<?php

namespace App\Http\Controllers;

use App\Models\Segment;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;

class SegmentController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Segment::class, 'segment');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $segments = Segment::with('category')->select();
  
            return DataTables::of($segments)
                  ->addColumn('action', function ($segment) {
                      return view('segments.buttons')->with(['segment' => $segment]);
                  })->make(true);
        }

        return view('segments.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Segment::orderBy('name')->pluck('name', 'id');
        return view('segments.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:segments|max:100',
            'category_id' => 'required',
        ]);
       
        Segment::create($validatedData);

        return  redirect()->route('segments.index')->with('success', 'Segment Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Segment  $segment
     * @return \Illuminate\Http\Response
     */
    public function show(Segment $segment)
    {
        return abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Segment  $segment
     * @return \Illuminate\Http\Response
     */
    public function edit(Segment $segment)
    {
        $categories = Segment::orderBy('name')->pluck('name', 'id');
        return view('segments.edit', compact('segment', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Segment  $segment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Segment $segment)
    {

        $validatedData = $request->validate([
            'name' => 'required|max:100|'.Rule::unique('segments')->ignore($segment),
            'category_id' => 'required',
        ]);
       
        $segment->update($validatedData);

        return  redirect()->route('segments.index')->with('success', 'Segment Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Segment  $segment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Segment $segment)
    {
        $segment->delete();
        return back()->with('success', 'Segment Deleted');
    }
}
