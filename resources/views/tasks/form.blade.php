@csrf
<div class="row">
    <div class="mb-3">
        <label for="" class="form-label">Title</label>
        <input type="text" value="{{ $task->title ?? old('title') }}" name="title" class="form-control" required>
    </div>


    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Deadline Time</label>
        <input type="date" name="deadline_time"
        value="{{ isset($task) ? $task->deadline_time->format('Y-m-d h:i') : old('deadline_time') }}"
         class="form-control" required>
    </div>
    
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Assignee Name (User)</label>
        <select name="assignee_id" id="assignee_id" class="form-control" required>
            @if(isset($task))
                @foreach($users as $key => $user)
                <option value="{{ $key }}" {{ $task->assignee_id == $key ? 'selected' : '' }}>
                    {{ $user }}
                </option>
                @endforeach   
            @else
                <option selected value="">Choose...</option>
                @foreach($users as $key => $user)
                @unless (auth()->id() == $key)
                  <option {{ old('assignee_id') == $key ? 'selected' : '' }} value="{{ $key }}">{{ $user}}</option>
                @endunless
                @endforeach
            @endif
        </select>
    </div>

</div>

<div class="mb-3">
    <label for="" class="form-label">Description (Optional)</label>
    <textarea name="description" id="" cols="30" rows="4" maxlength="250" class="form-control">{{ $task->description ?? old('description') }}</textarea>
</div>


<div class="mb-4">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="is_completed" name="is_completed" value="1" 
        {{ isset($task->completed_at) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_completed">Complete ?</label>
    </div>
</div>

<button type="submit" class="btn btn-primary">{{ $mode == 'create' ? 'Assign' : 'Edit' }}</button>


@push('scripts')

<script>

    $(document).ready(() => {
        
        $('select').selectize(); 

        $('input[type=date]').flatpickr({
            altInput: true,
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
        });  

        tinymce.init({
            selector: '[name=description]',
            height: 420,
            branding: false,
            plugins: 'lists link image paste table fullscreen',
            toolbar: `undo redo | bold italic underline | alignleft
                    aligncenter alignright alignjustify | bullist numlist outdent indent 
                    | table |link image | fullscreen`,
        });

    });

</script>

@endpush
