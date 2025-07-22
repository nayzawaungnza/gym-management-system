@extends('layouts/master', ['activePage' => 'sliders', 'titlePage' => __('Sliders')])


@section('vendor-style')
<!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
  <link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ url('/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endsection

@section('vendor-script')
 <!-- Vendors JS -->
    <script src="{{ url('/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ url('/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ url('/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ url('/assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ url('/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
@endsection

@section('page-script')
<script src="{{ url('/js/delete-record.js') }}"></script>
<script src="{{ url('/assets/js/tables-datatables-advanced.js') }}"></script>
@endsection

@section('content')
 
<div class="container-xxl flex-grow-1 container-p-y">
             
              <!-- Users List Table -->
              <!-- Ajax Sourced Server-side -->
              <div class="card">
              
                <h5 class="card-header">
                sliders List
                    <div class="demo-inline-spacing">
                        <a href="{{route('sliders.create')}}" class="btn btn-secondary add-new btn-primary">
                            <i class="ti ti-plus me-0 me-sm-1 ti-xs"></i>
                            <span class="d-none d-sm-inline-block">Add New Slide</span></a>
                    </div>
                </h5>
                
                <div id="table_url" data-table-url="{{url('admin/sliders')}}"></div>
                <div class="card-datatable table-responsive text-nowrap">
                  <table class="slider-datatables-ajax table">
                    <thead>
                      <tr>
                         <th>Name</th>
                        <th>Slug</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Created By</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
              <!--/ Ajax Sourced Server-side -->
              
            </div>
 

@endsection
