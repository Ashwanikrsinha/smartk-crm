@can('view', $task)
<a href="{{ route('tasks.show', ['task' => $task]) }}" class="btn btn-sm text-primary"><i class="feather icon-eye"></i></a>
@endcan

@can('update', $task)
<a href="{{ route('tasks.edit', ['task' => $task]) }}" class="btn btn-sm text-primary"><i class="feather icon-settings"></i></a>
@endcan

@can('delete', $task)
<form class="d-inline-block" action="{{ route('tasks.destroy', ['task' => $task]) }}" method="POST"
onsubmit="return confirm('Are You Sure?')">
    @csrf
    @method('DELETE')
    <button class="btn btn-sm"><i class="feather icon-trash-2 text-primary"></i></button>
</form>
@endcan