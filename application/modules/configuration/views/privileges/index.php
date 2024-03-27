<style>
	.material-switch > input[type="checkbox"] {
	   display: none;   
	}

	.material-switch > label {
	    cursor: pointer;
	    height: 0px;
	    position: relative; 
	    width: 40px;  
	}

	.material-switch > label::before {
	    background: rgb(0, 0, 0);
	    box-shadow: inset 0px 0px 10px rgba(0, 0, 0, 0.5);
	    border-radius: 8px;
	    content: '';
	    height: 10px;
	    margin-top: -5px;
	    position:absolute;
	    opacity: 0.3;
	    transition: all 0.4s ease-in-out;
	    width: 20px;
	}
	.material-switch > label::after {
	    background: rgb(255, 255, 255);
	    border-radius: 30px;
	    box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
	    content: '';
	    height: 15px;
	    left: -4px;
	    margin-top: -5px;
	    position: absolute;
	    top: -3px;
	    transition: all 0.3s ease-in-out;
	    width: 15px;
	}
	.material-switch > input[type="checkbox"]:checked + label::before {
	    background: inherit;
	    opacity: 0.5;
	}
	.material-switch > input[type="checkbox"]:checked + label::after {
	    background: inherit;
	    left: 9px;
	}
</style>

<?php $this->load->view('common/jeasyui'); ?>
<div class="content">
	<div class="row">
		<div class="col-lg-12">
			<div class="panel bg-angkasa2">
				<div class="panel-heading">
					<h6 class="panel-title" style="color: white;font-weight: normal !important;"><i class="icon-user-check position-left"></i> <?php echo $title ?></h6>
				</div>
			</div>
			<div class="panel" id="panel">
				<div id="tb">
					<div class="panel-heading">
						<label>User Group</label>
						<select id="group_name" name="group_name" class="form-control select2 " data-placeholder="Select User Group">
							<option disabled selected hidden>-- SELECT --</option>
							<?php foreach ($group_name as $key => $value) { ?>
								<option value="<?php echo $value->id_seq ?>"><?php echo $value->group_name ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="class="panel-body" style="display: block; padding: 5px">
					<table id="grid"></table>
				</div>
				<div class="table-responsive">
					<div style="padding: 10px;">
						
					</div>
				</div>
			</div>
		</div>
	</div>
<script type="text/javascript" src="<?php echo base_url() ?>assets/common/privileges.js"></script>
<script type="text/javascript">
	$('.select2').select2();

	$("#group_name").change(function(){
		//alert($(this).val());
		var id=$(this).val();
			$('#grid').treegrid({
						// idField:'id',
						// treeField:'name',
						fitColumns : true,
						url:"<?php echo site_url('configuration/privileges/getDetail') ?>",
						treeField : 'name',
        				idField : 'id_seq',
        				scrollbarSize: 0,
				        toolbar     : '#tb',
				        nowrap      : true,
				        height      : 'auto',
				        iconCls:'icon-add',
				        fitColumns:true,
						queryParams:{user_group_id:id},
						columns:[[
						{title:'MENU',field:'name',width:180},
						{title:'ACTION',field:'action',width:180}
						]],
						onLoadSuccess:function(){
							$(window).resize(function(){
								setTimeout(function(){
									$('#grid').datagrid('resize');
								},400)
							});

							$(".nav").click(function(){
								setTimeout(function(){
									$('#grid').datagrid('resize');
								},400)
							});

							// $(".tree-icon").removeClass('tree-folder').removeClass('tree-file');
						},
					});
	});

function update(privilege_id)
{
	//alert(privilege_id);
	privilege_update("<?php echo site_url ('configuration/privileges/update'); ?>",{id : privilege_id});

}

function insert(menu_detail_id, user_group_id, menu_id)
{
	//alert(menu_detail_id+" "+user_group_id+" "+menu_id);
	privilege_update("<?php echo site_url ('configuration/privileges/insert'); ?>",{menu_detail_id:menu_detail_id, user_group_id:user_group_id, menu_id:menu_id});
}
</script>