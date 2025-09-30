<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trading History Reports</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: #0d0d16;
            color: #eaeaea;
        }
        .card {
            background: #1a1a2e;
            border: none;
        }
        .card-header {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            box-shadow: 0 4px 15px rgba(0,0,0,0.4);
        }
        .card-header h2, 
        .card-header p {
            color: #fff !important;
        }
        .card-body {
            background: #161623;
        }
        .card-body .card {
            background: #212135;
            border-left: 4px solid #6a11cb;
        }
        .card-body .card:hover {
            transform: translateY(-3px);
            transition: 0.3s ease-in-out;
            box-shadow: 0 6px 15px rgba(106, 17, 203, 0.4);
        }
        .pdf-icon {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            box-shadow: 0 0 15px rgba(106,17,203,0.7);
        }
        .btn-download {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            border: none;
            color: #fff;
        }
        .btn-download:hover {
            background: linear-gradient(135deg, #2575fc, #6a11cb);
            box-shadow: 0 0 12px rgba(106,17,203,0.8);
            color: #fff;
        }
        .text-muted {
            color: #b3b3cc !important;
        }
        .card-footer {
            background: transparent;
        }

         
    .card-title {
        color: #ffffff !important; /* Title white */
    }

    .card-body p {
        color: #e0e0e0 !important; /* Description muted/light */
    }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow-lg rounded-4">
                <div class="card-header text-center py-4 rounded-top-4">
                    <h2 class="mb-1">
                        <i class="fas fa-file-pdf text-warning me-2"></i>
                        Trading History Reports
                    </h2>
                    <p class="mb-0 small">Access and download trading history reports anytime</p>
                </div>

                <div class="card-body">
                    @forelse($tradingHistories as $history)
                        <div class="card mb-3 rounded-3 shadow-sm">
                            <div class="card-body d-flex align-items-start">
                                <!-- Icon -->
                                <div class="me-3">
                                    <div class="pdf-icon text-white d-flex align-items-center justify-content-center rounded-3" 
                                         style="width:60px;height:60px;">
                                        <i class="fas fa-file-pdf fa-lg"></i>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">{{ $history->title }}</h5>
                                    @if($history->description)
                                        <p class="text-muted small mb-2">{{ $history->description }}</p>
                                    @endif
                                    




                                    <!-- Meta Info -->
                                    <div class="d-flex flex-wrap gap-2 small text-muted mb-3">
                                        <span><i class="fas fa-weight me-1 text-secondary"></i>{{ $history->formatted_file_size }}</span>
                                        <span><i class="fas fa-calendar me-1 text-secondary"></i>{{ $history->upload_date->format('M d, Y') }}</span>
                                        <span><i class="fas fa-user me-1 text-secondary"></i>{{ $history->uploader->name ?? 'Admin' }}</span>
                                    </div>

                                    <!-- Action -->
                                    <a href="{{ route('user.trading-history.download', $history->id) }}" class="btn btn-download btn-sm px-3">
                                        <i class="fas fa-download me-1"></i> Download
                                    </a>
                                    
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-file-pdf fa-3x text-secondary mb-3"></i>
                            <h5 class="text-muted">No Trading History Reports</h5>
                            <p class="small text-muted">Please check back later, reports will be available soon.</p>
                        </div>
                    @endforelse

                    <div class="text-center mt-4">
                        <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-home me-1"></i> Back To Home
                        </a>
                    </div>
                </div>

                @if($tradingHistories->hasPages())
                    <div class="card-footer d-flex justify-content-center">
                        {{ $tradingHistories->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle JS (with Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
