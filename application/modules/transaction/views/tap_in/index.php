<?php $this->load->view('common/jeasyui'); ?>
<?php  $lastweek = date('Y-m-d',strtotime("-7 days"));?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/common/toExcel.js"></script>
<style>
    .datagrid-header-rownumber{
        width:30px;
    }
    .datagrid-cell-rownumber{
        width:30px;
    }
</style>

<div class="panel bg-angkasa2">
	<div class="panel-heading ">
		<h5 class="panel-title" ><?php echo $title ?></h5>
	</div>
	<div class="panel-body" style="display: block; padding:5px ">
		<div id="toolbar">
			<div class="row">
				<div class="col col-md-1" style="padding: 5px 15px">
					<button class="btn bg-angkasa2 btn-sm btn-icon legitRipple" id="btndownload">Download</button>
					<!-- <a class="btn bg-angkasa2 btn-icon legitRipple" href="javascript:;" class="easyui-linkbutton" onclick="$('#grid').datagrid('print','DataGrid')">Print</a> -->
				</div>
				<div class="col col-md-11 " style="padding: 5px 15px" align="right" >
					<div class="row">

						<div class="col col-md-5">
							<div class="input-group" >
								<span class="input-group-addon">From :</span>
								<input type="text" value="<?php echo $lastweek ?>" id="sortFrom" placeholder="YYYY-MM-DD" class="form-control datepicker" required>
								<span class="input-group-addon"><i class="icon-calendar3"></i></span>
							
								<span class="input-group-addon">To</span>
								<input type="text" value="<?php echo date('Y-m-d') ?>" id="sortTo" placeholder="YYYY-MM-DD" class="form-control datepicker pull-right" required>
								<span class="input-group-addon"><i class="icon-calendar3"></i></span>
							</div>
						</div>

						<div class="col col-md-2">
							<div class="input-group" >
								<span class="input-group-addon">PO</span>
								<select class="form-control" name="po" id="po">
								<?php if($user_po==0) { ?>
									<option value="">Select</option>
									<?php foreach($po as $po){ ?>
									<option value="<?php echo encode($po->id_seq)?>"><?php echo $po->po_name?></option>
									<?php } ?>
								<?php } else { ?>
									<option value="<?php echo encode($po->id_seq)?>"><?php echo $po->po_name?></option>
								<?php } ?>
								</select>
							</div>
						</div>

						<div class="col col-md-2">
							<div class="input-group" >
								<span class="input-group-addon">Type</span>
								<select class="form-control" name="type" id="type">
									<option value="">Select</option>
									<?php foreach($busType as $busType){ ?>
									<option value="<?php echo $busType->id_seq?>"><?php echo $busType->type ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="col col-md-3">
							<div class="input-group" >
								<span class="input-group-addon">Search : </span>
								<input type="text" id="search" value="" class="form-control" placeholder="Search...">
								<div class="input-group-btn">
									<button type="submit" onclick="searching()" class="btn bg-angkasa2"><i class="icon-search4"></i></button>
								</div>
							</div>
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
		var po= $('#po').val();
		var type= $('#type').val();

		window.location.href="<?php echo site_url('transaction/tap_in/download?') ?>sortFrom="+sortFrom+"&sortTo="+sortTo+"&search="+search+"&po="+po+"&type="+type;
	});

	$(".datepicker").datepicker({
		format: 'yyyy-mm-dd',
		autoclose:true,
		orientation: "bottom auto",
	});


	settingDefaultDatagrid();
	$(document).ready(function(){
		$('#grid').datagrid({
			url         : '<?php echo site_url('transaction/tap_in/getList'); ?>',
			// // pagination : true,
			// // rownumbers : true,
			// // fitColumns : true,
			// emptyMsg: 'No Records Found.',
			queryParams: {sortFrom:$('#sortFrom').val(),sortTo:$('#sortTo').val(),po:$('#po').val()},
			columns:[[

				{ field: 'created_on', title: 'Transaction Date', sortable: true, halign:"center",width:100 },
				{ field: 'uid', title: 'UID', sortable: true, halign:"center",width:100 },
				{ field: 'po_name', title: 'PO Bus', sortable: true, halign:"center",width:100 },
				{ field: 'bus_name', title: 'Bus Name', sortable: true, halign:"center",width:100 },
				{ field: 'type', title: 'Type', sortable: true, halign:"center",width:100 },
				{ field: 'plate_number', title: 'Plate Number', sortable: true, halign:"center",width:100 },				
				{ field: 'status', title: 'Status', sortable: true, halign:"center",width:100, align:"center" },
				{ field: 'action', title: 'Action', sortable: true, halign:"center",width:100, align:"center" }
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

	$('#sortFrom').on('changeDate', function() {
		searching();
	});

	$('#sortTo').on('changeDate', function() {
		searching();
	});

	$('#po').on('change', function() {
		searching();
	});

	$('#type').on('change', function() {
		searching();
	});

	function searching()
	{
		$('#grid').datagrid('load',{sortFrom: $('#sortFrom').val(),sortTo: $('#sortTo').val(),search: $('#search').val(),po: $('#po').val(),type: $('#type').val()});
	}
	function clearData(id){
		url = "<?php echo site_url('transaction/tap_in/clear/') ?>"+id;
		confirm_clear(url);
	}

</script>