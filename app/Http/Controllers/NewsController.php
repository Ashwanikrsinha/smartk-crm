<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Event;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\NewsRequest;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(News::class, 'news');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $news = News::with(['event'])->select();
  
            return DataTables::of($news)
                    ->editColumn('title', function ($news) {
                        return Str::limit($news->title, 40);
                    })
                    ->editColumn('published_at', function ($news) {
                        return $news->published_at->format('d M, Y');
                    })
                    ->editColumn('is_active', function ($news) {
                        return $news->is_active == 0 ? '' : 'Active';
                    })
                  ->addColumn('action', function ($news) {
                      return view('news.buttons')->with(['news' => $news]);
                  })->make(true);
        }

        return view('news.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $events = Event::orderBy('name')->pluck('name', 'id');
        return view('news.create', compact('events'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NewsRequest $request)
    {
        DB::transaction(function () use ($request) {
            $news = News::create([
            'title' => $request->title,
            'event_id' => $request->event_id,
            'published_at' => $request->published_at,
            'is_active' => $request->has('is_active'),
            'description' => $request->description,
           ]);

            if ($request->filled('images.0')) {
                $news->createNewsImages($request);
            }
        });
            
        return  redirect()->route('news.index')->with('success', 'News Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\News $news
     * @return \Illuminate\Http\Response
     */
    public function show(News $news)
    {
        if ($news->images->count() > 0) {
            $news->load('images');
        }
        return view('news.show', compact('news'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\News $news
     * @return \Illuminate\Http\Response
     */
    public function edit(News $news)
    {
        if ($news->images->count() > 0) {
            $news->load('images');
        }

        $events = Event::orderBy('name')->pluck('name', 'id');
        return view('news.edit', compact('news', 'events'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\News $news
     * @return \Illuminate\Http\Response
     */
    public function update(NewsRequest $request, News $news)
    {
        DB::transaction(function () use ($request, $news) {
            $news->update([
            'title' => $request->title,
            'event_id' => $request->event_id,
            'published_at' => $request->published_at,
            'is_active' => $request->has('is_active'),
            'description' => $request->description,
           ]);

            if ($request->filled('images.0')) {
                $news->createNewsImages($request);
            }
        });
       
        return  back()->with('success', 'News Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\News $news
     * @return \Illuminate\Http\Response
     */
    public function destroy(News $news)
    {
        $news->images()->delete();
        $news->delete();
        return back()->with('success', 'News Deleted');
    }
}
