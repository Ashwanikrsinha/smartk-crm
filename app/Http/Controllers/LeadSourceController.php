<?php

namespace App\Http\Controllers;

use App\Models\LeadSource;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LeadSourceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of(
                LeadSource::withCount('customers')->select('id', 'name')
            )
                ->addColumn('action', fn($ls) => view('lead-sources.buttons', ['leadSource' => $ls])->render())
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('lead-sources.index');
    }

    public function create()
    {
        return view('lead-sources.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:lead_sources,name',
        ]);

        LeadSource::create(['name' => $request->name]);

        return redirect()->route('lead-sources.index')
            ->with('success', 'Lead source created.');
    }

    public function edit(LeadSource $leadSource)
    {
        return view('lead-sources.edit', compact('leadSource'));
    }

    public function update(Request $request, LeadSource $leadSource)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:lead_sources,name,' . $leadSource->id,
        ]);

        $leadSource->update(['name' => $request->name]);

        return back()->with('success', 'Lead source updated.');
    }

    public function destroy(LeadSource $leadSource)
    {
        if ($leadSource->customers()->count() > 0) {
            return back()->with('error', 'Cannot delete — ' . $leadSource->customers_count . ' school(s) are linked to this source.');
        }

        $leadSource->delete();

        return redirect()->route('lead-sources.index')
            ->with('success', 'Lead source deleted.');
    }
}
