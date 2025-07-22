@extends('layouts/master', ['activePage' => 'sliders.show', 'titlePage' => __('View Slider')])


@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-user-view.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
 <script src="{{ url('/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ url('/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ url('/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')}}"></script>
@endsection

@section('page-script')
<script src="{{ url('/assets/js/form-layouts.js') }}"></script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">View /</span> Slider Detail
</h4>
<div class="row">
  
<div class="card">
  <div class="card-body">
    <div class="row">
      <div class="mb-3 col-md-3 border-bottom pb-3">
        <div class="mb-0 text-wrap fw-semibold">Title</div>
      </div>
      <div class="mb-3 col-md-9 border-bottom pb-3">
        <div class="mb-0 text-muted">{{ $slider->name }}</div>
      </div>
      <div class="mb-3 col-md-3 border-bottom pb-3">
        <div class="mb-0 text-wrap fw-semibold">Slug</div>
      </div>
      <div class="mb-3 col-md-9 border-bottom pb-3">
        <div class="mb-0 text-muted">{{ $slider->slug }}</div>
      </div>
      <div class="mb-3 col-md-3 border-bottom pb-3">
        <div class="mb-0 text-wrap fw-semibold">Button</div>
      </div>
      <div class="mb-3 col-md-9 border-bottom pb-3">
        <div class="mb-0 text-muted"><a class="btn btn-primary" href="{{ $slider->button_url }}" target="_blank" rel="noopener noreferrer">{{ $slider->button_text }}</a></div>
      </div>
      
      <div class="mb-3 col-md-3 border-bottom pb-3">
        <div class="mb-0 text-wrap fw-semibold">Excerpt</div>
      </div>
      <div class="mb-3 col-md-9 border-bottom pb-3">
        <div class="mb-0 text-muted">{{ $slider->excerpt }}</div>
      </div>
      <div class="mb-3 col-md-3 border-bottom pb-3">
        <div class="mb-0 text-wrap fw-semibold">Status</div>
      </div>
      <div class="mb-3 col-md-9 border-bottom pb-3">
        <div class="mb-0 text-muted badge {{ $slider->status == 1 ? 'bg-label-success' : 'bg-label-danger' }}">{{ $slider->status == 1 ? 'Publish' : 'Draft' }}</div>
      </div>
      <div class="mb-3 col-md-3 border-bottom pb-3">
        <div class="mb-0 text-wrap fw-semibold">Created At</div>
      </div>
      <div class="mb-3 col-md-9 border-bottom pb-3">
        <div class="mb-0 text-muted">{{ $slider->created_at }}</div>
      </div>
      <div class="mb-3 col-md-3 border-bottom pb-3">
        <div class="mb-0 text-wrap fw-semibold">Created By</div>
      </div>
      <div class="mb-3 col-md-9 border-bottom pb-3">
        <div class="mb-0 text-muted">{{ $slider->createdBy->name }}</div>
      </div>
      <div class="mb-3 col-md-3 border-bottom pb-3">
        <div class="mb-0 text-wrap fw-semibold">Updated At</div>
      </div>
      <div class="mb-3 col-md-9 border-bottom pb-3">
        <div class="mb-0 text-muted">{{ $slider->updated_at }}</div>
      </div>
      <div class="mb-3 col-md-3 border-bottom pb-3">
        <div class="mb-0 text-wrap fw-semibold">Updated By</div>
      </div>
      <div class="mb-3 col-md-9 border-bottom pb-3">
        <div class="mb-0 text-muted">{{ $slider->updatedBy->name }}</div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-8">
          
            <div class="mb-0 text-wrap fw-semibold border-bottom pb-3">Description</div>
          
            <div class="mb-0 text-muted mt-2">{!! $slider->description !!}</div>
          
      </div>
      <div class="col-md-4">
          <div class="mb-0 text-wrap fw-semibold border-bottom pb-3">Image</div>
          
          <img src="{{ asset('storage/'.$slider->default_image->image_url) }}" alt="" class="mt-2 img-fluid img-thumbnail border-r-2">
      </div>
    </div>
    <div class="justify-content-end">
      <div class="col-sm-10">
          <a href="/admin/sliders" class="btn btn-outline-secondary">Back</a>
      </div>
    </div>
  </div>
</div>

 
</div>
</div>
<!-- Modal -->
<!-- /Modal -->
@endsection
