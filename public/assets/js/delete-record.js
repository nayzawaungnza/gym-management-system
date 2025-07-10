//  record delete
$(document).on("click", ".destroy_btn", function () {
    Swal.fire({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this imaginary file!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        customClass: {
          confirmButton: 'btn btn-primary me-3',
          cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
    }).then((willDelete) => {
        if (willDelete) {
            var form_id = $(this).attr("data-origin");
            $("#" + form_id).submit();
        } else {
            // swal("Your imaginary file is safe!");
        }
    });
});