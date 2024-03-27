<div class="col-md-6 col-md-offset-3">
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
							
							<div class="col-sm-6 form-group">
								<label>Airport <font color="red">*</font></label>
								<select class="form-control" name="airport_id" disabled>
									<?php foreach($airport as $a) {?>
									<option value="<?php echo $a->id_seq; ?>" <?php echo $a->id_seq==$terminal->airport_id?"selected":""; ?> ><?php echo $a->airport_name; ?></option>
									<?php }?>
								</select>
								<input name="id" type="hidden" value="<?php echo $id ?>" >
							</div>

<!-- 							<div class="col-sm-6 form-group">
								<label>Terminal Code</label>
								<input type="text" name="terminal_code" placeholder="Terminal Code" value="<?= $terminal->terminal_code ?>" autocomplete="off" class="form-control" required disabled>
							</div> -->

							<div class="col-sm-6 form-group">
								<label>Type <font color="red">*</font></label>
								<select class="form-control" name="terminal_type_id" required disabled>
									<?php foreach($terminal_type as $t) {?>
									<option value="<?php echo $t->id_seq; ?>" <?php echo $t->id_seq==$terminal->terminal_type_id?"selected":""; ?> ><?php echo $t->terminal_type_name; ?></option>
									<?php }?>
								</select>
							</div>
							
							<div class="col-sm-6 form-group">
								<label>Shelter <font color="red">*</font></label>
								<select class="form-control" name="shelter_id" required disabled>
									<?php foreach($shelter as $s) {?>
									<option value="<?php echo $s->id_seq; ?>" <?php echo $s->id_seq==$terminal->shelter_id?"selected":""; ?> ><?php echo $s->shelter_name; ?></option>
									<?php }?>
								</select>
							</div> 

							<div class="col-sm-6 form-group">
								<label>Terminal Name <font color="red">*</font></label>
								<input type="text" name="terminal_name" placeholder="Terminal Name" value="<?= $terminal->terminal_name ?>" autocomplete="off" class="form-control" required>
							</div>

							<div class="col-sm-6 form-group">
								<label>Imei </label>
								<input type="text" name="imei" placeholder="Imei" value="<?= $terminal->imei ?>" autocomplete="off" class="form-control">
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
			url: '<?php echo site_url(); ?>device/terminal/action_edit',
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