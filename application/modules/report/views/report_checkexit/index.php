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
				<div class="col col-md-4" style="padding: 5px 15px">
					<button class="btn bg-angkasa2 btn-sm btn-icon legitRipple" id="btndownload">Download</button>

				</div>
				<div class="col col-md-8 " style="padding: 5px 15px">
					<div class="input-group" align="right">
						<span class="input-group-addon">From :</span>
							<input type="text" value="<?php echo $lastweek ?>" id="sortFrom" placeholder="YYYY-MM-DD" class="form-control datepicker" required>
							<span class="input-group-addon"><i class="icon-calendar3"></i></span>
							<span class="input-group-addon">To</span>
							<input type="text" value="<?php echo date('Y-m-d') ?>" id="sortTo" placeholder="YYYY-MM-DD" class="form-control datepicker pull-right" required>
							<span class="input-group-addon"><i class="icon-calendar3"></i></span>

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

		window.location.href="<?php echo site_url('transaction/check_exit/download?') ?>sortFrom="+sortFrom+"&sortTo="+sortTo+"&search="+search;
	});

	$(".datepicker").datepicker({
		format: 'yyyy-mm-dd',
		autoclose:true,
		orientation: "bottom auto",
	});

	settingDefaultDatagrid()
	$(document).ready(function(){
		$('#grid').datagrid({
			url         : '<?php echo site_url('report/report_checkexit/getList'); ?>',
			// pagination : true,
			// rownumbers : true,
			// fitColumns : true,
			emptyMsg: 'No Records Found',
			queryParams: {sortFrom:$('#sortFrom').val(),sortTo:$('#sortTo').val()},
			columns:[[

				{ field: 'transaction_date', title: 'Date Exit', sortable: true, halign:"center",width:50},
				{ field: 'po_name', title: 'Po Name', sortable: true, halign:"center",width:50 },
				{ field: 'terminal_name', title: 'Terminal', sortable: true, halign:"center",width:50},
				{ field: 'route_info', title: 'Route', sortable: true, halign:"center",width:50},
				{ field: 'total_po', title: 'Total Po', sortable: true, halign:"center",width:50},
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

	function searching()
	{
		$('#grid').datagrid('load',{sortFrom: $('#sortFrom').val(),sortTo: $('#sortTo').val(),search: $('#search').val()});
	}
</script>