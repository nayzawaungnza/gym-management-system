
$(document).on("change", ".guard_name", function () { 
    var guard_name = $(this).val(); 
    var url = $(this).data("attr-url") + "/" + guard_name; 
    
    $.ajax({
        url: url,
        type: "GET",
        success: function (data) {
            if (data && data) {
                var permissionHtml = '';

                $.each(data, function(index, permission) {
                    permissionHtml += `
                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 mt-2 mb-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="permission[]" value="${permission.id}" class="name ${permission.guard_name} custom-control-input" id="checkboxPrimary${permission.id}">
                                <label for="checkboxPrimary${permission.id}" class="custom-control-label">${permission.name}</label>
                            </div>
                        </div>
                    `;
                });

                $(".permission_list").html(permissionHtml); 
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching permissions:', error);
        }
    });
});


var roleTable = $(".role-data-table").DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: $("#table_url").attr("data-table-url"),
        error: function (xhr, error, thrown) {
            if (xhr.status === 419) {
                window.location.reload();
                return;
            }
        },
    },
    columns: [
        { data: "name", name: "name" },
        {
            data: "action",
            name: "action",
            orderable: false,
            searchable: false,
            class: "td-actions",
        },
    ],
});

//  Select all function for role create and edit
function toggle(source) {
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i] != source) checkboxes[i].checked = source.checked;
    }
}

