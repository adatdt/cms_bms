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
			<form id="form_add" action="">
				<div class="modal-body">
					<div class="form-group">
						<label>Action Name <font color="red">*</font></label>
						<input type="text" class="form-control" autocomplete="off" placeholder="Name" name="name" required>
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
	$( document ).ready(function() {
		$("#form_add").submit(function(event){
			blockID('#form_add');
			event.preventDefault();

			$.ajax({
				url: '<?php echo site_url(); ?>configuration/menu_action/action_add',
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
						unblockID('#form_add');
						notif(json.header,json.message,json.theme);
					}
				},

				error: function(){
					unblockID('#form_add');
				},

				complete: function(){
					unblockID('#form_add');
				}
			});
		});
	});
</script>