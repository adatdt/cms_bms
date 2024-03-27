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
			<form method="post" id="change_password">
				<div class="modal-body ">
					<div class="form-group">
						<div class="row" >
							<div class="col-sm-12 form-group">
								<label>Old Password <font color="red">*</font></label>
								<input type="password" name="oldPassword" placeholder="password" autocomplete="off" class="form-control" required >
							</div>

							<div class="col-sm-12 form-group">
								<label>New Password <font color="red">*</font></label>
								<input type="password" name="newPassword" id="newPassword" placeholder="password" autocomplete="off" class="form-control" required >
								<!-- <input id="open" type="checkbox"> -->
							</div>

							<div class="col-sm-12 form-group">
								<label>Repeat Password <font color="red">*</font></label>
								<input type="password" name="repeatPassword" placeholder="password" autocomplete="off" class="form-control" required >

							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn bg-angkasa2">Submit</button>
				</div>
			</form>

		</div>
	</div>
</div>

<script type="text/javascript">

	$("#change_password").submit(function(event){
		event.preventDefault();

		$.ajax({
			url: '<?php echo site_url(); ?>profile/action_change_password',
			type: "POST",
			data: $("#change_password").serialize(),
			dataType: 'json',

			beforeSend: function(){
				blockID('#change_password');
			},

			success: function(json) {
				if (json.code == 200){
					unblockID('#change_password');
					close_modal();
					notif(json.header,json.message,json.theme);
					// $('#grid').datagrid('load');
				}else{
					unblockUI();
					notif(json.header,json.message,json.theme);
				}
			},

			error: function(json){
				unblockID('#change_password');
				notif(json.header,json.message,json.theme);
			},

			complete: function(){
				unblockID('#change_password');
			}
		});
	});

	$("#open1").change(function(){

		if(this.checked)
		{
			$('#newPassword').attr('type','text');
		}
		else
		{
			$('#newPassword').attr('type','password');
		}
		// if($(this).is('.glyphicon-eye-close')){
		// 	$(this).removeClass().addClass('glyphicon glyphicon-eye-open');
		// 	$('#newPassword').attr('type','text');
		// }
		// else
		// {
		// 	$(this).removeClass().addClass('glyphicon glyphicon-eye-close');	
		// 	$('#newPassword').attr('type','password');
		// }
	});

</script>