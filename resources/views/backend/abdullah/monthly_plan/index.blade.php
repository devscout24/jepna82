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
						<li class="breadcrumb-item active">Monthly Plan List</li>
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
					<h4 class="card-title mb-0 flex-grow-1">Monthly Plan List</h4>
					<a href="{{ route('admin.monthly_plan.create') }}" class="btn btn-sm btn-success">Add Plan</a>
				</div>

				<div class="card-body">
					@if (session('success'))
						<div class="alert alert-success">{{ session('success') }}</div>
					@endif

					<table class="table table-bordered dt-responsive nowrap align-middle" id="monthlyPlanTable">
						<thead>
							<tr>
								<th>#</th>
								<th>Title</th>
								<th>Title Text</th>
								<th>Price</th>
								<th>Duration</th>
								<th>Plan ID</th>
								<th>Stripe Price ID</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

    @push('scripts')
        <script>
            $(function() {
                $('#monthlyPlanTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
					ajax: "{{ route('admin.monthly_plan.index') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'title',
                            name: 'title'
                        },
                        {
                            data: 'title_text',
                            name: 'title_text'
                        },
                        {
                            data: 'price',
                            name: 'price'
                        },
						{
							data: 'duration',
							name: 'duration'
						},
					{
						data: 'plan_id',
						name: 'plan_id'
					},
					{
						data: 'stripe_price_id',
						name: 'stripe_price_id'
					},
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            });
        </script>
    @endpush
@endsection
