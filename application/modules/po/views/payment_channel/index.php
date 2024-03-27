<?php $this->load->view('common/jeasyui'); ?>
<div class="panel bg-angkasa2">
	<div class="panel-heading ">
		<h5 class="panel-title" ><?php echo $title ?></h5>
	</div>
	<div class="panel-body" style="display: block; padding:5px ">
		<div id="toolbar">
			<div class="row">
				<div class="col col-md-2" style="padding: 5px 15px">
					<?php if ($add) {
						create_btn(site_url('po/payment_channel/add'),'Add','plus-circle2');
					} ?>
				</div>
				<div class="col col-md-3" style="padding-top: 5px">
					<div class="input-group ">
						<span class="input-group-addon">PO</span>
						<select class="form-control" id="po">
							<?php if($user_po==0){ ?>
								<option value="">Select</option>
								<?php foreach($po as $po){ ?>
									<option value="<?php echo encode($po->id_seq)?>"><?php echo $po->po_name?></option>
								<?php } ?>
							<?php }else{ ?>
								<option value="<?php echo encode($po->id_seq)?>"><?php echo $po->po_name?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col col-md-3" style="padding-top: 5px">
					<div class="input-group ">
						<span class="input-group-addon">Payment Channel</span>
						<select class="form-control" id="pc">
							<option value="">Select</option>
							<?php foreach($pc as $pc){ ?>
								<option value="<?php echo encode($pc->id_seq)?>"><?php echo $pc->payment_channel?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col col-md-4 " style="padding: 5px 15px">
					<div class="input-group" align="right">
						<span class="input-group-addon">Search : </span>
						<input type="text" id="search" value="" class="form-control" placeholder="Search Merchant ID">
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
			url         : '<?php echo site_url('po/payment_channel/getList'); ?>',
			emptyMsg: 'No Records Found',
			columns:[[
				{ field: 'po_name', title: 'PO Bus', sortable: true, halign:"center",width:100 },
				{ field: 'payment_channel', title: 'Payment Channel', sortable: true, halign:"center", width:100},
				{ field: 'mid', title: 'Merchant ID', sortable: true, halign:"center",width:100},
				{ field: 'status', title: 'Status', sortable: false, halign:"center",width:50,align:"center"},
				{ field: 'action', title: 'Action', halign:"center",align:"center",width:100 },
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

	$('#po').on('change', function() {
		searching();
	});

	$('#pc').on('change', function() {
		searching();
	});

	function searching()
	{
		$('#grid').datagrid('load',
			{
				search: $('#search').val(),
				po: $('#po').val(),
				pc: $('#pc').val(),
			}
		);
	}

	function edit(id){
		$.magnificPopup.open({
			items: {
				src: "<?php echo site_url('po/payment_channel/edit/') ?>"+id,
			},
			modal: true,
			type: 'ajax',
			tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
			showCloseBtn: false,
		});
	}

	function deleteData(id){
		url = "<?php echo site_url('po/payment_channel/delete/') ?>"+id;
		confirm_delete(url);
	}
</script>