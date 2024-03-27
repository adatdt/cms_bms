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
			<form method="post" id="form_edit" >
				<div class="modal-body">
					<div class="form-group">
						<div class="row" >

							<div class="col-sm-12 form-group">
								<label>Name <font color="red">*</font></label>
								<input type="text" name="name" placeholder="Name" autocomplete="off" class="form-control" value="<?php echo $detail->first_name; ?>" required>

								<input type="hidden" name="id" value="<?php echo $id ?>">
							</div>

							<div class="col-sm-12 form-group">
								<label>Username <font color="red">*</font></label>
								<input type="text" name="username" placeholder="Username" autocomplete="off" class="form-control" value="<?php echo $detail->username; ?>">
							</div>  

							<div class="col-sm-12 form-group">
								<label>Shelter <font color="red">*</font></label>
								<select  name="shelter"  class="form-control">
									<option value="">select</option>
									<?php foreach ($shelter as $shelter ) { ?>
									<option value="<?php echo encode($shelter->id_seq)?>"
										<?php echo $shelter->id_seq==$detail->shelter_id?"selected":"" ?> ><?php echo $shelter->shelter_name; ?></option>
									<?php } ?>
								</select>
							</div>  

							<div class="col-sm-12 form-group">
								<label>Airport <font color="red">*</font></label>
								<select  name="airport" class="form-control">
									<?php foreach ($airport as $airport ) { ?>
									<option value="<?php echo encode ($airport->id_seq) ?>"
										<?php echo $airport->id_seq==$detail->airport_id?"selected":""?> ><?php echo $airport->airport_name ?></option>
									<?php } ?>
								</select>
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
			url: '<?php echo site_url(); ?>device/auto_gate/action_edit',
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
					// close_modal();
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