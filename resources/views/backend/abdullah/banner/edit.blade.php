@extends('backend.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between">
                <h4>Banner Edit</h4>
                <a href="{{ route('admin.banner.index') }}" class="btn btn-secondary btn-sm">← Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.banner.update', $banner->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title) }}">
                    </div>

                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" id="description" class="form-control summernote" rows="4">{!! old('description', $banner->description) !!}</textarea>
                        @if ($errors->has('description'))
                            <div class="text-danger small">{{ $errors->first('description') }}</div>
                        @endif
                    </div>

                    {{-- <div class="mb-3">
                        <label>Button Text</label>
                        <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $banner->button_text) }}">
                    </div> --}}

                    <div class="mb-3">
                        <label>Icons</label>

                        <div id="icon-wrapper">
                            @php
                                $existingIcons = old('existing_icons', $banner->icon ?? []);
                                $iconTexts = old('icon_texts', $banner->icon_title ?? []);
                                $rowCount = max(count($existingIcons), count($iconTexts), 1);
                            @endphp

                            @for ($index = 0; $index < $rowCount; $index++)
                                @php
                                    $existingIcon = $existingIcons[$index] ?? null;
                                    $iconText = $iconTexts[$index] ?? '';
                                @endphp

                                <div class="row mb-3 icon-item align-items-start">
                                    <div class="col-md-4">
                                        @if ($existingIcon)
                                            <img src="{{ asset($existingIcon) }}" alt="Icon" class="img-thumbnail mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                                            <input type="hidden" name="existing_icons[]" value="{{ $existingIcon }}">
                                        @endif
                                        <input type="file" name="icons[]" class="form-control" accept="image/*">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="icon_texts[]" class="form-control" placeholder="Icon text" value="{{ $iconText }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger remove-icon">X</button>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <button type="button" id="add-icon" class="btn btn-success btn-sm mt-2">+ Add More</button>
                        @if ($errors->has('icon_texts'))
                            <div class="text-danger small mt-1">{{ $errors->first('icon_texts') }}</div>
                        @endif
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.summernote) {
            window.jQuery('.summernote').summernote({
                height: 200
            });

            window.jQuery('form').on('submit', function () {
                window.jQuery('.summernote').each(function () {
                    var code = window.jQuery(this).summernote('code');
                    window.jQuery(this).val(code);
                });
            });
        }

        var iconWrapper = document.getElementById('icon-wrapper');
        var addBtn = document.getElementById('add-icon');

        if (!iconWrapper || !addBtn) {
            return;
        }

        addBtn.addEventListener('click', function (e) {
            e.preventDefault();

            var div = document.createElement('div');
            div.className = 'row mb-3 icon-item align-items-start';
            div.innerHTML = `
                <div class="col-md-4">
                    <input type="file" name="icons[]" class="form-control" accept="image/*">
                </div>
                <div class="col-md-6">
                    <input type="text" name="icon_texts[]" class="form-control" placeholder="Icon text">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-icon">X</button>
                </div>
            `;

            iconWrapper.appendChild(div);
        });

        iconWrapper.addEventListener('click', function (e) {
            if (!e.target.classList.contains('remove-icon')) {
                return;
            }

            e.preventDefault();

            var row = e.target.closest('.icon-item');
            var items = iconWrapper.querySelectorAll('.icon-item');

            if (items.length > 1) {
                row.remove();
                return;
            }

            var fileInput = row.querySelector('input[type=file]');
            var textInput = row.querySelector('input[type=text]');
            var hiddenInput = row.querySelector('input[name="existing_icons[]"]');
            var preview = row.querySelector('img');

            if (fileInput) fileInput.value = '';
            if (textInput) textInput.value = '';
            if (hiddenInput) hiddenInput.value = '';
            if (preview) preview.remove();
        });
    });
</script>
@endpush
