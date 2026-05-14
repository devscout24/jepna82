@extends('backend.app')

@section('title', 'Files')

@section('content')
    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between">
                <h4>Files</h4>
                <a href="{{ route('admin.file.create') }}" class="btn btn-secondary btn-sm">+ Upload File</a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Files</h4>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <table class="table table-bordered dt-responsive nowrap align-middle" id="fileTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>File</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#fileTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('admin.file.index') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'file', name: 'file' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });
    </script>
@endpush
