@can('update', $transport)
<a href="{{ route('transports.edit', ['transport' => $transport]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('delete', $transport)
    <form class="d-inline-block" action="{{ route('transports.destroy', ['transport' => $transport]) }}" method="POST"
    onsubmit="return confirm('Are You Sure?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
    </form>
@endcan