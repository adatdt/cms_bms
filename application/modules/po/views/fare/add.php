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
								<label>PO <font color="red">*</font></label>
								<select class="form-control" name="po_id" required>
									<option value="">Select</option>
									<?php foreach ($po as $key => $value) { ?>
										<option value="<?php echo encode($value->id_seq) ?>"><?php echo $value->po_name ?></option>
									<?php } ?>
								</select>
							</div>
							
							<div class="col-sm-6 form-group">
								<label>Type <font color="red">*</font></label>
								<select class="form-control" name="bus_type_id" required >
									<option value="">Select</option>
									<?php foreach ($type as $key => $value) { ?>
										<option value="<?php echo encode($value->id_seq) ?>"><?php echo $value->type ?></option>
									<?php } ?>
								</select>
							</div> 

							<div class="col-sm-6 form-group">
								<label>Route <font color="red">*</font></label>
								<select class="form-control" name="route_id" required>
									<option value="">Select</option>
									<?php foreach ($route as $key => $value) { ?>
										<option value="<?php echo encode($value->id_seq) ?>"><?php echo $value->route_info ?></option>
									<?php } ?>
								</select>
							</div> 

							<div class="col-sm-6 form-group">
								<label>Price <font color="red">*</font></label>
								<input type="number" min="0" name="price" placeholder="Price" autocomplete="off" class="form-control" required>
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

	$("#form_add").submit(function(event){
		event.preventDefault();

		$.ajax({
			url: '<?php echo site_url(); ?>po/fare/action_add',
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