@extends('layouts.admin.app')



@section('css')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
<link href="{{ asset('public/assets/admin/css/') }}/styles.css" rel="stylesheet" />
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
@endsection



@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fa fa-edit"></i> Edit Trading History
                    </h4>
                    <div>
                        <a href="{{ route('admin.trading-history.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                        <a href="{{ route('admin.trading-history.download', $tradingHistory) }}" class="btn btn-success btn-sm">
                            <i class="fa fa-download"></i> Download PDF
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.trading-history.update', $tradingHistory) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">
                                <strong>Document Title *</strong>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="title" name="title"
                                   value="{{ old('title', $tradingHistory->title) }}" required>
                            @error('title')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <strong>Description</strong>
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $tradingHistory->description) }}</textarea>
                            @error('description')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                <strong>Current File</strong>
                            </label>
                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-file-pdf-o fa-2x text-danger me-3"></i>
                                    <div>
                                        <strong>{{ $tradingHistory->file_name }}</strong><br>
                                        <small class="text-muted">
                                            Size: {{ $tradingHistory->formatted_file_size }} |
                                            Uploaded: {{ $tradingHistory->upload_date->format('M d, Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-text">
                                <i class="fa fa-info-circle text-info"></i>
                                To replace the PDF file, upload a new one in the create section.
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                       value="1" {{ old('is_active', $tradingHistory->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Active Status</strong>
                                </label>
                            </div>
                            <div class="form-text">
                                <i class="fa fa-info-circle text-info"></i>
                                Only active PDFs will be visible to users.
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.trading-history.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Details
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- File Information -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-info-circle text-info"></i> File Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>File Name:</strong></td>
                                    <td>{{ $tradingHistory->file_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>File Size:</strong></td>
                                    <td>{{ $tradingHistory->formatted_file_size }}</td>
                                </tr>
                                <tr>
                                    <td><strong>MIME Type:</strong></td>
                                    <td><code>{{ $tradingHistory->mime_type }}</code></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><strong>Upload Date:</strong></td>
                                    <td>{{ $tradingHistory->upload_date->format('F j, Y \a\t g:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Uploaded By:</strong></td>
                                    <td>{{ $tradingHistory->uploader->name ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($tradingHistory->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.form-control-lg {
    font-size: 1.1em;
    padding: 0.75rem 1rem;
}

.card-title {
    font-size: 1.4em;
}

.btn {
    font-size: 1em;
    padding: 0.5rem 1.5rem;
}

.form-text {
    font-size: 0.9em;
}

.table td {
    padding: 0.5rem;
    border: none;
}

.badge {
    font-size: 0.85em;
}

code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
}
</style>
@endsection
