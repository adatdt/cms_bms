<div class="col-md-5 col-md-offset-4">
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
							<div class="col col-md-12"><legend class="text-bold">DEVICE DETAIL</legend></div>

							<div class="col-sm-12 form-group">
								<label>Airport <font color="red">*</font></label>
								<select name="airport_id" class="form-control" required>
									<?php foreach ($airport as $key => $value) { ?>
										<option value="<?php echo encode($value->id_seq) ?>"><?php echo $value->airport_name ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="col-sm-12 form-group">
								<label>Device Name <font color="red">*</font></label>
								<input type="text" name="terminal_name" placeholder="Device Name" autocomplete="off" class="form-control" required>
							</div>

							<div class="col col-md-12"><legend class="text-bold">USER PROFILE</legend></div>

							<div class="col-sm-12 form-group">
								<label>First Name <font color="red">*</font></label>
								<input type="text" name="first_name" placeholder="First Name" autocomplete="off" class="form-control" required>
							</div>

							<div class="col-sm-12 form-group">
								<label>Last Name <font color="red"></font></label>
								<input type="text" name="last_name" placeholder="Last Name" autocomplete="off" class="form-control">
							</div>

							<div class="col-sm-12 form-group">
								<label>Username <font color="red">*</font></label>
								<input type="text" name="username" placeholder="Username" autocomplete="off" class="form-control" id="username" required>
							</div>  

							<div class="col-sm-12 form-group">
								<label>Password <font color="red">*</font></label>
								<input type="password" name="password" placeholder="Password" autocomplete="off" class="form-control" required>
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
			url: '<?php echo site_url(); ?>device/pos/action_add',
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