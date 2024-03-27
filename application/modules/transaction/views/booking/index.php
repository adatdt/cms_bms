<?php $this->load->view('common/jeasyui'); ?>
<?php  $lastweek = date('Y-m-d',strtotime("-7 days"));?>
<style type="text/css">
	/*.dataFilter{
		padding: 0px 0px;
	}*/
</style>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/common/toExcel.js"></script>


<div class="panel bg-angkasa2">
	<div class="panel-heading ">
		<h5 class="panel-title" ><?php echo $title ?><!-- <a class="heading-elements-toggle"><i class="icon-more"></i></a> --></h5>

	</div>
	<div class="panel-body" style="display: block; padding:5px ">
		<div id="toolbar">
			<div class="row">
				<div class="col col-md-1" style="padding: 5px 15px">
					<button class="btn bg-angkasa2 btn-sm btn-icon legitRipple" id="btndownload">Download</button>
					<!-- <a class="btn bg-angkasa2 btn-icon legitRipple" href="javascript:;" class="easyui-linkbutton" onclick="$('#grid').datagrid('print','DataGrid')">Print</a> -->
				</div>
				<div class="col col-md-11 " style="padding: 5px 15px">
					<div class="row">
						

						<div class="col col-md-5 dataFilter">
							<div class="input-group" align="right">
								<span class="input-group-addon">From :</span>
									<input type="text" value="<?php echo $lastweek ?>" id="sortFrom" placeholder="YYYY-MM-DD" class="form-control datepicker" required>
									<span class="input-group-addon"><i class="icon-calendar3"></i></span>
									<span class="input-group-addon">To</span>
									<input type="text" value="<?php echo date('Y-m-d') ?>" id="sortTo" placeholder="YYYY-MM-DD" class="form-control datepicker pull-right" required>
									<span class="input-group-addon"><i class="icon-calendar3"></i>
								</span>
							</div>
						</div>

						<div class="col col-md-2 dataFilter">
							<div class="input-group" align="right">
								<span class="input-group-addon">Po</span>
								<select class="form-control" name="po" id="po">
									<?php if($user_po==0) { ?>
									<option value="">Select</option>
									<?php foreach($po as $po) { ?>
									<option value="<?php echo encode($po->id_seq); ?>"><?php echo $po->po_name; ?></option>
									<?php } } else { ?> 
									<option value="<?php echo encode($po->id_seq); ?>"><?php echo $po->po_name; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>


						<div class="col col-md-2 dataFilter">
							<div class="input-group" align="right">
								<span class="input-group-addon">Status</span>
								<select class="form-control" name="status" id="status">
									<option value="">Select</option>
									<?php foreach($status as $status) {?>
									<option value="<?php echo encode($status->id_seq) ?>"><?php echo $status->status_name ?></option>
									<?php }?>
								</select>
							</div>
						</div>

						<div class="col col-md-3 dataFilter">
							<div class="input-group" align="right">
								<span class="input-group-addon">Shelter</span>
								<select class="form-control" name="shelter" id="shelter">
									<option value="">Select</option>
									<?php foreach($shelter as $shelter) {?>
									<option value="<?php echo encode($shelter->id_seq) ?>"><?php echo $shelter->shelter_name ?></option>
									<?php }?>
								</select>
							</div>
						</div>

						<div class="col col-md-12 dataFilter"></div>
						<div class="col col-md-9 dataFilter"></div>


						<div class="col col-md-3 " style="padding-top: 5px">
							<div class="input-group" align="right">
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
		var shelter= $('#shelter').val();
		var status= $('#status').val();

		window.location.href="<?php echo site_url('transaction/booking/download?') ?>sortFrom="+sortFrom+"&sortTo="+sortTo+"&search="+search+"&po="+po+"&shelter="+shelter+"&status="+status;
	});

	$(".datepicker").datepicker({
		format: 'yyyy-mm-dd',
		autoclose:true,
		orientation: "bottom auto",
	});

	settingDefaultDatagrid()
	$(document).ready(function(){
		$('#grid').datagrid({
			url         : '<?php echo site_url('transaction/booking/getList'); ?>',
			emptyMsg: 'No Records Found',
			queryParams: {sortFrom:$('#sortFrom').val(),sortTo:$('#sortTo').val(),po:$('#po').val()},
			nowrap:false,
			fitColumns : true,
			columns:[[

				// { field: 'booking_code', title: 'Booking Code', sortable: true, halign:"center",width:50},
				// { field: 'po_name', title: 'PO Name', sortable: true, halign:"center" ,width:50},
				// { field: 'terminal_name', title: 'Terminal', sortable: true, halign:"center",width:50 },
				// { field: 'qty', title: 'Qty', sortable: true, halign:"center",width:20 },
				// { field: 'total_amount', title: 'Total', sortable: true, halign:"center",width:30 },
				// { field: 'created_on', title: 'Time', sortable: true, halign:"center",width:50 },
				// { field: 'action', title: 'Action', sortable: true, halign:"center",width:15,align:'center' },

				{ field: 'trx_date', title: 'Transaction Date', sortable: true,halign:"center",width:100},
				{ field: 'booking_code', title: 'Booking Code', sortable: true, halign:"center",width:100},
				{ field: 'ticket_code', title: 'Ticket Number', sortable: true, halign:"center",width:100},
				{ field: 'po_name', title: 'Po Bus', sortable: true, halign:"center",width:100},
				{ field: 'shelter_name', title: 'Shelter', sortable: true,align:"center", halign:"center"},
				{ field: 'type', title: 'Type/Class', sortable: true,align:"center", halign:"center"},
				{ field: 'terminal_name', title: 'Terminal', sortable: true,halign:"center"},
				{ field: 'route_info', title: 'Route', sortable: true, halign:"center",width:100},
				{ field: 'price', title: 'Price', sortable: true, halign:"center", align:"right"},
				{ field: 'status', title: 'Status', sortable: true, halign:"center", align:"center"},
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

	$('#shelter').on('change', function() {
		searching();
	});

	$('#status').on('change', function() {
		searching();
	});

	function searching()
	{
		$('#grid').datagrid('load',{sortFrom: $('#sortFrom').val(),sortTo: $('#sortTo').val(),search: $('#search').val(),po :$('#po').val(),shelter:$('#shelter').val(),status:$('#status').val()});
	}

	function clickDetail(id)
	{
		$.magnificPopup.open({
			items: {
				src: "<?php echo site_url('transaction/booking/detail/') ?>"+id,
			},
			modal: true,
			type: 'ajax',
			tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
			showCloseBtn: false,
		});
	}
</script>