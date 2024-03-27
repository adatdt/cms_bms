<?php $this->load->view('common/jeasyui'); ?>
<div class="panel bg-angkasa2">
	<div class="panel-heading ">
		<h5 class="panel-title" ><?php echo $title ?></h5>
	</div>

	<div class="panel-body" style="display: block; padding:5px">
		<div id="toolbar">
			<div class="row">
				<div class="col col-md-2" style="padding: 5px 15px">
					<?php if ($add) {
						create_btn(site_url('configuration/user/add'),'Add','plus-circle2');
					} ?>
				</div>
				<div class="col col-md-10" style="padding: 5px 15px">
					<div class="row">

						<div class="col col-md-4 " align="right">
						</div>

						<div class="col col-md-4 " align="right">
							<div class="input-group" align="right">
								<span class="input-group-addon">User Group</span>
								<select id="user_group" class="form-control">
									<option value="">Select</option>
									<?php foreach($user_group_data as $group_user) {?>
									<option value="<?php echo encode($group_user->id_seq) ?>"><?php echo $group_user->group_name ?></option>
									<?php } ?>
								</select>
							</div>		
						</div>

						<div class="col col-md-4" >
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
	settingDefaultDatagrid()
	$(document).ready(function(){
		$('#grid').datagrid({
			url         : '<?php echo site_url('configuration/user/getList'); ?>',
			pagination : true,
			rownumbers : true,
			fitColumns : true,
			emptyMsg: 'No Records Found',
			columns:[[
				{ field: 'username', title: 'Username', width: 100, sortable: true, halign:'center'},
				{ field: 'full_name', title: 'Name', width: 100, sortable: true, halign:'center'},
				// { field: 'email', title: 'Email', width: 100, sortable: false , halign:'center'},
				{ field: 'group_name', title: 'Group Name', width: 100, sortable: false , halign:'center'},
				{ field: 'status', title: 'Status', sortable: false , align:'center'},
				{ field: 'action', title: 'Action', width: 100, sortable: false , halign:'center'},
			]],

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

				// var dg = $(this);
				// var opts = $(dg).datagrid('options');
				// opts.finder.getTr(dg[0], -1, 'allbody').css('height', '50px');

			}
		});
	});

	function edit_user(id){
		$.magnificPopup.open({
			items: {
				src: "<?php echo site_url('configuration/user/edit/') ?>"+id,
			},
			modal: true,
			type: 'ajax',
			tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
			showCloseBtn: false,
		});
	}

	// function change_password(id){
	// 	$.magnificPopup.open({
	// 		items: {
	// 			src: "<?php echo site_url('configuration/user/change_password/') ?>"+id,
	// 		},
	// 		modal: true,
	// 		type: 'ajax',
	// 		tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
	// 		showCloseBtn: false,
	// 	});
	// }

	function change_password(id,reload='')
	{
		var url = "<?php echo site_url('configuration/user/update_password/') ?>"+id;
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

	// function delete_user(id){
	// 	url = "<?php echo site_url('configuration/user/delete/') ?>"+id;
	// 	confirm_delete(url);
	// }

	function disable_user(id,msgButton,msg,reload='')
	{
		var url="<?php echo site_url('configuration/user/delete/') ?>"+id+"/"+msg;
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


	document.getElementById('search').onkeydown = function(e) {
		if (e.keyCode == 13 || e.which == 13) {
			searching(e);
		}
	}

	$("#user_group").change(function(){
		searching();
	});

	function searching()
	{
		$('#grid').datagrid('load',{search: $('#search').val(),user_group: $('#user_group').val()});
	}
</script>