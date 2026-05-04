@extends('backend.app')

@section('title', 'Stat Edit')

@section('content')

    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between">
                <h4>Stat Edit</h4>
                <a href="{{ route('admin.stat.index') }}" class="btn btn-secondary btn-sm">← Back</a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Stat Edit</h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('admin.stat.update', $stat->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" id="title" value="{{ old('title', $stat->title) }}">
                                @error('title')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" class="form-control summernote" id="description">{{ old('description', $stat->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="icon" class="form-label">Icon</label>
                                @if($stat->icon)
                                    <div class="mb-2">
                                        <img src="{{ asset($stat->icon) }}" alt="Current Icon" style="max-width:100px;height:auto;border-radius:4px;">
                                    </div>
                                @endif
                                <input type="file" name="icon" class="form-control" id="icon" accept="image/*">
                                <small class="text-muted">Leave empty to keep current icon</small>
                                @error('icon')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 200
            });

            // Sync Summernote content back to textarea on form submit
            $('form').on('submit', function() {
                $('.summernote').each(function() {
                    $(this).val($(this).summernote('code'));
                });
            });
        });
    </script>
@endpush
