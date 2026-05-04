@extends('backend.app')

@section('content')

    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between">
                <h4>Scan Edit</h4>
                <a href="{{ route('admin.scan.index') }}" class="btn btn-secondary btn-sm">← Back</a>
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

                <form method="POST" action="{{ route('admin.scan.update', $scans->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Title --}}
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $scans->title) }}" required>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $scans->description) }}</textarea>
                    </div>

                    {{-- Price --}}
                    <div class="mb-3">
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $scans->price) }}">
                    </div>

                    {{-- ITEMS ARRAY --}}
                    <div class="mb-3">
                        <label>Items</label>

                        <div id="items-wrapper">
                            @php
                                $existingItems = old('items', $scans->items ?? []);
                                $rowCount = count($existingItems) > 0 ? count($existingItems) : 1;
                            @endphp

                            @for ($index = 0; $index < $rowCount; $index++)
                                @php
                                    $item = $existingItems[$index] ?? '';
                                @endphp

                                <div class="row mb-2 item-row">
                                    <div class="col-md-10">
                                        <input type="text" name="items[]" class="form-control"
                                            placeholder="Item name" value="{{ $item }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm remove-item">Remove</button>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <button type="button" id="add-item" class="btn btn-success btn-sm mt-2">
                            + Add More
                        </button>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            document.addEventListener('click', function(e) {
                if (e.target && e.target.id === 'add-item') {
                    const wrapper = document.getElementById('items-wrapper');
                    const row = document.createElement('div');
                    row.className = 'row mb-2 item-row';
                    row.innerHTML = '<div class="col-md-10"><input type="text" name="items[]" class="form-control" placeholder="Item name"></div>' +
                        '<div class="col-md-2"><button type="button" class="btn btn-danger btn-sm remove-item">Remove</button></div>';
                    wrapper.appendChild(row);
                }

                if (e.target && e.target.classList.contains('remove-item')) {
                    e.target.closest('.item-row').remove();
                }
            });
        </script>
    @endpush

@endsection
