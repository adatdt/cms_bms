<div class="col-md-8 col-md-offset-2">
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
							<div class="col-sm-3 form-group">
								<label>Bus Name <font color="red">*</font></label>
								<input type="text" name="busName" placeholder="Bus Name" autocomplete="off" class="form-control" required value="<?php echo $bus->bus_name; ?>">
								<input type="hidden" name="id" value="<?php echo $bus->id_seq; ?>">
							</div>

							<div class="col-sm-3 form-group">
								<label>Plate Number <font color="red">*</font></label>
								<input type="text" name="plateNumber" placeholder="Plate Number" autocomplete="off" class="form-control" required value="<?php echo $bus->plate_number; ?>">
							</div> 

							<div class="col-sm-3 form-group">
								<label>PO Bus <font color="red">*</font></label>
								<select class="form-control" name="po" id="po" required>
									<option value="">Select</option>
									<?php foreach ($po as $key => $value) { ?>
									<option value="<?php echo $value->id_seq ?>" <?php echo $value->id_seq==$bus->po_id?"selected":""; ?> ><?php echo $value->po_name ?></option>
									 <?php } ?>
								</select>
							</div>

							<div class="col-sm-3 form-group">
								<label>Type <font color="red">*</font></label>
								<select class="form-control" name="type" id="type" required>
									<option value="">Select</option>
									<?php foreach ($type as $key => $value) { ?>
									<option value="<?php echo $value->id_seq ?>" <?php echo $value->id_seq==$bus->bus_type_id?"selected":""; ?>><?php echo $value->type ?></option>
									 <?php } ?>
								</select>
							</div>  

							<div class="col-sm-3 form-group">
								<label>Total Seat <font color="red">*</font></label>
								<input type="number" name="seat" placeholder="Total Seat" autocomplete="off" class="form-control" required  value="<?php echo $bus->total_seat; ?>">
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
	
	$("#form_edit").submit(function(event){
		event.preventDefault();

		$.ajax({
			url: '<?php echo site_url(); ?>po/bus/action_edit',
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