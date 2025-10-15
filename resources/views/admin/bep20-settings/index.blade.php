@extends('layouts.admin.app')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
<link href="{{ asset('public/assets/admin/css/') }}/styles.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Withdraw Settings Management</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    {{-- <th>
                                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                    </th> --}}
                                    <th>ID</th>
                                    <th>Min Withdraw</th>
                                    <th>Withdraw Fee</th>
                                    <th>Admin Deposit Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($settings as $setting)
                                    <tr>
                                        <td>{{ $setting->id }}</td>
                                        <td>${{ number_format($setting->min_withdraw, 2) }}</td>
                                        <td>${{ number_format($setting->withdraw_fee, 2) }}</td>
                                        <td>
                                            @if($setting->receiver_address)
                                                <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $setting->receiver_address }}">
                                                    <code class="small">{{ $setting->receiver_address }}</code>
                                                </span>
                                                <button class="btn btn-sm btn-outline-secondary ms-1" onclick="copyToClipboard('{{ $setting->receiver_address }}')" title="Copy Address">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            @else
                                                <span class="text-muted">Not configured</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.bep20-settings.show', $setting) }}" class="btn btn-info btn-sm" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.bep20-settings.edit', $setting) }}" class="btn btn-warning btn-sm" title="Edit Setting">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                               
                                               
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No BEP20 settings found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-trash-alt text-danger" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <h5>Are you sure you want to delete this BEP20 setting?</h5>
                    <p class="text-muted mb-3">This action cannot be undone.</p>
                    <div class="alert alert-warning">
                        <strong>Setting Details:</strong><br>
                        <span id="deleteSettingInfo"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i> Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('public/assets/admin/js/') }}/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script>
    // Initialize DataTable
    window.addEventListener('DOMContentLoaded', event => {
        const datatablesSimple = document.querySelector('.table');
        if (datatablesSimple) {
            new simpleDatatables.DataTable(datatablesSimple);
        }
    });

    // Delete confirmation variables
    let deleteFormId = null;

    // Function to show delete confirmation modal
    function confirmDelete(settingId, senderAddress) {
        deleteFormId = 'delete-form-' + settingId;
        
        // Update modal content with setting details
        document.getElementById('deleteSettingInfo').innerHTML = 
            '<strong>ID:</strong> ' + settingId + '<br>' +
            '<strong>Sender Address:</strong> ' + senderAddress.substring(0, 20) + '...';
        
        // Show the modal
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    // Handle confirm delete button click
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteFormId) {
            // Add loading state
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            this.disabled = true;
            
            // Submit the form
            document.getElementById(deleteFormId).submit();
        }
    });

    // Reset modal when closed
    document.getElementById('deleteModal').addEventListener('hidden.bs.modal', function() {
        deleteFormId = null;
        document.getElementById('confirmDeleteBtn').innerHTML = '<i class="fas fa-trash"></i> Yes, Delete';
        document.getElementById('confirmDeleteBtn').disabled = false;
    });

    // Copy to clipboard function
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Show success feedback
            const originalText = event.target.innerHTML;
            event.target.innerHTML = '<i class="fas fa-check"></i>';
            event.target.classList.remove('btn-outline-secondary');
            event.target.classList.add('btn-success');
            
            setTimeout(function() {
                event.target.innerHTML = originalText;
                event.target.classList.remove('btn-success');
                event.target.classList.add('btn-outline-secondary');
            }, 1000);
        }, function(err) {
            console.error('Could not copy text: ', err);
            alert('Failed to copy address');
        });
    }
</script>
@endsection