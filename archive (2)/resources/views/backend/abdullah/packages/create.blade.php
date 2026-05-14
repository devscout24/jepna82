@extends('backend.app')

@section('title', 'Package Create')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between">
                <h4>Package Create</h4>
                <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary btn-sm">← Back</a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.packages.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                                    @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Package Type</label>
                                    <select name="package_type" class="form-select" required>
                                        <option value="free_preview">Free Preview</option>
                                        <option value="one_time_basic">One Time Basic</option>
                                        <option value="one_time_pro">One Time Pro</option>
                                        <option value="bulk">Bulk</option>
                                        <option value="subscription">Subscription</option>
                                    </select>
                                    @error('package_type') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Price</label>
                                    <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price', 0) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Discount %</label>
                                    <input type="number" step="0.01" name="discount_percentage" id="discount_percentage" class="form-control" value="{{ old('discount_percentage', 0) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Final Price</label>
                                    <input type="number" step="0.01" name="final_price" id="final_price" class="form-control" value="{{ old('final_price', 0) }}" required readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Page Limit</label>
                                    <input type="number" name="page_limit" class="form-control" value="{{ old('page_limit', 0) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Extra Page Rate</label>
                                    <input type="number" step="0.0001" name="extra_page_rate" class="form-control" value="{{ old('extra_page_rate', 0.0000) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Billing Cycle</label>
                                    <select name="billing_cycle" class="form-select">
                                        <option value="one_time">One Time</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                        <option value="lifetime">Lifetime</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Features (One per line)</label>
                                <textarea name="features" class="form-control" rows="5">{{ old('features') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Package</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        function calculateFinalPrice() {
            let price = parseFloat($('#price').val()) || 0;
            let discount = parseFloat($('#discount_percentage').val()) || 0;
            let finalPrice = price - (price * (discount / 100));
            $('#final_price').val(finalPrice.toFixed(2));
        }

        $('#price, #discount_percentage').on('input', calculateFinalPrice);
    });
</script>
@endpush
