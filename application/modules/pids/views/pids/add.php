<div class="col-md-8 col-md-offset-2">
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
							<div class="col-sm-3">
								<label>Bus Name</label>
								<input type="text" name="busName" placeholder="Bus Name" autocomplete="off" class="form-control" required>
							</div>

							<div class="col-sm-3">
								<label>Plate Number</label>
								<input type="text" name="plateNumber" placeholder="Plate Number" autocomplete="off" class="form-control" required>
							</div> 

							<div class="col-sm-3">

								<label>PO Bus</label>
								<select class="form-control" name="po" id="po" required>
									<?php foreach ($po as $key => $value) { ?>
									<option value="<?php echo $value->id_seq ?>"><?php echo $value->po_name ?></option>
									 <?php } ?>
								</select>
							</div>

							<div class="col-sm-3">
								<label>Type</label>
								<select class="form-control" name="type" id="type" required>
									<?php foreach ($type as $key => $value) { ?>
									<option value="<?php echo $value->id_seq ?>"><?php echo $value->type ?></option>
									 <?php } ?>
								</select>

							</div>  

							<div class="col-sm-3">
								<label>Total Seat</label>
								<input type="number" name="seat" placeholder="Total Seat" autocomplete="off" class="form-control" required>
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
			url: '<?php echo site_url(); ?>po/bus/action_add',
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