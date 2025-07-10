@extends('layouts/master', ['activePage' => 'roles.create', 'titlePage' => __('New Role')])



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
                <h5 class="card-header">Create New Role</h5>
                @isset($url)
                    <form  class="card-body" method="POST" action="{{$url}}" enctype="multipart/form-data" >
                @else
                  <form  class="card-body" method="POST" action="{{url('/roles')}}" enctype="multipart/form-data" >
                @endisset
                  @csrf

                  
                
                  <h6>1. Roles</h6>
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                      <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="john.doe" />
                      @error('name')
                        <span class="error invalid-feedback" style="margin-left:8px;display:block;">{{ $message }}</span>
                      @enderror
                    </div>
                    <input type="hidden" name="guard_name" value="web">


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
                        
                        @foreach($permission as $key => $value)
                            <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 mt-2 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="permission[]" value="{{ $value->id }}" class="name custom-control-input" id="checkboxPrimary{{ $value->id }}">
                                    <label for="checkboxPrimary{{$value->id}}" class="custom-control-label">{{ $value->name }} </label>
                                </div>
                            </div>
                        @endforeach
                  </div>    
                    
                  
                  <hr class="my-4 mx-n4" />
                  <div class="pt-4">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                    <button type="reset" class="btn btn-label-secondary">Cancel</button>
                  </div>
                </form>
              </div>

    </div>

@endsection
