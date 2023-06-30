@extends('layouts.mainlayout')
@section('content')
<style>
    /* Active column visibility button */
    .dt-button {
        background-color: #6c757d;
        color: #fff;
        border: #6c757d; 
    }
    .dt-button:hover {
        background-color: #5c636a;
        color: #fff;
        transition: 0.3s;
        border: #6c757d; 

    }

    /* Inactive column visibility button */
    #catalogTable_wrapper .dt-button-collection {
        position: absolute;
        z-index: 1;
        background: #eee;
        margin: 0 !important;
        top: 40px !important;
        padding: 10px;
        left: 0 !important;
    }

    .dt-buttons {
        position: relative;
    }

    #catalogTable_wrapper .dt-button-background {
        display: none !important;
    }


    #catalogTable_wrapper .dt-button-collection .dt-button {
        background: #0d6efd;
        margin: 2px;
        font-size: 12px;
        width: 12%;
        color: #fff;
        border: 1px solid #0d6efd;
    }

    #catalogTable_wrapper .dt-button-collection .dt-button.active {
        background: #95c0ff;
        border: 1px solid #579bff;
    }
</style>
    <div class="container-fluid">
        <div class="row">
            <div class="col-2 bg-dark text-light side-bar">
                <div class="position-absolute top-0 start-0 bg-dark text-light side-bar">
                    <!-- Sidebar content goes here -->
                    @include('settings.settings')
                </div>
            </div>
            <div class="col-10">
                <div class="container mt-4">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            @if (session('match'))
                                <div class="alert alert-success">
                                    {{ session('match') }}
                                </div>
                            @elseif(session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @elseif(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            <div class="card">
                                <div class="card-body">
                                    <div class="container mt-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="h2 w-50">ADD CATALOG</div>
                                            <div class="input-group d-grid gap-4 d-md-flex justify-content-md-end">
                                                <form action="{{ route('catalog.export') }}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="hidden_columns[]" id="hiddenColumnsInput">
                                                    <button type="submit" class="rounded-pill p-2 fs-6 btn btn-secondary"
                                                        style="width: 250px;"><i class="fa-solid fa-file-arrow-down"></i>
                                                        DOWNLOAD TEMPLATE</button>
                                                </form>
                                                <form method="POST" id="myForm" action="{{ route('import') }}"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <input id="file_input" style="display:none" type="file"
                                                        name="excel_file" accept=".csv,.xls,.xlsx">
                                                    <button type="button" onclick="selectFile()"
                                                        class="rounded-pill p-2 fs-6 btn btn-secondary"
                                                        style="width: 250px;"><i class="fa-solid fa-file-arrow-up"></i>
                                                        UPLOAD A NEW FILE</button>
                                                </form>
                                            </div>
                                        </div>
                                        {{-- <button id="filterButton" class="rounded-pill fs-6 btn btn-secondary"
                                            type="button" style="width: 250px;">
                                            <i class="fa-solid fa-filter"></i> FILTER
                                        </button> --}}
                                    </div>
                                    @if (isset($empty))
                                        <p class="text-center fs-3 mt-4">{{ $empty }}</p>
                                    @else
                                        <table id="catalogTable" class="table table-bordered text-justify">
                                            <thead>
                                                <tr>
                                                    @foreach ($columns as $column)
                                                        <th>{{ $column }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($rows as $row)
                                                    <tr>
                                                        @foreach ($row as $value)
                                                            <td class="text-truncate ellipsis py-3"
                                                                style="max-width:150px;">
                                                                {{ $value }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function selectFile() {
            document.getElementById('file_input').click();
        }

        document.getElementById('file_input').addEventListener('change', function() {
            document.getElementById('myForm').submit();
        });

        $(document).ready(function() {
            var importantColumns = [0, 1, 2]; // Define the indices of the important columns

            var dataTable = $('#catalogTable').DataTable({
                "dom": 'Bfrtip',
                "buttons": [{
                        "extend": 'copyHtml5',
                        "exportOptions": {
                            "columns": [0, ':visible']
                        }
                    },
                    {
                        "extend": 'excelHtml5',
                        "exportOptions": {
                            "columns": ':visible'
                        }
                    },
                    {
                        "extend": 'pdfHtml5',
                        "exportOptions": {
                            "columns": [0, 1, 2, 5]
                        }
                    },
                    {
                        "extend": 'colvis',
                        "text": 'Column Visibility',
                        "className": 'btn-column-visibility'
                    },
                    {
                        "text": 'Hide All',
                        "action": function(e, dt, button, config) {
                            if (button.text() === 'Hide All') {
                                dataTable.columns().visible(false);
                                button.text('Show All');
                            } else {
                                dataTable.columns().visible(true);
                                button.text('Hide All');
                            }
                        }
                    }
                ],
                "scrollX": true,
                // "scrollY": "500px",
                "initComplete": function() {
                    var api = this.api();

                    // Event listener for search input
                    api.columns().every(function() {
                        var column = this;
                        var title = $(column.header()).text();

                        // Create input element and add event listener
                        var input = $('<input type="text" placeholder="Search ' + title +
                                '" />')
                            .on('click', function(e) {
                                e
                                    .stopPropagation(); // Prevent sorting when clicking on the input
                            })
                            .on('keyup change clear', function() {
                                if (column.search() !== this.value) {
                                    // Disable sorting before performing the search
                                    dataTable.order([]).draw();

                                    column.search(this.value).draw();
                                }
                            });

                        // Check if the column index is in the importantColumns array
                        if (importantColumns.includes(column.index())) {
                            $(column.header()).addClass(
                                'important'); // Add a class to style the important columns
                        }

                        $(column.header()).append(input);
                    });
                },
                "columnDefs": [{
                    "targets": '_all',
                    "orderable": true // Enable sorting for all columns
                }]
            });

            // Event listener for Column Visibility button
            $('.btn-column-visibility').on('click', function() {
                dataTable.colReorder
            .reset(); // Reset column order before showing the column visibility modal
                dataTable.colReorder.enable(); // Enable column reordering
                dataTable.colReorder.order([0, 1, 2, 3, 4, 5, 6, 7, 8]); // Set the default column order
                dataTable.colReorder.draw(); // Redraw the table

                dataTable.buttons().colvisShow(); // Show the column visibility buttons
                dataTable.buttons().colvisRestore(); // Restore the column visibility state

                // Change color of active/inactive columns
                $('.dt-button-collection .dt-button').each(function(index) {
                    if (dataTable.column(index).visible()) {
                        $(this).addClass('active'); // Add 'active' class for visible columns
                    } else {
                        $(this).removeClass('active'); // Remove 'active' class for hidden columns
                    }
                });
            });
        });
    </script>

@endsection
