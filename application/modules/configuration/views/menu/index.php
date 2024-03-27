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

	<div class="panel-body" style="padding:0px">
		<table id="grid" class="easyui-treegrid" singleSelect="true"></table>
	</div>
	<div id="toolbar">
		<div class="panel-heading" style="padding-bottom: 0px">
			<div class="row">
				<div class="col-sm-9 pb">
					<div id="btnAdd">
						<?php if ($add) {
							btn_add(site_url('configuration/menu/add'));
						}?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 -->

 <?php $this->load->view('common/jeasyui'); ?>
<div class="panel bg-angkasa2">
	<div class="panel-heading ">
		<h5 class="panel-title"><?php echo $title ?></h5>
	</div>

	<div class="panel-body " style="display: block; padding:5px">
		<div id="toolbar">
			<div class="row">
				<div class="col col-md-4" style="padding: 5px 15px">
					<?php if ($add) {
						create_btn(site_url('configuration/menu/add'),'Add','plus-circle2');
					} ?>
				</div>
				<div class="col col-md-4 col-md-offset-4" style="padding: 5px 15px">
					<div class="input-group" align="right">
						<!-- <span class="input-group-addon">Search : </span>
						<input type="text" id="search" value="" class="form-control" placeholder="Search...">
						<div class="input-group-btn">
							<button type="submit" onclick="searching()" class="btn bg-angkasa2"><i class="icon-search4"></i></button>
						</div> -->
					</div>
				</div>
			</div>
		</div>
		<table id="grid" class="easyui-treegrid" singleSelect="true"></table>
	</div>

</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#grid').treegrid({
			url:"<?php echo site_url('configuration/menu/getList') ?>",
			treeField : 'name',
			idField : 'id_seq',
			scrollbarSize: 0,
			toolbar     : '#toolbar',
			nowrap      : true,
			height      : 'auto',
			fitColumns  :true,
			columns:[[
				{title:'Menu',field:'name',width:180},
				// {title:'Order',field:'order',width:180},
				{title:'Action',field:'action',align:'center',width:180}
			]],
			onLoadSuccess:function(row,data){

			}
		});
	})
</script>