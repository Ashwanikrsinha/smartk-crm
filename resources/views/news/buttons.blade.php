@can('view', $news)
<a href="{{ route('news.show', ['news' => $news]) }}" class="btn btn-sm text-primary"><i class="feather icon-eye"></i></a>
@endcan

@can('update', $news)
<a href="{{ route('news.edit', ['news' => $news]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('delete', $news)
<form class="d-inline-block" action="{{ route('news.destroy', ['news' => $news]) }}" method="POST"
onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan