/**
 * DataTables Advanced (jquery)
 */

'use strict';

$(function () {
  var dt_ajax_table = $('.datatables-ajax'),
  dt_user_ajax_table =  $('.user-datatables-ajax'),
  dt_subscriber_ajax_table =  $('.subscriber-datatables-ajax'),
  dt_post_category_ajax_table =  $('.post-category-data-table'),
  dt_post_ajax_table =  $('.post-datatables-ajax'),
  dt_page_ajax_table =  $('.page-datatables-ajax'),
  dt_service_ajax_table =  $('.service-datatables-ajax'),
  dt_project_ajax_table =  $('.project-datatables-ajax'),
  dt_slider_ajax_table =  $('.slider-datatables-ajax'),
  dt_partner_ajax_table =  $('.partner-datatables-ajax'),
  dt_client_ajax_table =  $('.client-datatables-ajax'),
  // dt_enrollments_ajax_table =  $('.enrollments-datatables-ajax'),
  // dt_payments_ajax_table =  $('.payments-datatables-ajax'),
  // dt_transactions_ajax_table =  $('.transactions-data-table'),
  // dt_orders_ajax_table =  $('.orders-data-table'),
  // dt_assignments_ajax_table = $('.assignments-datatables-ajax'),
  // dt_question_ajax_table =  $('.question-datatables-ajax'),
    dt_filter_table = $('.dt-column-search'),
    dt_adv_filter_table = $('.dt-advanced-search'),
    dt_responsive_table = $('.dt-responsive'),
    startDateEle = $('.start_date'),
    endDateEle = $('.end_date');

  // Advanced Search Functions Starts
  // --------------------------------------------------------------------

  // Datepicker for advanced filter
  var rangePickr = $('.flatpickr-range'),
    dateFormat = 'MM/DD/YYYY';

  if (rangePickr.length) {
    rangePickr.flatpickr({
      mode: 'range',
      dateFormat: 'm/d/Y',
      orientation: isRtl ? 'auto right' : 'auto left',
      locale: {
        format: dateFormat
      },
      onClose: function (selectedDates, dateStr, instance) {
        var startDate = '',
          endDate = new Date();
        if (selectedDates[0] != undefined) {
          startDate = moment(selectedDates[0]).format('MM/DD/YYYY');
          startDateEle.val(startDate);
        }
        if (selectedDates[1] != undefined) {
          endDate = moment(selectedDates[1]).format('MM/DD/YYYY');
          endDateEle.val(endDate);
        }
        $(rangePickr).trigger('change').trigger('keyup');
      }
    });
  }

  // Filter column wise function
  function filterColumn(i, val) {
    if (i == 5) {
      var startDate = startDateEle.val(),
        endDate = endDateEle.val();
      if (startDate !== '' && endDate !== '') {
        $.fn.dataTableExt.afnFiltering.length = 0; // Reset datatable filter
        dt_adv_filter_table.dataTable().fnDraw(); // Draw table after filter
        filterByDate(i, startDate, endDate); // We call our filter function
      }
      dt_adv_filter_table.dataTable().fnDraw();
    } else {
      dt_adv_filter_table.DataTable().column(i).search(val, false, true).draw();
    }
  }

  // Advance filter function
  // We pass the column location, the start date, and the end date
  $.fn.dataTableExt.afnFiltering.length = 0;
  var filterByDate = function (column, startDate, endDate) {
    // Custom filter syntax requires pushing the new filter to the global filter array
    $.fn.dataTableExt.afnFiltering.push(function (oSettings, aData, iDataIndex) {
      var rowDate = normalizeDate(aData[column]),
        start = normalizeDate(startDate),
        end = normalizeDate(endDate);

      // If our date from the row is between the start and end
      if (start <= rowDate && rowDate <= end) {
        return true;
      } else if (rowDate >= start && end === '' && start !== '') {
        return true;
      } else if (rowDate <= end && start === '' && end !== '') {
        return true;
      } else {
        return false;
      }
    });
  };

  // converts date strings to a Date object, then normalized into a YYYYMMMDD format (ex: 20131220). Makes comparing dates easier. ex: 20131220 > 20121220
  var normalizeDate = function (dateString) {
    var date = new Date(dateString);
    var normalized =
      date.getFullYear() + '' + ('0' + (date.getMonth() + 1)).slice(-2) + '' + ('0' + date.getDate()).slice(-2);
    return normalized;
  };
  // Advanced Search Functions Ends

  // Ajax Sourced Server-side
  // --------------------------------------------------------------------

  if (dt_ajax_table.length) {
    var dt_ajax = dt_ajax_table.dataTable({
      processing: true,
      ajax: assetsPath + 'json/ajax.php',
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
    });
  }
  //User

  if(dt_user_ajax_table.length)
    {
      var dt_ajax = dt_user_ajax_table.dataTable({
        processing: true,
    serverSide: true,
    aaSorting: [],
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
        { data: "email", name: "email" },
        {data: "role", name: "role"},
        { data: "mobile", name: "mobile" },
        { data: "default_image", 
          name: "default_image",
          orderable: false, 
          searchable: false 
        },
        { data: "is_active", name: "is_active" },
        { data: "created_at", name: "created_at" },
        {
            data: "action",
            name: "action",
            orderable: false,
            searchable: false,
            class: "td-actions",
        },
    ],
      });
    }

    if(dt_subscriber_ajax_table.length)
      {
        var dt_ajax = dt_subscriber_ajax_table.dataTable({
          processing: true,
      serverSide: true,
      aaSorting: [],
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
          { data: "email", name: "email" },
          {data: "role", name: "role"},
          { data: "mobile", name: "mobile" },
          { data: "default_image", 
            name: "default_image",
            orderable: false, 
            searchable: false 
          },
          { data: "is_active", name: "is_active" },
          { data: "created_at", name: "created_at" },
          {
              data: "action",
              name: "action",
              orderable: false,
              searchable: false,
              class: "td-actions",
          },
      ],
        });
      }

  //   if (dt_transactions_ajax_table.length) {
  //     var dt_ajax = dt_transactions_ajax_table.DataTable({
  //         processing: true,
  //         serverSide: true,
  //         ajax: {
  //             url: $("#table_url").data("table-url"),
  //             error: function (xhr) {
  //                 if (xhr.status === 419) window.location.reload();
  //             },
  //         },
  //         columns: [
  //             { data: "transaction_ref", name: "transaction_ref" },
  //             { data: "student", name: "student.name" },
  //             { data: "payment_method", name: "payment_method.display_name" },
  //             { data: "order_status", name: "order_status" },
  //             { data: "sub_total", name: "sub_total" },
  //             { data: "transaction_date", name: "transaction_date" },
  //             {
  //                 data: "action",
  //                 name: "action",
  //                 orderable: false,
  //                 searchable: false,
  //                 class: "td-actions",
  //             },
  //         ],
  //     });
  // }

