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
				<div class="col col-md-8" style="padding: 5px 15px">
					<?php if ($add) {
						create_btn(site_url('device/manless_gate/add'),'Add','plus-circle2');
					} ?>
				</div>
				<div class="col col-md-4 " style="padding: 5px 15px">
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

		
		<table id="grid" toolbar="#toolbar" class="easyui-datagrid" singleSelect="true"></table>
	</div>

</div>
<script type="text/javascript">

	$(".datepicker").datepicker({
		format: 'yyyy-mm-dd',
		autoclose:true,
		orientation: "bottom auto",
	});

	settingDefaultDatagrid()
	$(document).ready(function(){
		$('#grid').datagrid({
			url         : '<?php echo site_url('device/manless_gate/getList'); ?>',
			emptyMsg: 'No Records Found',
			columns:[[

				{ field: 'username', title: 'Username', sortable: true,width:100, halign:"center" },
				{ field: 'first_name', title: 'Name', sortable: true,width:100, halign:"center" },
				{ field: 'shelter_name', title: 'Shelter', sortable: true,width:50, halign:"center" },
				{ field: 'lane_name', title: 'Lane', sortable: true,width:50, halign:"center" },
				{ field: 'status', title: 'Status', sortable: true,width:50, halign:"center", align:"center" },
				{ field: 'action', title: 'action', halign:"center",width:100 },
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

	function edit(id){
		$.magnificPopup.open({
			items: {
				src: "<?php echo site_url('device/manless_gate/edit/') ?>"+id,
			},
			modal: true,
			type: 'ajax',
			tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
			showCloseBtn: false,
		});
	}

	// function deleteData(id){
	// 	url = "<?php echo site_url('device/kiosk/delete/') ?>"+id;
	// 	confirm_delete(url);
	// }

	function change_password(id,reload='')
	{
		var url = "<?php echo site_url('device/manless_gate/update_password/') ?>"+id;
		swal({
			title: "Are you sure want reset password?",
			text: "",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#EF5350",
			confirmButtonText: "Reset",
			cancelButtonText: "Cancel",
			closeOnConfirm: true,
			closeOnCancel: true
		},
		function(isConfirm){
			if (isConfirm) {
				$.ajax({
					url: url,
					type: "GET",
					dataType: "json",
					beforeSend: function(){
						blockUI();
					},
					success: function(json) {
						if (json.code == 200) {
							unblockUI();
							notif(json.header,json.message,json.theme);
							$('#grid').datagrid('load');
						}else{
							unblockUI();
							notif(json.header,json.message,json.theme);
							$('#grid').datagrid('load');
						}
					},

					error: function(){
					},

					complete: function(){
						unblockUI();
					}
				});
			}
		});
	}

	function disable_user(id,msgButton,msg,reload='')
	{
		var url="<?php echo site_url('device/manless_gate/delete/') ?>"+id+"/"+msg;
		swal({
			title: "Are you sure want "+msg+" this user?",
			text: "",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#EF5350",
			confirmButtonText: msgButton,
			cancelButtonText: "Cancel",
			closeOnConfirm: true,
			closeOnCancel: true
		},
		function(isConfirm){
			if (isConfirm) {
				$.ajax({
					url: url,
					type: "GET",
					dataType: "json",
					beforeSend: function(){
						blockUI();
					},
					success: function(json) {
						if (json.code == 200) {
							unblockUI();
							notif(json.header,json.message,json.theme);
							$('#grid').datagrid('load');
						}else{
							unblockUI();
							notif(json.header,json.message,json.theme);
							$('#grid').datagrid('load');
						}
					},

					error: function(json){
					unblockID('#form_add');
					notif('Error','Error please contact admin','alert-styled-left bg-danger');
					},

					complete: function(){
						unblockUI();
					}
				});
			}
		});
	}

	function validasi(msg,reload='')
	{
		swal({
			title:msg,
			text: "",
			type: "warning",
			confirmButtonColor: "#EF5350",
		});
	}

</script>