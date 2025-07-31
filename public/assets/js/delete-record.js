$(document).on("click", ".destroy_btn", function (e) {
    e.preventDefault();
    var form = $(this).closest("form"); // Get the parent form directly

    Swal.fire({
        title: "Are you sure?",
        text: "This record will be permanently deleted!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
        customClass: {
            confirmButton: "btn btn-primary me-3",
            cancelButton: "btn btn-label-secondary",
        },
        buttonsStyling: false,
        reverseButtons: true, // Optional: swaps button positions
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit(); // Submit the form directly
        }
    });
});
