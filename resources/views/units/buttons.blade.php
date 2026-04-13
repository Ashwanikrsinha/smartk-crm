@can('update', $unit)
<a href="{{ route('units.edit', ['unit' => $unit]) }}" class="btn text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('delete', $unit)
    <form class="d-inline-block" action="{{ route('units.destroy', ['unit' => $unit]) }}" method="POST"
    onsubmit="return confirm('Are You Sure?')">
        @csrf
        @method('DELETE')
        <button class="btn"><i class="feather icon-trash-2 text-primary"></i></button>
    </form>
@endcan