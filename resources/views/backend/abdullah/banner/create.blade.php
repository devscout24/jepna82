@extends('backend.app')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@section('content')

    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between">
                <h4>Banner Create</h4>
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

                <form method="POST" action="{{ route('admin.banner.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Title --}}
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" id="description" class="form-control summernote" rows="4">{!! old('description') !!}</textarea>
                        @if ($errors->has('description'))
                            <div class="text-danger small">{{ $errors->first('description') }}</div>
                        @endif
                    </div>

                    {{-- Button Text --}}
                    <div class="mb-3">
                        <label>Button Text</label>
                        <input type="text" name="button_text" class="form-control" value="{{ old('button_text') }}">
                    </div>

                    {{-- ICON ARRAY --}}
                    <div class="mb-3">
                        <label>Icons</label>

                        <div id="icon-wrapper">
                            @php $icon_texts = old('icon_texts', []); @endphp
                            @if (count($icon_texts) > 0)
                                @foreach ($icon_texts as $text)
                                    <div class="row mb-2 icon-item">
                                        <div class="col-md-5">
                                            <input type="file" name="icons[]" class="form-control" accept="image/*">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" name="icon_texts[]" class="form-control"
                                                placeholder="Icon text" value="{{ $text }}">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-icon">X</button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="row mb-2 icon-item">
                                    <div class="col-md-5">
                                        <input type="file" name="icons[]" class="form-control" accept="image/*">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="icon_texts[]" class="form-control"
                                            placeholder="Icon text">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger remove-icon">X</button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <button type="button" id="add-icon" class="btn btn-success btn-sm mt-2">
                            + Add More
                        </button>
                        @if ($errors->has('icon_texts'))
                            <div class="text-danger small mt-1">{{ $errors->first('icon_texts') }}</div>
                        @endif
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary">Save</button>
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

        if (!iconWrapper || !addBtn) return;

        // ADD
        addBtn.addEventListener('click', function (e) {
            e.preventDefault();

            var div = document.createElement('div');
            div.className = 'row mb-2 icon-item';

            div.innerHTML = `
                <div class="col-md-5">
                    <input type="file" name="icons[]" class="form-control" accept="image/*">
                </div>
                <div class="col-md-5">
                    <input type="text" name="icon_texts[]" class="form-control" placeholder="Icon text">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-icon">X</button>
                </div>
            `;

            iconWrapper.appendChild(div);
        });

        // REMOVE (delegation)
        iconWrapper.addEventListener('click', function (e) {
            if (!e.target.classList.contains('remove-icon')) return;

            e.preventDefault();

            var row = e.target.closest('.icon-item');
            var items = iconWrapper.querySelectorAll('.icon-item');

            if (items.length > 1) {
                row.remove();
            } else {
                row.querySelector('input[type=file]').value = '';
                row.querySelector('input[type=text]').value = '';
            }
        });
    });
</script>
@endpush
