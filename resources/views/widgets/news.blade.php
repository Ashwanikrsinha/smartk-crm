<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
       <span>
          <i class="feather icon-message-circle me-1 bg-primary text-white rounded p-1"></i> Recent News
       </span>
       <a href="{{ route('news.index') }}" class="text-primary d-flex align-items-center btn btn-sm">
          More <i class="feather icon-chevron-right"></i>
       </a>
    </div>
    <div class="card-body">
       @if ($news->count() > 0)
        <div class="row">
            @foreach ($news as $new)
                <div class="col-lg-6 col-xl-3">
                    <article class="mb-4">
                        <a href="{{ route('news.show', ['news' => $new ]) }}" target="_blank" class="d-block bg-light">
                        <div class="w-100 rounded mb-2" 
                            style="height:12rem; 
                            background: no-repeat center/cover {{ isset($new->image->filename) ? 'url('. url('storage/'.$new->image->filename).')' : '#cfe4ee' }};">
                        </div>
                        </a>
                        
                        <h6 class="mb-2 border-bottom pb-2">{{ Str::limit($new->title, 30) }}</h6>
                        <div class="d-flex justify-content-between">     
                        <small class="text-muted">{{ $new->published_at->format('d M, Y') }}</small>
                        <span class="badge alert-primary rounded-pill">{{ $new->event->name }}</span>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
       @endif
    </div>
 </div>
 