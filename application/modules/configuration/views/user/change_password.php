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
			<form method="post" action="" id="form_changepass">
				<div class="modal-body">
					<div class="form-group">
						<div class="row">
							<div class="col-sm-12">
	<!-- 							<label>Password</label>
								<input type="password" name="password" id="password" placeholder="New Password" autocomplete="off" class="form-control" required>
								<input type="hidden" name="id" value="<?php echo $id ?>"> -->
								<div class="alert alert-warning">Are you sure</div>
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
	$("#form_changepass").submit(function(event){
		blockID('#form_changepass');
		event.preventDefault();

		$.ajax({
			url: '<?php echo site_url(); ?>configuration/user/update_password',
			type: "POST",
			data: $("#form_changepass").serialize(),
			dataType: 'json',

			success: function(json) {
				if (json.code == 200){
					unblockID('#form_changepass');
					close_modal();
					notif(json.header,json.message,json.theme);
					$('#grid').datagrid('load');
				}else{
					unblockUI();
					notif(json.header,json.message,json.theme);
				}
			},

			error: function(){
				unblockID('#form_changepass');
			},

			complete: function(){
				unblockID('#form_changepass');
			}
		});
	});
</script>