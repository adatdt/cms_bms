<?php $this->load->view('common/jeasyui'); ?>
<div class="panel bg-angkasa2">
	<div class="panel-heading ">
		<h5 class="panel-title" ><?php echo $title ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
		<div class="heading-elements">
			<ul class="icons-list">
		        <li><a data-action="collapse" ></a></li>
		    </ul>
	    </div>
	</div>

	<div class="panel-body" style="display: block; padding: 5px">
		<div id="toolbar">
			<div class="row">
				<div class="col col-md-4" style="padding: 5px 15px">
					<?php if ($add) {
						create_btn(site_url('configuration/menu/add'),'Add','plus-circle2');
					} ?>
				</div>
				<div class="col col-md-4 col-md-offset-4" style="padding: 5px 15px">
					<div class="input-group" align="right">
					</div>
				</div>
			</div>
		</div>
	
		<table id="grid" toolbar="#toolbar" class="easyui-datagrid" singleSelect="true" ></table>
	</div>

</div>

<script type="text/javascript">
		settingDefaultDatagrid()
	$(document).ready(function(){
		$('#grid').datagrid({
			url         : '<?php echo site_url('master/bus/list'); ?>',
			pagination : true,
			rownumbers : true,
			fitColumns : true,
			emptyMsg: 'No Records Found',
			columns:[[
				{ field: 'plate_number', title: 'Plate Number', width: 100, sortable: true, halign:'center'},
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
</script>