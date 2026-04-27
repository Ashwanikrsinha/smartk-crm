@csrf
<div class="row">
    <div class="mb-3">
        <label for="" class="form-label">Title</label>
        <input type="text" class="form-control" name="title" value="{{ $news->title ?? old('title') }}" required>
    </div>

    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Event</label>
        <select name="event_id" id="event_id" class="form-control" required>
            @if(isset($news))
                @foreach($events as $key => $event)
                <option value="{{ $key }}" {{ $news->event_id == $key ? 'selected' : '' }}>
                    {{ $event }}
                </option>
                @endforeach
            @else
                <option selected value="">Choose...</option>
                @foreach($events as $key => $event)
                <option {{ old('event_id') == $key ? 'selected' : '' }} value="{{ $key }}">{{ $event}}</option>
                @endforeach
            @endif
        </select>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="" class="form-label">Publish Date</label>
        <input type="date" class="form-control" name="published_at"
        value="{{ isset($news) ? $news->published_at->format('Y-m-d') : date('Y-m-d') }}" required>
    </div>


    <div class="mb-3">
        <label for="" class="form-label">Description</label>
        <textarea name="description" cols="30" rows="5" class="form-control">{{ $news->description ?? old('description') }}</textarea>
    </div>

    <div id="filepond-alert" class="alert alert-danger d-none my-3">
        Only images are allowed with max size 10MB.
    </div>

    <div class="col-lg-12 mb-3">
        <label for="attachemnts" class="form-label">Images</label>
        <input type="file" name="images[]" multiple max="3" id="images">
        <small class="text-muted d-block" style="transform: translateY(-6px);">
            <strong>Formats</strong>: jpg, png, jpeg.</small>
    </div>


    <div class="mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
            value="1" {{ isset($news) ? $news->is_active ? 'checked' : '' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>

</div>

<button type="submit" class="btn btn-primary">{{ $mode == 'create' ? 'Save' : 'Update' }}</button>


@push('scripts')

<script>

    $(document).ready(() => {

        $('select').selectize();
        $('input[type=date]').flatpickr({
            altInput: true,
            altFormat: 'F j, Y',
            dateFormat: 'Y-m-d',
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



        const filePondAlertEl = $('#filepond-alert');
        FilePond.create(document.querySelector('#images'));

        FilePond.setOptions({
            server : {
                headers : {
                      'X-CSRF-TOKEN' : '{{ csrf_token() }}',
                      'X-Requested-With': 'XMLHttpRequest',
                },
                process : {
                    url : `{{ route('news.images.store') }}`,
                    onerror : (res) => {
                        console.log(res);
                        filePondAlertEl.removeClass('d-none');
                        setTimeout(() => filePondAlertEl.addClass('d-none'), 4000);
                    }
                },
                revert: {
                  url : `{{ route('news.images.destroy') }}`,
                  _method : 'DELETE',
                }
            }
        })


    });

</script>

@endpush
