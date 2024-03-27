<div class="col-md-8 col-md-offset-2">
	<div class="modal-header bg-angkasa2" style="padding:3px">
		<div class="panel-heading">
			<h5 class="panel-title" style="color: white;font-weight: normal !important;">
				<?php echo $title ?>
			</h5>
			<div class="heading-elements">
				<ul class="icons-list">
					<li><a data-action="close" onclick="close_modal()"></a></li>
				</ul>
			</div>
		</div>
		<div class="panel-body">
			<form method="post" action="" id="form_edit">
				<div class="modal-body">
					<div class="form-group">
						<div class="row">
							<div class="col-sm-6">
								<label>Menu Name <font color="red">*</font></label>
								<input type="text" name="name" value="<?php echo $row->name?>" class="form-control required" placeholder="Menu Name" id="name" required>
								<input type="hidden" value="<?php echo encode($id) ?>" name="id">
							</div>
							<div class="col-sm-3">
								<label>Icon</label>
								<input type="text" name="icon" id="icon" class="form-control" value="<?php echo $row->icon ?>">
								<!-- <select class="form-control" name="icon" id="icon">
									<?php foreach ($icon as $key => $value) {
										$selected = '';
										if ($value->icon_name == $row->icon) {
											$selected = "selected";
										}
										?>
										<option <?php echo $selected ?> value="<?= $value->icon_name ?>"><?= $value->icon_name ?></option>
									<?php } ?>
								</select> -->
							</div>
							<div class="col-sm-3">
								<label>Order <font color="red">*</font></label>
								<input type="number" min="1" name="order" class="form-control required" placeholder="Order" id="order_int" aria-required="true" value="<?php echo $row->menu_order?>" required>
							</div>
						</div>
					</div> 
					<div class="form-group">
						<div class="row">
							<div class="col-sm-6">
								<label>URL</label>
								<div class="input-group input-group-sm">
									<span class="input-group-addon"><b><?php echo base_url() ?></b></span>
									<input type="text" name="slug" class="form-control" placeholder="URL" id="slug" value="<?php echo $row->slug?>">
								</div>
							</div>
							<div class="col-sm-6">
								<label>Parent</label>
								<input name="parent" id="parent" class="form-control easyui-combotreegrid" singleSelect="true"style="width: 100%;" value="<?php echo $row->parent_id?>"></input>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-sm-12">
								<label>Menu Action <font color="red">*</font></label>
								<select class="form-control select2" multiple="multiple" data-placeholder="Select a State"
								style="width: 100%;" name="menu_action[]" required>
								<?php foreach ($menu_action as $key => $value) {
									$selected = '';
									foreach ($action as $key => $act) {
										if ($value->id_seq == $act->id_seq) {
											$selected = "selected";
										}
									}
									?>
									<option <?php echo $selected ?> value="<?php echo $value->id_seq ?>"><?= $value->name ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<!-- <button type="button" class="btn btn-link" onclick="close_modal()">Cancel</button> -->
					<button type="submit" class="btn bg-angkasa2">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	function iconFormat(icon) {
		return "<i class='icon-" + icon.text + "'></i> " + icon.text;
	}

	$(".select2").select2();
	// $("#icon").select2({
	// 		dropdownParent: $('#form_edit'),
 //            templateResult: iconFormat,
 //            templateSelection: iconFormat,
 //            escapeMarkup: function(m) { return m; }
 //        });
	
	$("#form_edit").submit(function(event){
		event.preventDefault();

		$.ajax({
			url: '<?php echo site_url(); ?>configuration/menu/action_edit',
			type: "POST",
			data: $("#form_edit").serialize(),
			dataType: 'json',

			beforeSend: function(){
				blockID('#form_edit');
			},

			success: function(json) {
				if (json.code == 200){
					unblockID('#form_add');
					close_modal();
					notif(json.header,json.message,json.theme);
					$('#grid').treegrid('load');
				}else{
					unblockID('#form_edit');
					notif(json.header,json.message,json.theme);
				}
			},

			error: function(){
				unblockID('#form_edit');
				notif('Error','error failed insert data','alert-styled-left bg-danger');
			},

			complete: function(){
				unblockID('#form_edit');
			}
		});
	});

	$(document).ready(function(){
		$('#parent').combotreegrid({
			url:"<?php echo site_url('configuration/menu/getList') ?>",
			treeField : 'name',
			idField : 'id_seq',
			scrollbarSize: 0,
			nowrap      : true,
			height      : 'auto',
			fitColumns:true,
			columns:[[
				{title:'Menu',field:'name',width:180}
			]],

			loadFilter :function(data){
				data.rows.push({id_seq:0,name:"No Parent"})
				return data;
			}
		});
	})
</script>