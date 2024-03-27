<?php $this->load->view('common/jeasyui'); ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/common/toExcel.js"></script>
<style type="text/css">
	.datagrid-cell-group {
		height: 32px !important;
	}
</style>

<div class="panel bg-angkasa2">
	<div class="panel-heading ">
		<h5 class="panel-title" ><?php echo $title ?></h5>
	</div>
	<div class="panel-body" style="display: block; padding:5px ">
		<div id="toolbar">
			<div class="row">
				<div class="col col-md-8" style="padding: 5px 15px"></div>			
				<div class="col col-md-4" style="padding: 5px 15px">
				<?php if($this->session->userdata('user_group_id')!=11): ?>  <!--Hardcode untuk user grup PO-->
					<div class="input-group ">
						<span class="input-group-addon">PO</span>
						<select class="form-control" id="po">
							<?php if($user_po==0){ ?>
								<option value="<?php echo encode(0)?>">Select</option>
								<?php foreach($po as $po){ ?>
								<option value="<?php echo encode($po->id_seq)?>"><?php echo $po->po_name?></option>
								<?php } ?>
							<?php }else{ ?>
								<option value="<?php echo encode($po->id_seq)?>"><?php echo $po->po_name?></option>
							<?php } ?>
						</select>
					</div>
				<?php endif; ?>
				</div>
			</div>
		</div>
		<table id="grid" toolbar="#toolbar" class="easyui-datagrid" singleSelect="true"></table>
	</div>
</div>
<br>
<div class="panel bg-angkasa2">
	<div class="panel-heading ">
		<h5 class="panel-title" >Tracking Posisi Bus</h5>
	</div>
	<div class="panel-body" style="display: block; padding:5px ">
		<div id="toolbar_bawah">
			<div class="row">
				<div class="col col-md-8" style="padding: 5px 15px"></div>
				<div class="col col-md-4" style="padding: 5px 15px">
				
				</div>
			</div>
		</div>
		<table id="grid_bawah" toolbar="#toolbar_bawah" class="easyui-datagrid" singleSelect="true"></table>
	</div> 
</div>
<script type="text/javascript">
	$(".datepicker").datepicker({
		format: 'yyyy-mm-dd',
		autoclose:true,
		orientation: "bottom auto",
	});

	settingDefaultDatagrid();
	$(document).ready(function(){
		grid_atas();
		grid_bawah();
	});

	function grid_atas(){
		$('#grid').datagrid({
			url         : '<?php echo site_url('dashboard/getListDashboardPo'); ?>',
			emptyMsg	: 'No Records Found',
			pageSize 	: 100,
			queryParams	: {po: $('#po').val()},
			columns 	: [[

				{ field: 'route_info', title: 'Route', sortable: true,width:100, halign:"center"},				
				{ field: 't1', title: 'T1', halign:"center", align:"center", height:150,width:30},				
				{ field: 't2', title: 'T2', sortable: false,width:30, halign:"center", align:"center"},
				{ field: 't3', title: 'T3', sortable: false,width:30, halign:"center", align:"center"},
				{ field: 'pax_capacity', title: 'Total Pax/<br/>Capacity',  sortable: true,width:80, halign:"center", align:"center" },
				{ field: 'last_depart', title: 'Last Departed Bus', sortable: true,width:100, halign:"center"},
				{ field: 'last_paid', title: 'Last Ticket Paid', sortable: true,width:100, halign:"center"},
				{ field: 'max_wait', title: 'Max.<br/>Waiting Time', sortable: true,width:80, halign:"center"},
				{ field: 'pool', title: 'Available<br/>Bus in Pool', sortable: true,width:60, halign:"center", align:"center" },
				{ field: 'status', title: 'Status', sortable: true,width:100, halign:"center", align:"center"},
			]
			],

			onLoadSuccess:function(row){
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
	}

	function grid_bawah(){
		$('#grid_bawah').datagrid({
			url         : '<?php echo site_url('dashboard/getListTracking'); ?>',
			emptyMsg	: 'No Records Found',
			pageSize 	: 100,
			fitColumns 	: true,
			queryParams : {po: $('#po').val()},
			columns 	: [[
				{ field: 'route_info', title: 'Route', rowspan:2, sortable: true,width:100, halign:"center"},		
				{ field: 'pool', title: 'Pool', rowspan:2, sortable: false,width:30, halign:"center",align:"center"},		
				{ title: 'Position', colspan:3, halign:"center", align:"center", height:150 },
				{ field: 'jumlah_semua', title: 'Bus Tracked', rowspan:2, sortable: false,width:80, halign:"center", align:"center"},
			],[
				{ field: 't1', title: 'T1', sortable: false,width:30, halign:"center", align:"center"},
				{ field: 't2', title: 'T2', sortable: false,width:30, halign:"center", align:"center"},
				{ field: 't3', title: 'T3', sortable: false,width:30, halign:"center", align:"center"},
			]],

			onLoadSuccess:function(row){
				$(window).resize(function(){
					setTimeout(function(){
						$('#grid_bawah').datagrid('resize');
					},400);
				});

				$(".nav").click(function(){
					setTimeout(function(){
						$('#grid_bawah').datagrid('resize');
					},400);
				});
			}
		});
	}

	$("#po").on("change",function(){
		$('#grid').datagrid('load',{po: $('#po').val()});	
		$('#grid_bawah').datagrid('load',{po: $('#po').val()});	
	});
</script>
