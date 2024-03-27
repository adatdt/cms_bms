<?php $this->load->view('common/jeasyui'); ?>

<div class="panel bg-angkasa2">
	<div class="panel-heading ">
		<h5 class="panel-title" ><?php echo $title ?></h5>
	</div>
	<div class="panel-body" style="display: block; padding:5px ">
		<div id="toolbar">
			<div class="row">
				<div class="col col-md-2" style="padding: 5px 15px" >
					<?php if ($add) {
						create_btn(site_url('po/fare/add'),'Add','plus-circle2');
					} ?>
				</div>

				<div class="col col-md-10 " style="padding: 5px 15px" align="right">

					<div class="row">
						<div class="col col-md-3">
							<div class="input-group" align="right" >
								<span class="input-group-addon" >PO</span>
								<select name="po" class="form-control" id="po">
									<option value="">Select</option>
									<?php foreach ($po as $po) {?>
									<option value="<?php echo encode($po->id_seq)?>"><?php echo $po->po_name?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="col col-md-3">
							<div class="input-group" align="right" >
								<span class="input-group-addon" >Bus Type</span>
								<select name="busType" class="form-control" id="busType">
									<option value="">Select</option>
									<?php foreach ($busType as $busType) {?>
									<option value="<?php echo encode($busType->id_seq)?>"><?php echo $busType->type?></option>
									<?php } ?>
								</select>
							</div>
						</div>


						<div class="col col-md-6">
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
			url         : '<?php echo site_url('po/fare/getList'); ?>',
			emptyMsg: 'No Records Found',
			nowrap:false,
			columns:[[

				{ field: 'po_name', title: 'PO', sortable: true, halign:"center",width:70 },
				{ field: 'type', title: 'Type', sortable: true, halign:"center",width:40 },
				{ field: 'route_info', title: 'Route', sortable: true, halign:"center",width:110 },
				{ field: 'price', title: 'Price', sortable: true, halign:"center",width:40, align:"right" },
				{ field: 'action', title: 'action', align:"center",width:40, align:"center" },
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

	$("#po").change(function(){
		searching();
	});

	$("#busType").change(function(){
		searching();
	});

	function searching()
	{
		$('#grid').datagrid('load',{search: $('#search').val(),po:$('#po').val(),busType:$('#busType').val() });
	}

	function edit(id){
		$.magnificPopup.open({
			items: {
				src: "<?php echo site_url('po/fare/edit/') ?>"+id,
			},
			modal: true,
			type: 'ajax',
			tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
			showCloseBtn: false,
		});
	}

	function deleteData(id){
		url = "<?php echo site_url('po/fare/delete/') ?>"+id;
		confirm_delete(url);
	}

	function validasi(msg,reload=''){
		swal({
			title: "Warning, "+msg,
			text: "",
			type: "warning",
			confirmButtonColor: "#EF5350",
			closeOnConfirm: true,
			closeOnCancel: true,
		});
	}

</script>