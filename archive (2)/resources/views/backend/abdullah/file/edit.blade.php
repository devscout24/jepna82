@extends('backend.app')

@section('title', 'Edit File')

@section('content')

    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between">
                <h4>Edit File</h4>
                <a href="{{ route('admin.file.index') }}" class="btn btn-secondary btn-sm">← Back</a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Edit File</h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('admin.file.update', $file->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="file" class="form-label">File</label>
                                @if($file->file)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/' . $file->file) }}" target="_blank">Current file</a>
                                    </div>
                                @endif
                                <input type="file" name="file" class="form-control" id="file">
                                <small class="text-muted">Leave empty to keep current file</small>
                                @error('file')
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
