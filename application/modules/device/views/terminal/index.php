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
				<div class="col col-md-5" style="padding: 5px 15px">
					<?php if ($add) {
						create_btn(site_url('device/terminal/add'),'Add','plus-circle2');
					} ?>
				</div>
				<div class="col col-md-3" style="padding: 5px 15px">
					<div class="input-group">
						<span class="input-group-addon">Shelter : </span>
						<select class="form-control" id="shelter">
							<option value="">All Shelter</option>
							<?php foreach ($shelter as $key => $value) { ?>
								<option value="<?=$value->id_seq ?>"><?=$value->shelter_name ?></option>
							<?php } ?>
						</select>
					</div>
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

	settingDefaultDatagrid()
	$(document).ready(function(){
		$('#grid').datagrid({
			url         : '<?php echo site_url('device/terminal/getList'); ?>',
			fitColumns : true,
			emptyMsg: 'No Records Found',
			columns:[[

				// { field: 'terminal_code', title: 'Code', sortable: true,width:75 },
				{ field: 'terminal_name', title: 'Name', sortable: true,width:100 },
				{ field: 'terminal_type_name', title: 'Type', sortable: true,width:100 },
				{ field: 'shelter_name', title: 'Shelter', sortable: true,width:80 },
				{ field: 'imei', title: 'Imei', sortable: true,width:100 },
				{ field: 'action', title: 'action',width:80,align:'center' },
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

	document.getElementById('search').onkeydown = function(e) {
		if (e.keyCode == 13 || e.which == 13) {
			searching(e);
		}
	}

	$("#shelter").on("change",function(){
		$('#grid').datagrid('load',{shelter: $("#shelter").val()});	
	});

	function searching()
	{
		$('#grid').datagrid('load',{search: $('#search').val()});
	}

	function edit(id){
		$.magnificPopup.open({
			items: {
				src: "<?php echo site_url('device/terminal/edit/') ?>"+id,
			},
			modal: true,
			type: 'ajax',
			tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
			showCloseBtn: false,
		});
	}

	function deleteData(id){
		url = "<?php echo site_url('device/terminal/delete/') ?>"+id;
		confirm_delete(url);
	}


</script>