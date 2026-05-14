@extends('backend.app')

@section('title', 'Stripe Settings')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">Stripe Settings</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Stripe Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title mb-0 flex-grow-1">Update Stripe Settings</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.stripe-settings.update') }}">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="stripe_key" class="form-label">Stripe Public Key (STRIPE_KEY)</label>
                            <input type="text" name="stripe_key" id="stripe_key" class="form-control @error('stripe_key') is-invalid @enderror"
                                value="{{ env('STRIPE_KEY') }}" placeholder="Enter Stripe Public Key" required>
                            @error('stripe_key')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="stripe_secret" class="form-label">Stripe Secret Key (STRIPE_SECRET)</label>
                            <input type="text" name="stripe_secret" id="stripe_secret" class="form-control @error('stripe_secret') is-invalid @enderror"
                                value="{{ env('STRIPE_SECRET') }}" placeholder="Enter Stripe Secret Key" required>
                            @error('stripe_secret')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="stripe_webhook_secret_subscription" class="form-label">Stripe Webhook Secret (Subscription)</label>
                            <input type="text" name="stripe_webhook_secret_subscription" id="stripe_webhook_secret_subscription" class="form-control @error('stripe_webhook_secret_subscription') is-invalid @enderror"
                                value="{{ env('STRIPE_WEBHOOK_SECRET_SUBSCRIPTION') }}" placeholder="Enter Stripe Webhook Secret for Subscriptions">
                            @error('stripe_webhook_secret_subscription')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="stripe_webhook_secret_bulk" class="form-label">Stripe Webhook Secret (Bulk/One-time)</label>
                            <input type="text" name="stripe_webhook_secret_bulk" id="stripe_webhook_secret_bulk" class="form-control @error('stripe_webhook_secret_bulk') is-invalid @enderror"
                                value="{{ env('STRIPE_WEBHOOK_SECRET_BULK') }}" placeholder="Enter Stripe Webhook Secret for Bulk/One-time">
                            @error('stripe_webhook_secret_bulk')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
