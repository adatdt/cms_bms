<div class="col-md-5 col-md-offset-4">
	<div class="modal-header bg-angkasa2" style="padding:3px">
		<div class="panel-heading ">
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
							<!-- <div class="col-sm-3">
								<label>Route Code</label>
								<input type="text" name="routeCode" autocomplete="off" class="form-control" disabled value="<?php echo $route->route_code ?>">
								
							</div> -->
							<div class="col-sm-12 form-group">
								<label>Airport <font color="red">*</font></label>
								<select class="form-control" name="airportId" id="airport" required>
									<?php foreach ($airport as $key => $value) { ?>
									<option value="<?php echo $value->id_seq ?>" <?php echo $value->id_seq==$route->id_seq?"selected":""; ?> ><?php echo $value->airport_name ?></option>
									 <?php } ?>
								</select>
							</div> 

							<div class="col-sm-12 form-group">
								<label>Route <font color="red">*</font></label>
								<input type="text" name="route" placeholder="Route Name" autocomplete="off" class="form-control" required  value="<?php echo $route->route_info ?>" >
								<input type="hidden" name="routeId"  value="<?php echo encode($route->id_seq) ?>">
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
			url: '<?php echo site_url(); ?>po/route/action_edit',
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