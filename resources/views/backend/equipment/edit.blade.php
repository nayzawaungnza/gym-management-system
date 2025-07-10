@extends('layouts.backend')

@section('title', 'Edit Equipment')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Equipment</h1>
            <p class="mb-0 text-muted">Update equipment information</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('backend.equipment.show', $equipment) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Details
            </a>
            <a href="{{ route('backend.equipment.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Equipment
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Equipment Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('backend.equipment.update', $equipment) }}" method="POST" enctype="multipart/form-data" id="equipmentForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Equipment Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $equipment->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('category') is-invalid @enderror" 
                                       id="category" name="category" value="{{ old('category', $equipment->category) }}" 
                                       list="categoryList" required>
                                <datalist id="categoryList">
                                    @foreach($categories as $category)
                                    <option value="{{ $category }}">
                                    @endforeach
                                    <option value="Cardio">
                                    <option value="Strength">
                                    <option value="Free Weights">
                                    <option value="Functional">
                                    <option value="Accessories">
                                </datalist>
                                @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="brand" class="form-label">Brand</label>
                                <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                                       id="brand" name="brand" value="{{ old('brand', $equipment->brand) }}">
                                @error('brand')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="model" class="form-label">Model</label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                       id="model" name="model" value="{{ old('model', $equipment->model) }}">
                                @error('model')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="serial_number" class="form-label">Serial Number</label>
                                <input type="text" class="form-control @error('serial_number') is-invalid @enderror" 
                                       id="serial_number" name="serial_number" value="{{ old('serial_number', $equipment->serial_number) }}">
                                @error('serial_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="operational" {{ old('status', $equipment->status) == 'operational' ? 'selected' : '' }}>Operational</option>
                                    <option value="maintenance" {{ old('status', $equipment->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="out_of_order" {{ old('status', $equipment->status) == 'out_of_order' ? 'selected' : '' }}>Out of Order</option>
                                    <option value="retired" {{ old('status', $equipment->status) == 'retired' ? 'selected' : '' }}>Retired</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                       id="location" name="location" value="{{ old('location', $equipment->location) }}" 
                                       placeholder="e.g., Main Floor, Cardio Area">
                                @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $equipment->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Purchase Information -->
                        <h5 class="mb-3 mt-4">Purchase Information</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="purchase_date" class="form-label">Purchase Date</label>
                                <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" 
                                       id="purchase_date" name="purchase_date" value="{{ old('purchase_date', $equipment->purchase_date?->format('Y-m-d')) }}">
                                @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="purchase_price" class="form-label">Purchase Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('purchase_price') is-invalid @enderror" 
                                           id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $equipment->purchase_price) }}" 
                                           step="0.01" min="0">
                                </div>
                                @error('purchase_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="warranty_expiry_date" class="form-label">Warranty Expiry</label>
                                <input type="date" class="form-control @error('warranty_expiry_date') is-invalid @enderror" 
                                       id="warranty_expiry_date" name="warranty_expiry_date" value="{{ old('warranty_expiry_date', $equipment->warranty_expiry_date?->format('Y-m-d')) }}">
                                @error('warranty_expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Maintenance Information -->
                        <h5 class="mb-3 mt-4">Maintenance Information</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="maintenance_interval_days" class="form-label">Maintenance Interval (Days)</label>
                                <input type="number" class="form-control @error('maintenance_interval_days') is-invalid @enderror" 
                                       id="maintenance_interval_days" name="maintenance_interval_days" 
                                       value="{{ old('maintenance_interval_days', $equipment->maintenance_interval_days) }}" min="1" max="365">
                                @error('maintenance_interval_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="last_maintenance_date" class="form-label">Last Maintenance Date</label>
                                <input type="date" class="form-control @error('last_maintenance_date') is-invalid @enderror" 
                                       id="last_maintenance_date" name="last_maintenance_date" value="{{ old('last_maintenance_date', $equipment->last_maintenance_date?->format('Y-m-d')) }}">
                                @error('last_maintenance_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="next_maintenance_date" class="form-label">Next Maintenance Date</label>
                                <input type="date" class="form-control" id="next_maintenance_date" 
                                       value="{{ $equipment->next_maintenance_date?->format('Y-m-d') }}" readonly>
                                <small class="text-muted">Calculated automatically based on interval</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="maintenance_notes" class="form-label">Maintenance Notes</label>
                            <textarea class="form-control @error('maintenance_notes') is-invalid @enderror" 
                                      id="maintenance_notes" name="maintenance_notes" rows="2">{{ old('maintenance_notes', $equipment->maintenance_notes) }}</textarea>
                            @error('maintenance_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Technical Specifications -->
                        <h5 class="mb-3 mt-4">Technical Specifications</h5>
                        <div class="mb-3">
                            <label for="specifications" class="form-label">Specifications (JSON)</label>
                            <textarea class="form-control @error('specifications') is-invalid @enderror" 
                                      id="specifications" name="specifications" rows="4" 
                                      placeholder='{"weight": "100kg", "dimensions": "2m x 1m x 1.5m", "power": "220V"}'>{{ old('specifications', $equipment->specifications ? json_encode($equipment->specifications, JSON_PRETTY_PRINT) : '') }}</textarea>
                            <small class="text-muted">Enter specifications in JSON format</small>
                            @error('specifications')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Equipment Image -->
                        <h5 class="mb-3 mt-4">Equipment Image</h5>
                        @if($equipment->image)
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div>
                                <img src="{{ Storage::url($equipment->image) }}" alt="{{ $equipment->name }}" 
                                     class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        </div>
                        @endif
                        <div class="mb-3">
                            <label for="image" class="form-label">{{ $equipment->image ? 'Replace Image' : 'Upload Image' }}</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            <small class="text-muted">Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB</small>
                            @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image Preview -->
                        <div id="imagePreview" style="display: none;">
                            <img id="previewImg" src="/placeholder.svg" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('backend.equipment.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Equipment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Current Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Current Status</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Status:</strong>
                        @php
                            $statusColors = [
                                'operational' => 'success',
                                'maintenance' => 'warning',
                                'out_of_order' => 'danger',
                                'retired' => 'secondary'
                            ];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$equipment->status] ?? 'secondary' }}">
                            {{ ucfirst(str_replace('_', ' ', $equipment->status)) }}
                        </span>
                    </div>
                    
                    @if($equipment->next_maintenance_date)
                    <div class="mb-3">
                        <strong>Next Maintenance:</strong><br>
                        {{ $equipment->next_maintenance_date->format('M d, Y') }}
                        @if($equipment->next_maintenance_date < now())
                            <span class="badge badge-danger ml-2">Overdue</span>
                        @elseif($equipment->next_maintenance_date <= now()->addDays(7))
                            <span class="badge badge-warning ml-2">Due Soon</span>
                        @endif
                    </div>
                    @endif

                    @if($equipment->warranty_expiry_date)
                    <div class="mb-3">
                        <strong>Warranty:</strong><br>
                        Expires {{ $equipment->warranty_expiry_date->format('M d, Y') }}
                        @if($equipment->warranty_expiry_date < now())
                            <span class="badge badge-danger ml-2">Expired</span>
                        @elseif($equipment->warranty_expiry_date <= now()->addDays(30))
                            <span class="badge badge-warning ml-2">Expiring Soon</span>
                        @endif
                    </div>
                    @endif

                    <div class="mb-3">
                        <strong>Last Updated:</strong><br>
                        {{ $equipment->updated_at->format('M d, Y g:i A') }}
                    </div>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Help & Tips</h6>
                </div>
                <div class="card-body">
                    <h6>Equipment Categories</h6>
                    <ul class="small">
                        <li><strong>Cardio:</strong> Treadmills, bikes, ellipticals</li>
                        <li><strong>Strength:</strong> Weight machines, cable systems</li>
                        <li><strong>Free Weights:</strong> Dumbbells, barbells, plates</li>
                        <li><strong>Functional:</strong> Kettlebells, resistance bands</li>
                        <li><strong>Accessories:</strong> Mats, balls, foam rollers</li>
                    </ul>

                    <h6 class="mt-3">Status Definitions</h6>
                    <ul class="small">
                        <li><strong>Operational:</strong> Ready for use</li>
                        <li><strong>Maintenance:</strong> Under maintenance</li>
                        <li><strong>Out of Order:</strong> Broken, needs repair</li>
                        <li><strong>Retired:</strong> No longer in use</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Image preview
    $('#image').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#imagePreview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').hide();
        }
    });

    // Calculate next maintenance date
    function calculateNextMaintenance() {
        const lastDate = $('#last_maintenance_date').val();
        const interval = $('#maintenance_interval_days').val();
        
        if (lastDate && interval) {
            const last = new Date(lastDate);
            const next = new Date(last.getTime() + (interval * 24 * 60 * 60 * 1000));
            $('#next_maintenance_date').val(next.toISOString().split('T')[0]);
        } else if (interval) {
            const today = new Date();
            const next = new Date(today.getTime() + (interval * 24 * 60 * 60 * 1000));
            $('#next_maintenance_date').val(next.toISOString().split('T')[0]);
        } else {
            $('#next_maintenance_date').val('');
        }
    }

    $('#last_maintenance_date, #maintenance_interval_days').change(calculateNextMaintenance);

    // JSON validation for specifications
    $('#specifications').blur(function() {
        const value = $(this).val().trim();
        if (value) {
            try {
                JSON.parse(value);
                $(this).removeClass('is-invalid').addClass('is-valid');
            } catch (e) {
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        } else {
            $(this).removeClass('is-invalid is-valid');
        }
    });

    // Form validation
    $('#equipmentForm').submit(function(e) {
        const specs = $('#specifications').val().trim();
        if (specs) {
            try {
                JSON.parse(specs);
            } catch (e) {
                e.preventDefault();
                alert('Please enter valid JSON format for specifications.');
                $('#specifications').focus();
                return false;
            }
        }
    });
});
</script>
@endpush
