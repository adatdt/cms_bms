<!-- <?php $this->load->view('common/jeasyui'); ?>
<div class="panel panel-astra">
	<div class="panel-heading">
		<h5 class="panel-title" style="color: white;font-weight: normal !important;">
			<?php echo $title ?>
			<a class="heading-elements-toggle">
				<i class="icon-more"></i>
			</a>
		</h5>
	</div>
	<div class="panel-body">
		<table id="grid" toolbar="#toolbar" class="easyui-datagrid" singleselect="true"></table>
	</div>
	<div id="toolbar">
		<div class="panel-heading" style="padding-bottom: 0px">
			<div class="row">
				<div class="col-sm-9 pb">
					<div id="btnAdd">
						<?php if ($add) {
							btn_add(site_url('configuration/menu_action/add'));
						}?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div> -->


<?php $this->load->view('common/jeasyui'); ?>

<div class="panel bg-angkasa2">
	<div class="panel-heading ">
		<h5  class="panel-title"><?php echo $title ?></h5>
	</div>

	<div class="panel-body" style="display: block; padding: 5px">
		<div id="toolbar">
			<div class="row">
				<div class="col col-md-4" style="padding: 5px 15px">
					<?php if ($add) {
						create_btn(site_url('configuration/menu_action/add'),'Add','plus-circle2');
					} ?>
				</div>
				<div class="col col-md-4 col-md-offset-4" style="padding: 5px 25px">
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
	$(".select2").select2();

	settingDefaultDatagrid()
	$(document).ready(function(){
		$('#grid').datagrid({
			url         : '<?php echo site_url('configuration/menu_action/getList'); ?>',
			pagination : true,
			rownumbers : true,
			columns:[[
				{ field: 'name', title: 'Menu', sortable: true,width:100},
				{ field: 'action', title: 'Action', sortable: true,halign: 'center',width:50, align:"center"},
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
			}
		});
	});

	function edit_menu(id){
		$.magnificPopup.open({
			items: {
				src: "<?php echo site_url('configuration/menu_action/edit/') ?>"+id,
			},
			modal: true,
			type: 'ajax',
			tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
			showCloseBtn: false,
		});
	}

	function delete_menu(id){
		url = "<?php echo site_url('configuration/menu_action/delete/') ?>"+id;
		data = {id : id};
		confirm_delete(url);
	}

	document.getElementById('search').onkeydown = function(e) {
		if (e.keyCode == 13 || e.which == 13) {
			searching(e);
		}
	}

	function searching()
	{
		$('#grid').datagrid('load',{search: $('#search').val()});
	}

	function validasi(msg,reload=''){
	swal({
		title:msg,
		text: "",
		type: "warning",
		confirmButtonColor: "#EF5350",
		});
	}
</script>