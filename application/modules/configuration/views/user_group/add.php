<div class="col-md-5 col-md-offset-4">
	<div class="modal-header bg-astra" style="padding:3px">
		<div class="panel-heading bg-angkasa2">
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
			<form method="post" action="" id="form_add">
				<div class="modal-body">
					<div class="form-group">
						<div class="row">
							<div class="col-sm-12 form-group">
								<label>Group Name <font color="red">*</font></label>
								<input type="text" name="group_name" placeholder="Group Name" id="group_name" autocomplete="off" class="form-control" required>
							</div>

							<div class="col-sm-12 form-group">
								<label>Access Type <font color="red">*</font></label>
								<select name="access_type" class="form-control" required>
									<option value="">Select</option>
									<?php foreach($access_type as $access_type) {?>
									<option value="<?php echo encode($access_type->id_seq) ?>"><?php echo $access_type->access_type ?></option>
									<?php } ?>
								</select>
							</div>

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
	$("#form_add").submit(function(event){
		blockID('#form_add');
		event.preventDefault();

		$.ajax({
			url: '<?php echo site_url(); ?>configuration/user_group/action_add',
			type: "POST",
			data: $("#form_add").serialize(),
			dataType: 'json',

			success: function(json) {
				if (json.code == 200){
					unblockID('#form_add');
					close_modal();
					notif(json.header,json.message,json.theme);
					$('#grid').datagrid('load');
				}else{
					unblockUI();
					notif(json.header,json.message,json.theme);
				}
			},

			error: function(){
				notif(json.header,json.message,json.theme);
			},

			complete: function(){
				unblockID('#form_add');
			}
		});
	});
</script>