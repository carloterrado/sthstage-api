@extends('layouts.mainlayout')

@section('content')

	<section class="main-container">
		<h1 style="display:none">Bulk Upload</h1>

			<!-- Page header -->
			<div class="header">
				<div class="header-content">
					<div class="page-title">
					    Catalog
					</div>
				</div>
			</div>
			<!-- /Page header -->

			<div class="container-fluid page-content">
				<div class="card card-inverse card-flat">
					<div class="card-block">
					    <div class="upload">
                            <input type="file" title="" class="drop-here">
                            <div class="text text-drop">Drop CSV here</div>
                            <div class="text text-upload">uploading</div>
                            <svg class="progress-wrapper" width="300" height="300">
                            <circle class="progress" r="115" cx="150" cy="150"></circle>
                          </svg>
                            <svg class="check-wrapper" width="130" height="130">
                            <polyline class="check" points="100.2,40.2 51.5,88.8 29.8,67.5 "/>
                          </svg>
                            <div class="shadow"></div>
                        </div>
					</div>
				</div>
				
				<div class="card card-inverse card-flat">
					<div class="card-block">
					    <div class="card-header">
    						<div class="card-title">
    						    <div class="row">
    						        <div class="col-md-6">
    						            Lists
    						        </div>
    						        
                    				<div class="text-right col-md-6">
                    				    <a href="#" class="btn btn-success btn-xs" style="padding: 10px 15px; font-size: 12px; display: inline-block;">Download Template</a>
                    				    <a href="#" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#modal_order" style="padding: 10px 15px; font-size: 12px; display: inline-block;">Add Catalog</a>
                    				</div>
    						    </div>
    						 </div>
    					</div>
					    
					    <div>
        					<table class="table datatable datatable-header-fixed">
        					    <thead>
                                    <tr class="bg-black-800">
                                        <th>Brand</th>
                                        <th>MSPN</th>
                                        <th>Model </th>
                                        <th>Size</th>
                                        <th>Description </th>
                                        <th>Date Added </th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>MICHELIN</td>
                                        <td>29523</td>
                                        <td>PILOT SPORT CUP 2</td>
                                        <td>2853019</td>
                                        <td>MICHELIN PILOT SPORT CUP 2 285/30R19 BSW 180 AA A</td>
                                        <td>2020-08-07 21:12:41</td>
                                        <td>Competed</td>
                                        <td>
                                        	<a href="#"><i class="icon icon-pencil5"></i> Edit </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>TOYO</td>
                                        <td>238430</td>
                                        <td>CELSIUS CARGO</td>
                                        <td>2756520</td>
                                        <td>TOYO CELSIUS CARGO 275/65R20 BSW </td>
                                        <td>2020-07-31 20:22:53</td>
                                        <td>Missing Load Index</td>
                                        <td>
                                        	<a href="#"><i class="icon icon-pencil5"></i> Edit </a>       
                                        </td>
                                    </tr>
                                </tbody>
        					</table>
        				</div>
					</div>
				</div>

			</div>
@endsection
@section('scripts')

<script type="text/javascript">
        
        $(document).ready(function() {
            
            // Setup - add a text input to each footer cell
            $('.datatable-header-fixed thead tr').clone(true).appendTo( '.datatable-header-fixed thead' );
            $('.datatable-header-fixed thead tr:eq(1) th').each( function (i) {
                var title = $(this).text();
                $(this).html( '<input type="text" class="form-control input-xs" placeholder="'+title+'" />' );
         
                $( 'input', this ).on( 'keyup change', function () {
                    if ( table.column(i).search() !== this.value ) {
                        table
                            .column(i)
                            .search( this.value )
                            .draw();
                    }
                } );
            } );
         
            var table = $('.datatable-header-fixed').DataTable( {
                orderCellsTop: true,
                paging: false,
                iDisplayLength: -1
            } );
            
            // Add placeholder to the datatable filter option
        	$('.dataTables_filter input[type=search]').attr('placeholder','Type to search...');
        	$('.dataTables_filter input[type=search]').attr('class', 'form-control');
        
        	// Enable Select2 select for the length option
        	$('.dataTables_length select').select2({
        		minimumResultsForSearch: Infinity,
        		width: 'auto'
        	});
        } );

	var fileUpload = document.querySelector(".upload");

    fileUpload.addEventListener("dragover", function() {
      this.classList.add("drag");
      this.classList.remove("drop", "done");
    });
    
    fileUpload.addEventListener("dragleave", function() {
      this.classList.remove("drag");
    });
    
    fileUpload.addEventListener("drop", start, false);
    fileUpload.addEventListener("change", start, false);
    
    function start() { 
      this.classList.remove("drag");
      this.classList.add("drop");
      setTimeout(() => this.classList.add("done"), 3000);
    }
    
    
</script>
@endsection