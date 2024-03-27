<div class="col-md-6 col-md-offset-3">
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

			<form method="post" id="form_add">
				<div class="modal-body">
					<div class="form-group">
						<div class="row" >
							<div class="col-sm-6 form-group">
								<label>Airport <font color="red">*</font></label>
								<select name="airport_id" class="form-control" required>
									<?php foreach ($airport as $key => $value) { ?>
										<option value="<?php echo $value->id_seq ?>"><?php echo $value->airport_name ?></option>
									<?php } ?>
								</select>
							</div> 

							<div class="col-sm-6 form-group">
								<label>Terminal Name <font color="red">*</font></label>
								<input type="text" name="terminal_name" placeholder="Name" autocomplete="off" class="form-control" required>
							</div> 

							<div class="col-sm-6 form-group">
								<label>Type <font color="red">*</font></label>
								<select class="form-control" name="terminal_type_id" required>
									<option value="">Select</option>
									<?php foreach ($terminal_type as $key => $value) { ?>
									<option value="<?php echo $value->id_seq ?>"><?php echo $value->terminal_type_name ?></option>
									 <?php } ?>
								</select>
							</div>

							<div class="col-sm-6 form-group">
								<label>Shelter <font color="red">*</font></label>
								<select class="form-control" name="shelter_id" required>
									<option value="">Select</option>
									<?php foreach ($shelter as $key => $value) { ?>
									<option value="<?php echo $value->id_seq ?>"><?php echo $value->shelter_name ?></option>
									 <?php } ?>
								</select>
							</div>

							<div class="col-sm-6 form-group">
								<label>Imei </label>
								<input type="text" name="imei" placeholder="Imei" autocomplete="off" class="form-control">
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
		event.preventDefault();

		$.ajax({
			url: '<?php echo site_url(); ?>device/terminal/action_add',
			type: "POST",
			data: $("#form_add").serialize(),
			dataType: 'json',

			beforeSend: function(){
				blockID('#form_add');
			},

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

			error: function(json){
				unblockID('#form_add');
				notif(json.header,json.message,json.theme);
			},

			complete: function(){
				unblockID('#form_add');
			}
		});
	});

</script>