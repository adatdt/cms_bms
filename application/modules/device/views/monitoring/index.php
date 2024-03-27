<?php $this->load->view('common/jeasyui'); ?>

<style type="text/css">
	.tree-icon{
		display:none;
	}
</style>

<div class="panel bg-angkasa2">
	<div class="panel-heading ">
		<h5 class="panel-title" ><?php echo $title ?></h5>
	</div>
		<div class="panel-body" style="display: block; padding:5px ">
			<div id="toolbar">
				<div class="row">
				<div class="col col-md-3" style="padding: 5px 15px"></div>
				<div class="col col-md-3" style="padding: 5px 15px">
					
					<div class="media-left media-middle">
						<span class="btn bg-success btn-rounded btn-lg btn-icon text-size-large"><i class="icon-arrow-up16"></i></span>
					</div>
					<div class="media-left">
						<h5 class="text-semibold text-success">
							<span id="persen_sukses">0</span> % <small class="display-block"> <span id="angka_sukses">0 / 0</span> Device Active</small>
						</h5>
					</div>

				</div>
				<div class="col col-md-3" style="padding: 5px 15px">
					
					<div class="media-left media-middle">
						<span class="btn bg-danger btn-rounded btn-lg btn-icon text-size-large"><i class="icon-arrow-down16"></i></span>
					</div>
					<div class="media-left">
						<h5 class="text-semibold text-danger">
							<span id="persen_gagal">0</span> % <small class="display-block"> <span id="angka_gagal">0 / 0</span> Device Not Active</small>
						</h5>
					</div>

				</div>
			</div>
			</div>
			<table id="grid" toolbar="#toolbar" class="easyui-treegrid" singleSelect="true"></table>
		</div>
	</div>
</div>

<script type="text/javascript">
	settingDefaultDatagrid()
	$(document).ready(function(){
		mintaData();
	});

	function mintaData(){
		$('#grid').treegrid({
			url:"<?php echo site_url('device/monitoring/getList') ?>",
			treeField : 'perangkat',
			idField : 'id_seq',
			scrollbarSize: 0,
			toolbar     : '#toolbar',
			nowrap      : true,
			height      : 'auto',
			fitColumns  :true,
			columns:[[
				{title:'Device Terminal',field:'perangkat',width:100, 
					formatter:function(value,row,index){
						return '<span class="'+row.iconCls+'"></span> '+value;
					}
				},
				{title:'Name',field:'terminal_name',width:100},
				{title:'Updated On',field:'waktu_updated',width:100},
				{title:'Status',field:'status_device',align:'center',width:100}
			]],
			onLoadSuccess:function(row,data){
				$("#persen_sukses").html(data.detail.percent_active);
				$("#persen_gagal").html(data.detail.percent_not_active);
				$("#angka_sukses").html(data.detail.active_data+' / '+data.detail.count_data);
				$("#angka_gagal").html(data.detail.not_active_data+' / '+data.detail.count_data);
			}
		});
		setTimeout(mintaData, 10000);
	}
</script>