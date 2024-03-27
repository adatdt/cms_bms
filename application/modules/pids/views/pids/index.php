<?php $this->load->view('common/jeasyui'); ?>
<?php  $lastweek = date('Y-m-d',strtotime("-7 days"));?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/common/toExcel.js"></script>


<div class="panel bg-angkasa2">
	<div class="panel-heading ">
		<h5 class="panel-title" ><?php echo $title ?></h5>
	</div>
	<div class="panel-body" style="display: block; padding:5px ">
		<div id="toolbar">
			<div class="row">
				<div class="col col-md-6" style="padding: 5px 15px">
					<!-- <?php if ($add) {
						create_btn(site_url('pids/add'),'Add','plus-circle2');
					} ?> -->
				</div>

				<div class="col col-md-6 " style="padding: 5px 15px">
					<div class="input-group" align="right">
						<span class="input-group-addon">Terminal</span>
						<select class="form-control" id="shelter">
							<option value="">Select</option>
							<?php foreach($shelter as $shelter) { ?>
							<option value="<?php echo $shelter->id_seq ?>"><?php echo $shelter->shelter_name ?></option>
							<?php } ?>
						</select>

						<span class="input-group-addon">Search : </span>
						<input type="text" id="search" value="" class="form-control" placeholder="Search...">
						<div class="input-group-btn">
							<button type="submit" onclick="searching()" class="btn bg-angkasa2"><i class="icon-search4"></i></button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<table id="grid" toolbar="#toolbar" class="easyui-datagrid" singleSelect="true"></table>
	</div>

</div>
<script type="text/javascript">
	$("#btndownload").click(function(event){

		var sortFrom= $('#sortFrom').val();
		var sortTo= $('#sortTo').val();
		var search= $('#search').val();

		window.location.href="<?php echo site_url('transaction/tap_in/download?') ?>sortFrom="+sortFrom+"&sortTo="+sortTo+"&search="+search;
	});

	$(".datepicker").datepicker({
		format: 'yyyy-mm-dd',
		autoclose:true,
		orientation: "bottom auto",
	});

	settingDefaultDatagrid()
	$(document).ready(function(){
		$('#grid').datagrid({
			url         : '<?php echo site_url('pids/pids/getList'); ?>',
			// pagination : true,
			// rownumbers : true,
			// fitColumns : true,
			emptyMsg: 'No Records Found',
			// queryParams: {sortFrom:$('#sortFrom').val(),sortTo:$('#sortTo').val()},
			columns:[[

				{ field: 'estimated_arrival_time', title: 'Estimated Time', sortable: true, halign:"center"},
				{ field: 'airport_name', title: 'Airport', sortable: true, halign:"center" },
				{ field: 'po_name', title: 'PO Bus', sortable: true, halign:"center",width:100},
				{ field: 'bus_name', title: 'Bus Name', sortable: true, halign:"center",width:100 },
				{ field: 'type', title: 'Type', sortable: true, halign:"center",width:100 },
				{ field: 'plate_number', title: 'plate_number', sortable: true, halign:"center",width:100 },
				{ field: 'shelter_name', title: 'Terminal', sortable: true, halign:"center",width:100 },
				{ field: 'route_info', title: 'Route', sortable: true, halign:"center",width:100 },
				{ field: 'bus_status', title: 'Status', sortable: true, halign:"center" },
				{ field: 'action', title: 'Action',  halign:"center",width:100},

			]],

			onLoadSuccess:function(row){
				if (row.total == 0) {
					$('#btndownload').prop('disabled',true);
				}else{
					$('#btndownload').prop('disabled',false);
				}

				$(window).resize(function(){
					setTimeout(function(){
						$('#grid').datagrid('resize');
					},400);
				});

				$(".nav").click(function(){
					setTimeout(function(){
						$('#grid').datagrid('resize');
					},400);
				});

			}
		});
	});

	document.getElementById('search').onkeydown = function(e) {
		if (e.keyCode == 13 || e.which == 13) {
			searching(e);
		}
	}


	function searching()
	{
		$('#grid').datagrid('load',{search: $('#search').val()});
	}

	$("#shelter").on("change",function(){

		$('#grid').datagrid('load',{shelter: $("#shelter").val()});	
	});


	function edit(id){
		$.magnificPopup.open({
			items: {
				src: "<?php echo site_url('pids/pids/edit/') ?>"+id,
			},
			modal: true,
			type: 'ajax',
			tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
			showCloseBtn: false,
		});
	}

	function deleteData(id){
		url = "<?php echo site_url('pids/pids/delete/') ?>"+id;
		confirm_delete(url);
	}


</script>