<div class="col-md-5 col-md-offset-4">
	<div class="modal-header bg-astra" style="padding:2px">
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
			<form method="post" id="form_edit">
				<div class="modal-body">
					<div class="form-group">
						<div class="row" >
							
							<div class="col-sm-12 form-group">
								<label>Airport <font color="red">*</font></label>
								<select class="form-control" name="airport_id" required>
									<?php foreach($airport as $a) {?>
									<option value="<?php echo $a->id_seq; ?>" <?php echo $a->id_seq==$terminal->airport_id?"selected":""; ?> ><?php echo $a->airport_name; ?></option>
									<?php }?>
								</select>
								<input name="id" type="hidden" value="<?php echo $id ?>" >
							</div>

							<div class="col-sm-12 form-group">
								<label>Terminal Name <font color="red">*</font></label>
								<input type="text" name="terminal_name" placeholder="Terminal Name" value="<?= $terminal->terminal_name ?>" autocomplete="off" class="form-control" required>
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
	
	$("#form_edit").submit(function(event){
		event.preventDefault();

		$.ajax({
			url: '<?php echo site_url(); ?>device/pos/action_edit',
			type: "POST",
			data: $("#form_edit").serialize(),
			dataType: 'json',

			beforeSend: function(){
				blockID('#form_edit');
			},

			success: function(json) {
				if (json.code == 200){
					unblockID('#form_edit');
					close_modal();
					notif(json.header,json.message,json.theme);
					$('#grid').datagrid('load');
				}
				else
				{
					unblockID('#form_edit');
					notif(json.header,json.message,json.theme);
				}
			},

			error: function(){
				unblockID('#form_edit');
			},

			complete: function(){
				unblockID('#form_edit');
			}
		});
	});
</script>