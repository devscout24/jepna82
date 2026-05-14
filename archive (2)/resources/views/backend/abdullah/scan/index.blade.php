@extends('backend.app')

@section('content')
	<!-- start page title -->
	<div class="row">
		<div class="col-12">
			<div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
				<h4 class="mb-sm-0">Scans</h4>

				<div class="page-title-right">
					<ol class="breadcrumb m-0">
						<li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
						<li class="breadcrumb-item active">Scan List</li>
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
					<h4 class="card-title mb-0 flex-grow-1">Scan List</h4>
					<a href="{{ route('admin.scan.create') }}" class="btn btn-sm btn-success">Add Scan</a>
				</div>

				<div class="card-body">
					@if (session('success'))
						<div class="alert alert-success">{{ session('success') }}</div>
					@endif

					<table class="table table-bordered dt-responsive nowrap align-middle" id="scanTable">
						<thead>
							<tr>
								<th>#</th>
								<th>Title</th>
								<th>Description</th>
								<th>Price</th>
								<th>Items</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script>
		$(function() {
			$('#scanTable').DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				ajax: "{{ route('admin.scan.index') }}",
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
						data: 'description',
						name: 'description',
						orderable: false,
						searchable: false
					},
					{
						data: 'price',
						name: 'price'
					},
					{
						data: 'items',
						name: 'items',
						orderable: false,
						searchable: false
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