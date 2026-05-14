@extends('backend.app')

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Subscribe Plans</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Subscribe Plan Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Subscribe Plan Edit</h4>
                    <a href="{{ route('admin.subscribe_plan.index') }}" class="btn btn-sm btn-secondary">Back</a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.subscribe_plan.update', $subscribePlan->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $subscribePlan->title) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Title Text</label>
                            <input type="text" name="title_text" class="form-control" value="{{ old('title_text', $subscribePlan->title_text) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $subscribePlan->price) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Price Text</label>
                            <input type="text" name="price_text" class="form-control" value="{{ old('price_text', $subscribePlan->price_text) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Items</label>

                            <div id="items-wrapper">
                                @php
                                    $existingItems = old('items', $subscribePlan->items ?? []);
                                    if (is_string($existingItems)) {
                                        $existingItems = json_decode($existingItems, true) ?? [];
                                    }
                                    $rowCount = count($existingItems) > 0 ? count($existingItems) : 1;
                                @endphp

                                @for ($index = 0; $index < $rowCount; $index++)
                                    @php $item = $existingItems[$index] ?? ''; @endphp

                                    <div class="row g-2 mb-2 item-row align-items-center">
                                        <div class="col-md-10">
                                            <input type="text" name="items[]" class="form-control" placeholder="Item name" value="{{ $item }}">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm remove-item w-100">Remove</button>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <button type="button" id="add-item" class="btn btn-success btn-sm mt-2">+ Add More</button>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('click', function(e) {
                if (e.target && e.target.id === 'add-item') {
                    const wrapper = document.getElementById('items-wrapper');
                    const row = document.createElement('div');
                    row.className = 'row g-2 mb-2 item-row align-items-center';
                    row.innerHTML = '<div class="col-md-10"><input type="text" name="items[]" class="form-control" placeholder="Item name"></div>' +
                        '<div class="col-md-2"><button type="button" class="btn btn-danger btn-sm remove-item w-100">Remove</button></div>';
                    wrapper.appendChild(row);
                }

                if (e.target && e.target.classList.contains('remove-item')) {
                    const row = e.target.closest('.item-row');
                    if (row) {
                        row.remove();
                    }
                }
            });
        </script>
    @endpush

@endsection
