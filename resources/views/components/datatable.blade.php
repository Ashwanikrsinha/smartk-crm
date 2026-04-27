@props(['id', 'columns', 'small'])

<div class="card border-0 shadow-sm rounded mb-4">
    <div class="card-body px-0">
        <section class="table-responsive-lg">
            <table class="table w-100 {{ isset($small) ? 'small' : '' }}" id="{{ $id ?? '' }}">
                <thead>
                <tr>
                @if (isset($columns))
                    @foreach ($columns as $column)
                        <th>{{ ucwords($column) }}</th>
                    @endforeach
                @endif
                </tr>
                </thead>
            </table>
        </section>
    </div>
</div>
