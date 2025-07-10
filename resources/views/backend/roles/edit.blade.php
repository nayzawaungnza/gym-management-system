@extends('layouts/master', ['activePage' => 'roles.edit', 'titlePage' => __('Edit Role')])

@section('title', 'Academy Dashboard - Apps')

@section('vendor-style')
<!-- Vendors CSS -->
   <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ url('/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ url('/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ url('/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ url('/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ url('/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ url('/assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />

   
@endsection
@section('page-style')

 
@endsection

@section('vendor-script')
<!-- Vendors JS -->
    <script src="{{ url('/assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ url('/assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ url('/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ url('/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ url('/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ url('/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ url('/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ url('/assets/vendor/libs/tagify/tagify.js') }}"></script>
    

@endsection

@section('page-script')
 <script src="{{ url('/assets/js/roles.js') }}"></script>
<!-- Page JS -->
    <script src="{{ url('/assets/js/form-layouts.js') }}"></script>
   

    
@endsection

@section('content')
 
    <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-4">
                <span class="text-muted fw-light">Roles/</span> Create
            </h4>
        
           <div class="card mb-4">
                <h5 class="card-header">Edit Role</h5>
                @isset($url)
                    <form  class="card-body" method="POST" action="{{$url}}" enctype="multipart/form-data" >
                @else
                  <form  class="card-body" method="POST" action="{{url('/admin/roles/'.$role->id)}}" enctype="multipart/form-data" >
                @endisset
                  @csrf
                  @method('PUT')

                  
                
                  <h6>1. Roles</h6>
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                      <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="john.doe" />
                      @error('name')
                        <span class="error invalid-feedback" style="margin-left:8px;display:block;">{{ $message }}</span>
                      @enderror
                    </div>
                    {{-- <div class="col-md-6">
                        <label class="form-label" for="guard-name">Guard <span class="text-danger">*</span></label>
                        <select id="guard-name" name="guard_name"
                            class="guard_name form-select @error('guard_name') is-invalid @enderror" data-attr-url="{{ url('/admin/permissions') }}">
                            <option value="" disabled selected>Please Select Guard</option>
                            <option value="admin" {{ old('guard_name', $role->guard_name) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="teacher" {{ old('guard_name', $role->guard_name) == 'teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="student" {{ old('guard_name', $role->guard_name) == 'student' ? 'selected' : '' }}>Student</option>
                            <option value="web" {{ old('guard_name', $role->guard_name) == 'web' ? 'selected' : '' }}>User</option>
                        </select>
                        @error('guard_name')
                            <span class="error invalid-feedback" style="margin-left:8px;display:block;">{{ $message }}</span>
                        @enderror
                    </div> --}}
                  </div>
                  <hr class="my-4 mx-n4" />
                  <h6>2. Permissions</h6>
                  <div class="row g-3">
                        <label for="name" class="form-label">Permission <span class="text-danger">*</span></label>
                        <div class="icheck-primary custom-control custom-checkbox">
                            <input type="checkbox" id="select-all" onclick="toggle(this);" class="custom-control-input">
                            <label for="select-all" class="custom-control-label">Select All</label>
                        </div>
                  </div>
                  <div class="row permission_list">
                        @foreach($permission as $value)
                            <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 mt-2 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="permission[]" value="{{ $value->id }}" class="name custom-control-input" id="checkboxPrimary{{ $value->id }}" 
                                    @if(in_array($value->id, $rolePermissions)) checked @endif>
                                    <label for="checkboxPrimary{{ $value->id }}" class="custom-control-label">
                                        {{ $value->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach

                            
                    
                  </div>
                  <hr class="my-4 mx-n4" />
                  <div class="pt-4">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                    <a href="{{ url('/admin/roles') }}" class="btn btn-label-secondary">Cancel</a>
                  </div>
                </form>
              </div>

    </div>

@endsection
