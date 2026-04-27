@if (auth()->user()->isAccounts() || auth()->user()->isAdmin())

    <div class="dropdown d-inline-block">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
            title="Update status">
            Update
        </button>
        <ul class="dropdown-menu shadow-sm border-0">
            @foreach (['pending', 'cleared', 'bounced'] as $status)
                @if ($pdc->status !== $status)
                    <li>
                        <form action="{{ route('pdcs.update', $pdc) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="{{ $status }}">
                            <button type="submit"
                                class="dropdown-item
                    {{ $status === 'cleared' ? 'text-success' : ($status === 'bounced' ? 'text-danger' : '') }}">
                                <i
                                    class="feather {{ $status === 'cleared' ? 'icon-check-circle' : ($status === 'bounced' ? 'icon-x-circle' : 'icon-clock') }} me-1"></i>
                                Mark {{ ucfirst($status) }}
                            </button>
                        </form>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>

@endif
