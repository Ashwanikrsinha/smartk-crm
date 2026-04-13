@can('update', $category)
<a href="{{ route('categories.edit', ['category' => $category]) }}" class="btn text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('delete', $category)
    <form class="d-inline-block" action="{{ route('categories.destroy', ['category' => $category]) }}" method="POST"
    onsubmit="return confirm('Are You Sure?')">
        @csrf
        @method('DELETE')
        <button class="btn"><i class="feather icon-trash-2 text-primary"></i></button>
    </form>
@endcan