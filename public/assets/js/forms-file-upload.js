/**
 * File Upload
 */

'use strict';

(function () {
  // previewTemplate: Updated Dropzone default previewTemplate
  // ! Don't change it unless you really know what you are doing
  const previewTemplate = `<div class="dz-preview dz-file-preview">
<div class="dz-details">
  <div class="dz-thumbnail">
    <img data-dz-thumbnail>
    <span class="dz-nopreview">No preview</span>
    <div class="dz-success-mark"></div>
    <div class="dz-error-mark"></div>
    <div class="dz-error-message"><span data-dz-errormessage></span></div>
    <div class="progress">
      <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
    </div>
  </div>
  <div class="dz-filename" data-dz-name></div>
  <div class="dz-size" data-dz-size></div>
</div>
</div>`;

  // ? Start your code from here

  // Basic Dropzone
  // --------------------------------------------------------------------
  const dropzoneBasic = document.querySelector('#dropzone-basic');
  if (dropzoneBasic) {
    const myDropzone = new Dropzone(dropzoneBasic, {
      url: "{{ isset($url) ? $url : url('/admin/courses/store') }}",
      previewTemplate: previewTemplate,
      previewsContainer: ".dropzone-previews",
      parallelUploads: 1,
      maxFilesize: 5,
      addRemoveLinks: true,
      acceptedFiles: '.jpg,.jpeg,.png,.gif',
      maxFiles: 1,
      uploadMultiple: false,
      autoProcessQueue: false,
      init: function () {
        const submitButton = document.querySelector('button[type="submit"]'); // Get the submit button
            const myDropzone = this;

            // Disable the default form submission
            submitButton.addEventListener('click', function (e) {
                e.preventDefault(); // Prevent default form submission
                e.stopPropagation();

                // Check if there are files in the Dropzone queue
                if (myDropzone.getQueuedFiles().length > 0) {
                    // Process the Dropzone queue (upload the file)
                    myDropzone.processQueue();
                } else {
                    // If no files are queued, submit the form normally
                    document.querySelector('#dropzone-basic').submit();
                }
            });

            // Once all files are uploaded successfully
            myDropzone.on("queuecomplete", function () {
                // Submit the form once the files are uploaded
                document.querySelector('#dropzone-basic').submit();
            });

            // Handle errors during file uploads
            myDropzone.on("error", function (file, response) {
                console.error('File upload error:', response);
            });
      }
    });
  }

  // Multiple Dropzone
  // --------------------------------------------------------------------
  const dropzoneMulti = document.querySelector('#dropzone-multi');
  if (dropzoneMulti) {
    const myDropzoneMulti = new Dropzone(dropzoneMulti, {
      previewTemplate: previewTemplate,
      parallelUploads: 1,
      maxFilesize: 5,
      addRemoveLinks: true
    });
  }
})();
