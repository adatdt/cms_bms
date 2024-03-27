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
				<div class="col col-md-2" style="padding: 5px 15px">
					<?php if ($add) {
						create_btn(site_url('po/bus/add'),'Add','plus-circle2');
					} ?>
				</div>
				<div class="col col-md-10 " style="padding: 5px 15px">
					<div class="row">
						<div class="col col-md-2"></div>
						<div class="col col-md-3">
							<div class="input-group" align="right">
								<span class="input-group-addon">Po</span>
								<select id="po" name="po" class="form-control">
									<option value="">Select</option>
									<?php foreach ($po as $po) {?>
									<option value="<?php echo encode($po->id_seq) ?>"><?php echo $po->po_name ?></option>
								<?php } ?>
								</select>
							</div>
						</div>

						<div class="col col-md-3">
							<div class="input-group" align="right">
								<span class="input-group-addon">type</span>
								<select id="type" name="type" class="form-control">
									<option value="">Select</option>
									<?php foreach ($type as $type) {?>
									<option value="<?php echo encode($type->id_seq) ?>"><?php echo $type->type ?></option>
								<?php } ?>
								</select>
							</div>
						</div>

						<div class="col col-md-4">
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
			url         : '<?php echo site_url('po/bus/getList'); ?>',
			// pagination : true,
			// rownumbers : true,
			// fitColumns : true,
			emptyMsg: 'No Records Found',
			// queryParams: {sortFrom:$('#sortFrom').val(),sortTo:$('#sortTo').val()},
			columns:[[

				{ field: 'bus_name', title: 'Bus Name', sortable: true, halign:"center",width:100 },
				{ field: 'plate_number', title: 'Plate Number', sortable: true, halign:"center",width:100 },
				{ field: 'po_name', title: 'PO Bus', sortable: true, halign:"center",width:100 },
				{ field: 'type', title: 'Type', sortable: true, halign:"center",width:100 },
				{ field: 'total_seat', title: 'Total Seat', sortable: true, halign:"center",width:100 },
				{ field: 'action', title: 'Action', sortable: true, halign:"center",width:100 },

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

	$("#type").change(function(){
		searching();
	});

	$("#po").change(function(){
		searching();
	});

	function searching()
	{
		$('#grid').datagrid('load',{search: $('#search').val(),po: $('#po').val(),type: $('#type').val()});
	}

	function edit(id){
		$.magnificPopup.open({
			items: {
				src: "<?php echo site_url('po/bus/edit/') ?>"+id,
			},
			modal: true,
			type: 'ajax',
			tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
			showCloseBtn: false,
		});
	}

	function deleteData(id){
		url = "<?php echo site_url('po/bus/delete/') ?>"+id;
		confirm_delete(url);
	}
	
	function validasi(msg,reload=''){
		swal({
			title: msg,
			text: "",
			type: "warning",
			confirmButtonColor: "#EF5350",
			closeOnConfirm: true,
			closeOnCancel: true,
		});
	}

</script>