@extends('layouts/master', ['activePage' => 'sliders.edit', 'titlePage' => __('Edit Slider')])


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

 
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.2.0/ckeditor5.css">
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
<!-- Page JS -->
    <script src="{{ url('/assets/js/form-layouts.js') }}"></script>
   
<script type="importmap">
			{
				"imports": {
					"ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.2.0/ckeditor5.js",
					"ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.2.0/"
				}
			}
</script>
<script type="module">
import {
				ClassicEditor,
				Essentials,
				Paragraph,
				Bold,
				Italic,
				Font
			} from 'ckeditor5';
    ClassicEditor
        .create(document.querySelector('#editor'), {
          plugins: [ Essentials, Paragraph, Bold, Italic, Font ],
            toolbar: ['undo', 'redo', '|', 'bold', 'italic', '|', 'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor'],
        })
        
        .then(editor => {
          editor.ui.view.editable.element.style.height = '300px';
          
            window.editor = editor;
        })
        .catch(error => {
            console.error(error);
        });
</script>

    
@endsection

@section('content')
 
    <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="py-3 mb-4">
                <span class="text-muted fw-light">Slider /</span> Update
            </h4>
        
           <div class="card mb-4">
                @isset($url)
                    <form  class="card-body" method="POST" action="{{$url}}" enctype="multipart/form-data" >
                @else
                  <form  class="card-body" method="POST" action="{{url('/admin/sliders/'.$slider->id.'')}}" enctype="multipart/form-data" >
                @endisset
                  @csrf
                  @method('PUT')
                  
                
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                      <input type="text" id="name" name="name" value="{{ old('name', $slider->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="Enter Slider Name" />
                      @error('name')
                        <span class="error invalid-feedback" style="margin-left:8px;display:block;">{{ $message }}</span>
                      @enderror
                    </div>
                    <div class="col-md-6">
                      <label class="form-label" for="subname">Sub Name </label>
                      <input type="text" id="subname" name="subname" value="{{ old('subname', $slider->subname) }}" class="form-control @error('subname') is-invalid @enderror" placeholder="Enter Slider Sub Name" />
                      @error('subname')
                        <span class="error invalid-feedback" style="margin-left:8px;display:block;">{{ $message }}</span>
                      @enderror
                    </div>
                    <div class="col-md-6">
                      <label class="form-label" for="button_text">Button Label </label>
                      <input type="text" id="button_text" name="button_text" value="{{ old('button_text', $slider->button_text) }}" class="form-control @error('button_text') is-invalid @enderror" placeholder="Enter Button Label" />
                      @error('button_text')
                        <span class="error invalid-feedback" style="margin-left:8px;display:block;">{{ $message }}</span>
                      @enderror
                    </div>
                    <div class="col-md-6">
                      <label class="form-label" for="button_url">Button URL </label>
                      <input type="text" id="button_url" name="button_url" value="{{ old('button_url', $slider->button_url) }}" class="form-control @error('button_url') is-invalid @enderror" placeholder="Enter button url" />
                      @error('button_url')
                        <span class="error invalid-feedback" style="margin-left:8px;display:block;">{{ $message }}</span>
                      @enderror
                    </div>
                    <div>
                      <label class="form-label" for="editor">Description</label>
                      <textarea id="editor" name="description" style="height: 300px; width: 100%;" class=" form-control" placeholder="Enter Description">{{ old('description', $slider->description) }}</textarea>
                    </div>
                    
                  </div>
                 
                  <hr class="my-4 mx-n4" />
                  <div class="row g-3">
                    
                    <div class="col-md-6">
                          <label class="form-label" for="basic-default-upload-file">Slider pic</label>
                          <input type="file" name="default_image" class="form-control @error('default_image') is-invalid @enderror" id="basic-default-upload-file">
                          @error('default_image')
                          <span class="error invalid-feedback" style="margin-left:8px;display:block;">{{ $message }}</span>
                        @enderror
                        <div class="mt-3">
                            <img id="imagePreview" 
                                src="{{ isset($slider) && $slider->default_image ? asset('storage/' . $slider->default_image->image_url)  : asset('assets/img/backgrounds/5.jpg') }}" 
                                alt="Preview Image" 
                                style="max-width: 100%; height: auto; border-radius: 8px;">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                      <label class="form-label" for="excerpt">Excerpt</label>
                      <textarea id="excerpt" name="excerpt" class="form-control @error('excerpt') is-invalid @enderror" placeholder="enter excerpt">{{ old('excerpt', $slider->excerpt) }}</textarea>
                      @error('excerpt')
                          <span class="error invalid-feedback" style="margin-left:8px;display:block;">{{ $message }}</span>
                        @enderror
                    </div>
                  </div>
                  
                  <div class="pt-4">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
                    <button type="reset" class="btn btn-label-secondary">Cancel</button>
                  </div>
                </form>
              </div>

    </div>

@endsection