//   if (dt_orders_ajax_table.length) {
//     var dt_ajax = dt_orders_ajax_table.DataTable({
//         processing: true,
//         serverSide: true,
//         ajax: {
//             url: $("#table_url").data("table-url"),
//             error: function (xhr) {
//                 if (xhr.status === 419) window.location.reload();
//             },
//         },
//         columns: [
//             { data: "student", name: "student.name" },
//             {data: "courses_name", name: "courses_name"},
//             { data: "payment_method", name: "payment_method.display_name" },
//             { data: "order_status", name: "order_status" },
//             { data: "sub_total", name: "sub_total" },
//             { data: "created_at", name: "created_at" },
//             {
//                 data: "action",
//                 name: "action",
//                 orderable: false,
//                 searchable: false,
//                 class: "td-actions",
//             },
//         ],
//     });
// }
  

  
  if(dt_post_category_ajax_table.length)
    {
      var dt_ajax = dt_post_category_ajax_table.dataTable({
        processing: true,
    serverSide: true,
    aaSorting: [],
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
        //{ data: "slug", name: "slug" },
        { data: "status", name: "status" },
        { data: "created_at", name: "created_at" },
        { data: "created_by", name: "created_by" }, 
        {
            data: "action",
            name: "action",
            orderable: false,
            searchable: false,
            class: "td-actions",
        },
    ],
      });
    }

    //Student Table
    if(dt_post_ajax_table.length)
      {
        console.log($("#table_url").attr("data-table-url"));
        var dt_ajax = dt_post_ajax_table.dataTable({
          processing: true,
      serverSide: true,
      aaSorting: [],
      ajax: {
          url: $("#table_url").attr("data-table-url"),
          error: function (xhr, error, thrown) {
            console.error("Error occurred: ", error);
            console.log("Thrown error: ", thrown);
            console.log("XHR: ", xhr);
            
            if (xhr.status === 419) {
                window.location.reload();
                return;
            }
            
            if (xhr.status === 500) {
                alert('Internal Server Error (500). Please check the server logs.');
            }
        },
        dataSrc: function (json) {
            console.log("Server Response: ", json);
            return json.data; // Ensure 'data' contains the list of students
        }
      },
      columns: [
          { data: "name", name: "name", orderable: true, searchable: true },
          { data: "slug", name: "slug" },
          { data: "default_image", name: "default_image" },
          { data: "status", name: "status" },
          { data: "created_at", name: "created_at", orderable: true, searchable: true },
          { data: "created_by", name: "created_by", orderable: true, searchable: true },
          {
              data: "action",
              name: "action",
              orderable: false,
              searchable: false,
              class: "td-actions",
          },
      ],
        });
      }

      if(dt_page_ajax_table.length)
        {
          console.log($("#table_url").attr("data-table-url"));
          var dt_ajax = dt_page_ajax_table.dataTable({
            processing: true,
        serverSide: true,
        aaSorting: [],
        ajax: {
            url: $("#table_url").attr("data-table-url"),
            error: function (xhr, error, thrown) {
              console.error("Error occurred: ", error);
              console.log("Thrown error: ", thrown);
              console.log("XHR: ", xhr);
              
              if (xhr.status === 419) {
                  window.location.reload();
                  return;
              }
              
              if (xhr.status === 500) {
                  alert('Internal Server Error (500). Please check the server logs.');
              }
          },
          dataSrc: function (json) {
              console.log("Server Response: ", json);
              return json.data; // Ensure 'data' contains the list of students
          }
        },
        columns: [
            { data: "name", name: "name", orderable: true, searchable: true },
            { data: "slug", name: "slug" },
            { data: "default_image", name: "default_image" },
            { data: "status", name: "status" },
            { data: "created_at", name: "created_at" },
            { data: "created_by", name: "created_by", orderable: true, searchable: true },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
                class: "td-actions",
            },
        ],
          });
        }

        if(dt_slider_ajax_table.length)
          {
            console.log($("#table_url").attr("data-table-url"));
            var dt_ajax = dt_slider_ajax_table.dataTable({
              processing: true,
          serverSide: true,
          aaSorting: [],
          ajax: {
              url: $("#table_url").attr("data-table-url"),
              error: function (xhr, error, thrown) {
                console.error("Error occurred: ", error);
                console.log("Thrown error: ", thrown);
                console.log("XHR: ", xhr);
                
                if (xhr.status === 419) {
                    window.location.reload();
                    return;
                }
                
                if (xhr.status === 500) {
                    alert('Internal Server Error (500). Please check the server logs.');
                }
            },
            dataSrc: function (json) {
                console.log("Server Response: ", json);
                return json.data; // Ensure 'data' contains the list of students
            }
          },
          columns: [
              { data: "name", name: "name", orderable: true, searchable: true },
              { data: "slug", name: "slug" },
              { data: "default_image", name: "default_image" },
              { data: "status", name: "status" },
              { data: "created_at", name: "created_at" },
              { data: "created_by", name: "created_by", orderable: true, searchable: true },
              {
                  data: "action",
                  name: "action",
                  orderable: false,
                  searchable: false,
                  class: "td-actions",
              },
          ],
            });
          }
  
          if(dt_partner_ajax_table.length)
            {
              console.log($("#table_url").attr("data-table-url"));
              var dt_ajax = dt_partner_ajax_table.dataTable({
                processing: true,
            serverSide: true,
            aaSorting: [],
            ajax: {
                url: $("#table_url").attr("data-table-url"),
                error: function (xhr, error, thrown) {
                  console.error("Error occurred: ", error);
                  console.log("Thrown error: ", thrown);
                  console.log("XHR: ", xhr);
                  
                  if (xhr.status === 419) {
                      window.location.reload();
                      return;
                  }
                  
                  if (xhr.status === 500) {
                      alert('Internal Server Error (500). Please check the server logs.');
                  }
              },
              dataSrc: function (json) {
                  console.log("Server Response: ", json);
                  return json.data; // Ensure 'data' contains the list of students
              }
            },
            columns: [
                { data: "name", name: "name", orderable: true, searchable: true },
                { data: "slug", name: "slug" },
                { data: "default_image", name: "default_image" },
                { data: "status", name: "status" },
                { data: "created_at", name: "created_at" },
                { data: "created_by", name: "created_by", orderable: true, searchable: true },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false,
                    class: "td-actions",
                },
            ],
              });
            }

            if(dt_client_ajax_table.length)
              {
                console.log($("#table_url").attr("data-table-url"));
                var dt_ajax = dt_client_ajax_table.dataTable({
                  processing: true,
              serverSide: true,
              aaSorting: [],
              ajax: {
                  url: $("#table_url").attr("data-table-url"),
                  error: function (xhr, error, thrown) {
                    console.error("Error occurred: ", error);
                    console.log("Thrown error: ", thrown);
                    console.log("XHR: ", xhr);
                    
                    if (xhr.status === 419) {
                        window.location.reload();
                        return;
                    }
                    
                    if (xhr.status === 500) {
                        alert('Internal Server Error (500). Please check the server logs.');
                    }
                },
                dataSrc: function (json) {
                    console.log("Server Response: ", json);
                    return json.data; // Ensure 'data' contains the list of students
                }
              },
              columns: [
                  { data: "name", name: "name", orderable: true, searchable: true },
                  { data: "slug", name: "slug" },
                  { data: "default_image", name: "default_image" },
                  { data: "status", name: "status" },
                  { data: "created_at", name: "created_at" },
                  { data: "created_by", name: "created_by", orderable: true, searchable: true },
                  {
                      data: "action",
                      name: "action",
                      orderable: false,
                      searchable: false,
                      class: "td-actions",
                  },
              ],
                });
              }

        if(dt_service_ajax_table.length)
          {
            console.log($("#table_url").attr("data-table-url"));
            var dt_ajax = dt_service_ajax_table.dataTable({
              processing: true,
          serverSide: true,
          aaSorting: [],
          ajax: {
              url: $("#table_url").attr("data-table-url"),
              error: function (xhr, error, thrown) {
                console.error("Error occurred: ", error);
                console.log("Thrown error: ", thrown);
                console.log("XHR: ", xhr);
                
                if (xhr.status === 419) {
                    window.location.reload();
                    return;
                }
                
                if (xhr.status === 500) {
                    alert('Internal Server Error (500). Please check the server logs.');
                }
            },
            dataSrc: function (json) {
                console.log("Server Response: ", json);
                return json.data; // Ensure 'data' contains the list of students
            }
          },
          columns: [
              { data: "name", name: "name", orderable: true, searchable: true },
              { data: "slug", name: "slug" },
              { data: "default_image", name: "default_image" },
              { data: "status", name: "status" },
              { data: "created_at", name: "created_at" },
              { data: "created_by", name: "created_by", orderable: true, searchable: true },
              {
                  data: "action",
                  name: "action",
                  orderable: false,
                  searchable: false,
                  class: "td-actions",
              },
          ],
            });
          }

          if(dt_project_ajax_table.length)
            {
              console.log($("#table_url").attr("data-table-url"));
              var dt_ajax = dt_project_ajax_table.dataTable({
                processing: true,
            serverSide: true,
            aaSorting: [],
            ajax: {
                url: $("#table_url").attr("data-table-url"),
                error: function (xhr, error, thrown) {
                  console.error("Error occurred: ", error);
                  console.log("Thrown error: ", thrown);
                  console.log("XHR: ", xhr);
                  
                  if (xhr.status === 419) {
                      window.location.reload();
                      return;
                  }
                  
                  if (xhr.status === 500) {
                      alert('Internal Server Error (500). Please check the server logs.');
                  }
              },
              dataSrc: function (json) {
                  console.log("Server Response: ", json);
                  return json.data; // Ensure 'data' contains the list of students
              }
            },
            columns: [
                { data: "name", name: "name", orderable: true, searchable: true },
                { data: "slug", name: "slug" },
                { data:"stage", name: "stage"},
                { data: "default_image", name: "default_image" },
                { data: "status", name: "status" },
                { data: "created_at", name: "created_at" },
                { data: "created_by", name: "created_by", orderable: true, searchable: true },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false,
                    class: "td-actions",
                },
            ],
              });
            }

      
      

      


      //payments Table
    // if(dt_payments_ajax_table.length)
    //   {
    //     console.log($("#table_url").attr("data-table-url"));
    //     var dt_ajax = dt_payments_ajax_table.dataTable({
    //       processing: true,
    //   serverSide: true,
    //   aaSorting: [],
    //   ajax: {
    //       url: $("#table_url").attr("data-table-url"),
    //       error: function (xhr, error, thrown) {
    //         console.error("Error occurred: ", error);
    //         console.log("Thrown error: ", thrown);
    //         console.log("XHR: ", xhr);
            
    //         if (xhr.status === 419) {
    //             window.location.reload();
    //             return;
    //         }
            
    //         if (xhr.status === 500) {
    //             alert('Internal Server Error (500). Please check the server logs.');
    //         }
    //     },
    //     dataSrc: function (json) {
    //         console.log("Server Response: ", json);
    //         return json.data; // Ensure 'data' contains the list of students
    //     }
    //   },
    //   columns: [
    //     { data: "payment_logo", name: "logo" },
    //     { data: "display_name", name: "name" },
    //     { data: "provider_name", name: "provider_name" },
    //     { data: "method_name", name: "method_name" },
        
    //     {
    //         data: "action",
    //         name: "action",
    //         orderable: false,
    //         searchable: false,
    //         class: "td-actions",
    //     },
    //   ],
    //     });
    //   }

    

    
      
  // Column Search
  // --------------------------------------------------------------------

  if (dt_filter_table.length) {
    // Setup - add a text input to each footer cell
    $('.dt-column-search thead tr').clone(true).appendTo('.dt-column-search thead');
    $('.dt-column-search thead tr:eq(1) th').each(function (i) {
      var title = $(this).text();
      $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');

      $('input', this).on('keyup change', function () {
        if (dt_filter.column(i).search() !== this.value) {
          dt_filter.column(i).search(this.value).draw();
        }
      });
    });

    var dt_filter = dt_filter_table.DataTable({
      ajax: assetsPath + 'json/table-datatable.json',
      columns: [
        { data: 'full_name' },
        { data: 'email' },
        { data: 'post' },
        { data: 'city' },
        { data: 'start_date' },
        { data: 'salary' }
      ],
      orderCellsTop: true,
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>'
    });
  }

  // Advanced Search
  // --------------------------------------------------------------------

  // Advanced Filter table
  if (dt_adv_filter_table.length) {
    var dt_adv_filter = dt_adv_filter_table.DataTable({
      dom: "<'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-6'i><'col-sm-12 col-md-6 dataTables_pager'p>>",
      ajax: assetsPath + 'json/table-datatable.json',
      columns: [
        { data: '' },
        { data: 'full_name' },
        { data: 'email' },
        { data: 'post' },
        { data: 'city' },
        { data: 'start_date' },
        { data: 'salary' }
      ],

      columnDefs: [
        {
          className: 'control',
          orderable: false,
          targets: 0,
          render: function (data, type, full, meta) {
            return '';
          }
        }
      ],
      orderCellsTop: true,
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['full_name'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                ? '<tr data-dt-row="' +
                    col.rowIndex +
                    '" data-dt-column="' +
                    col.columnIndex +
                    '">' +
                    '<td>' +
                    col.title +
                    ':' +
                    '</td> ' +
                    '<td>' +
                    col.data +
                    '</td>' +
                    '</tr>'
                : '';
            }).join('');

            return data ? $('<table class="table"/><tbody />').append(data) : false;
          }
        }
      }
    });
  }

  // on key up from input field
  $('input.dt-input').on('keyup', function () {
    filterColumn($(this).attr('data-column'), $(this).val());
  });

  // Responsive Table
  // --------------------------------------------------------------------

  if (dt_responsive_table.length) {
    var dt_responsive = dt_responsive_table.DataTable({
      ajax: assetsPath + 'json/table-datatable.json',
      columns: [
        { data: '' },
        { data: 'full_name' },
        { data: 'email' },
        { data: 'post' },
        { data: 'city' },
        { data: 'start_date' },
        { data: 'salary' },
        { data: 'age' },
        { data: 'experience' },
        { data: 'status' }
      ],
      columnDefs: [
        {
          className: 'control',
          orderable: false,
          targets: 0,
          searchable: false,
          render: function (data, type, full, meta) {
            return '';
          }
        },
        {
          // Label
          targets: -1,
          render: function (data, type, full, meta) {
            var $status_number = full['status'];
            var $status = {
              1: { title: 'Current', class: 'bg-label-primary' },
              2: { title: 'Professional', class: ' bg-label-success' },
              3: { title: 'Rejected', class: ' bg-label-danger' },
              4: { title: 'Resigned', class: ' bg-label-warning' },
              5: { title: 'Applied', class: ' bg-label-info' }
            };
            if (typeof $status[$status_number] === 'undefined') {
              return data;
            }
            return (
              '<span class="badge ' + $status[$status_number].class + '">' + $status[$status_number].title + '</span>'
            );
          }
        }
      ],
      // scrollX: true,
      destroy: true,
      dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              var data = row.data();
              return 'Details of ' + data['full_name'];
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col, i) {
              return col.title !== '' // ? Do not show row in modal popup if title is blank (for check box)
                ? '<tr data-dt-row="' +
                    col.rowIndex +
                    '" data-dt-column="' +
                    col.columnIndex +
                    '">' +
                    '<td>' +
                    col.title +
                    ':' +
                    '</td> ' +
                    '<td>' +
                    col.data +
                    '</td>' +
                    '</tr>'
                : '';
            }).join('');

            return data ? $('<table class="table"/><tbody />').append(data) : false;
          }
        }
      }
    });
  }

  // Filter form control to default size
  // ? setTimeout used for multilingual table initialization
  setTimeout(() => {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 200);
});
