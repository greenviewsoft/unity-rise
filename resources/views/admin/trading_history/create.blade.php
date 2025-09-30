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
                        <i class="fa fa-upload"></i> Upload Trading History PDF
                    </h4>
                    <a href="{{ route('admin.trading-history.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.trading-history.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">
                                <strong>Document Title *</strong>
                            </label>
                            <input type="text" class="form-control form-control-lg" id="title" name="title"
                                   value="{{ old('title') }}" placeholder="Enter document title (e.g., 'Weekly Trading Report')" required>
                            @error('title')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <strong>Description</strong>
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4"
                                      placeholder="Brief description of the trading history document...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="pdf_file" class="form-label">
                                <strong>PDF File *</strong>
                            </label>
                            <input type="file" class="form-control form-control-lg" id="pdf_file" name="pdf_file"
                                   accept=".pdf" required>
                            <div class="form-text">
                                <i class="fa fa-info-circle text-info"></i>
                                Maximum file size: 50MB. Only PDF files are allowed.
                            </div>
                            @error('pdf_file')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.trading-history.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-upload"></i> Upload PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Upload Guidelines -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fa fa-lightbulb-o text-warning"></i> Upload Guidelines
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fa fa-check text-success"></i> Recommended</h6>
                            <ul class="list-unstyled">
                                <li><i class="fa fa-file-pdf-o text-muted"></i> PDF format only</li>
                                <li><i class="fa fa-compress text-muted"></i> File size under 50MB</li>
                                <li><i class="fa fa-tag text-muted"></i> Descriptive title</li>
                                <li><i class="fa fa-calendar text-muted"></i> Include date in title</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fa fa-times text-danger"></i> Not Recommended</h6>
                            <ul class="list-unstyled">
                                <li><i class="fa fa-file-image-o text-muted"></i> Image files (JPG, PNG)</li>
                                <li><i class="fa fa-file-word-o text-muted"></i> Word documents (DOC, DOCX)</li>
                                <li><i class="fa fa-file-excel-o text-muted"></i> Excel files (XLS, XLSX)</li>
                                <li><i class="fa fa-file-archive-o text-muted"></i> Compressed files (ZIP, RAR)</li>
                            </ul>
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

#pdf_file {
    border: 2px dashed #dee2e6;
    border-radius: 0.5rem;
    padding: 2rem;
    text-align: center;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

#pdf_file:hover {
    border-color: #007bff;
    background-color: #e3f2fd;
}

.upload-guidelines {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-left: 4px solid #007bff;
}
</style>
@endsection
