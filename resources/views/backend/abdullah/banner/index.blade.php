@extends('backend.app')

@section('content')
	<!-- start page title -->
	<div class="row">
		<div class="col-12">
			<div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
				<h4 class="mb-sm-0">Banners</h4>

				<div class="page-title-right">
					<ol class="breadcrumb m-0">
						<li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
						<li class="breadcrumb-item active">Banner List</li>
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
					<h4 class="card-title mb-0 flex-grow-1">Banner List</h4>
					<a href="{{ route('admin.banner.create') }}" class="btn btn-sm btn-success">Add Banner</a>
				</div>

				<div class="card-body">
					@if (session('success'))
						<div class="alert alert-success">{{ session('success') }}</div>
					@endif

					<table class="table table-bordered dt-responsive nowrap align-middle" id="bannerTable">
						<thead>
							<tr>
								<th>#</th>
								<th>Title</th>
								<th>Description</th>
								{{-- <th>Button Text</th> --}}
								<th>Icons</th>
								<th>Icon Titles</th>
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
			$('#bannerTable').DataTable({
				processing: true,
				serverSide: true,
				responsive: true,
				ajax: "{{ route('admin.banner.index') }}",
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
					// {
					// 	data: 'button_text',
					// 	name: 'button_text',
					// 	orderable: false,
					// 	searchable: false
					// },
					{
						data: 'icons',
						name: 'icons',
						orderable: false,
						searchable: false
					},
					{
						data: 'icon_title',
						name: 'icon_title',
						orderable: false,
						searchable: false
						},
						{
							data: 'action',
							name: 'action',
							orderable: false,
							searchable: false
					},
				]
			});
		});
	</script>
@endpush

