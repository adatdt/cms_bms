
<?php $this->load->view('common/jeasyui'); ?>
<?php  $lastweek = date('Y-m-d',strtotime("-7 days"));?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/common/toExcel.js"></script>


<div class="panel bg-angkasa2">
	<div class="panel-heading ">
		<h5 class="panel-title" ><?php echo $title; ?></h5>
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
									<?php if ($user_po==0) { ?>
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
								<span class="input-group-addon">Shelter</span>
								<select class="form-control" name="shelter" id="shelter">
									<option value="">Select</option>
									<?php foreach($shelter as $shelter) {?>
									<option value="<?php echo encode($shelter->id_seq) ?>"><?php echo $shelter->shelter_name ?></option>
									<?php }?>
								</select>
							</div>
						</div>

						<div class="col col-md-3 dataFilter">
							<div class="input-group" align="right">
								<span class="input-group-addon">Status ticket</span>
								<select class="form-control" name="integration" id="integration">
									<option value="">Select</option>
									<option value="<?php echo encode('1') ?>">Integration</option>
									<option value="<?php echo encode('2') ?>">Non Integration</option>
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
		var integration= $('#integration').val()

		window.location.href="<?php echo site_url('transaction/boarding/download?') ?>sortFrom="+sortFrom+"&sortTo="+sortTo+"&search="+search+"&po="+po+"&shelter="+shelter+"&integration="+integration;
	});

	$(".datepicker").datepicker({
		format: 'yyyy-mm-dd',
		autoclose:true,
		orientation: "bottom auto",
	});

	settingDefaultDatagrid()
	$(document).ready(function(){
		$('#grid').datagrid({
			url         : '<?php echo site_url('transaction/boarding/getList'); ?>',
			// pagination : true,
			// rownumbers : true,
			// fitColumns : true,
			emptyMsg: 'No Records Found',
			queryParams: {sortFrom:$('#sortFrom').val(),sortTo:$('#sortTo').val(),po:$('#po').val()},
			columns:[[

				{ field: 'booking_code', title: 'Booking Code', sortable: true, halign:"center" },
				{ field: 'ticket_code', title: 'Ticket Number', sortable: true, halign:"center"},
				{ field: 'created_on', title: 'Transaction Date', sortable: true, halign:"center" },
				{ field: 'shelter_name', title: 'Shelter', sortable: true, halign:"center",width:50},
				{ field: 'po_name', title: 'PO Bus', sortable: true, halign:"center",width:50 },
				{ field: 'route_info', title: 'Route', sortable: true, halign:"center",width:50 },
				{ field: 'type', title: 'Type', sortable: true, halign:"center", align:"right", width:50},
				{ field: 'price2', title: 'Fare', sortable: true, halign:"center", align:"right", width:50},
				// { field: 'status_integration', title: 'Status Ticket', sortable: true, halign:"center", align:"center", width:50},

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

	$('#integration').on('change', function() {
		searching();
	});

	function searching()
	{
		$('#grid').datagrid('load',{sortFrom: $('#sortFrom').val(),sortTo: $('#sortTo').val(),search: $('#search').val(),po :$('#po').val(),shelter:$('#shelter').val(),integration:$('#integration').val()});
	}
</script>