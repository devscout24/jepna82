@extends('backend.app')

@section('title', 'Monthly Plan')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Monthly Plans</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Monthly Plan Create</li>
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
                    <h4 class="card-title mb-0 flex-grow-1">Create Monthly Plan</h4>
                    <a href="{{ route('admin.monthly_plan.index') }}" class="btn btn-sm btn-secondary">Back</a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('admin.monthly_plan.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Title Text</label>
                            <input type="text" name="title_text" class="form-control" value="{{ old('title_text') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="decimal" name="price" class="form-control" value="{{ old('price') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Duration</label>
                            <input type="text" name="duration" class="form-control" value="{{ old('duration') }}" placeholder="e.g. 1 Month">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Plan ID</label>
                            <input type="text" name="plan_id" class="form-control" value="{{ old('plan_id') }}" placeholder="Optional internal plan id">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stripe Price ID</label>
                            <input type="text" name="stripe_price_id" class="form-control" value="{{ old('stripe_price_id') }}" placeholder="price_xxx">
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection