@extends('backend.app')

@section('title', 'Package Edit')

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between">
                <h4>Package Edit</h4>
                <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary btn-sm">← Back</a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.packages.update', $package->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title', $package->title) }}" required>
                                    @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Package Type</label>
                                    <select name="package_type" class="form-select" required>
                                        <option value="free_preview" {{ $package->package_type == 'free_preview' ? 'selected' : '' }}>Free Preview</option>
                                        <option value="one_time_basic" {{ $package->package_type == 'one_time_basic' ? 'selected' : '' }}>One Time Basic</option>
                                        <option value="one_time_pro" {{ $package->package_type == 'one_time_pro' ? 'selected' : '' }}>One Time Pro</option>
                                        <option value="bulk" {{ $package->package_type == 'bulk' ? 'selected' : '' }}>Bulk</option>
                                        <option value="subscription" {{ $package->package_type == 'subscription' ? 'selected' : '' }}>Subscription</option>
                                    </select>
                                    @error('package_type') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Price</label>
                                    <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price', $package->price) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Discount %</label>
                                    <input type="number" step="0.01" name="discount_percentage" id="discount_percentage" class="form-control" value="{{ old('discount_percentage', $package->discount_percentage) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Final Price</label>
                                    <input type="number" step="0.01" name="final_price" id="final_price" class="form-control" value="{{ old('final_price', $package->final_price) }}" required readonly>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Page Limit</label>
                                    <input type="number" name="page_limit" class="form-control" value="{{ old('page_limit', $package->page_limit) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Extra Page Rate</label>
                                    <input type="number" step="0.0001" name="extra_page_rate" class="form-control" value="{{ old('extra_page_rate', $package->extra_page_rate) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Billing Cycle</label>
                                    <select name="billing_cycle" class="form-select">
                                        <option value="one_time" {{ $package->billing_cycle == 'one_time' ? 'selected' : '' }}>One Time</option>
                                        <option value="monthly" {{ $package->billing_cycle == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="yearly" {{ $package->billing_cycle == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                        <option value="lifetime" {{ $package->billing_cycle == 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Features (One per line)</label>
                                <textarea name="features" class="form-control" rows="5">{{ old('features', is_array($package->features) ? implode("\n", $package->features) : '') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="1" {{ $package->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $package->status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Package</button>
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
