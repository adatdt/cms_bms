<style type="text/css">
	select { vertical-align: top; }
</style>
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
								<label>PO <font color="red">*</font></label>
								<select class="form-control" name="po_id" required disabled>
									<option value="">Select</option>
									<?php foreach($po as $po) {?>
									<option value="<?php echo encode($po->id_seq); ?>" <?php echo encode($po->id_seq)==encode($fare->po_id)?"selected":""; ?> ><?php echo $po->po_name; ?></option>
									<?php }?>
								</select>
								<input name="id" type="hidden" value="<?php echo $id ?>" >
							</div>

							<div class="col-sm-6 form-group">
								<label>Type <font color="red">*</font></label>
								<select class="form-control" name="bus_type_id" required disabled>
									<option value="">Select</option>
									<?php foreach($type as $type) {?>
									<option value="<?php echo encode($type->id_seq); ?>" <?php echo encode($type->id_seq)==encode($fare->bus_type_id)?"selected":""; ?> ><?php echo $type->type; ?></option>
									<?php }?>
								</select>
							</div>
							
							<div class="col-sm-6 form-group">
								<label>Route <font color="red">*</font></label>
								<select class="form-control" name="route_id" required disabled>
									<option value="">Select</option>
									<?php foreach($route as $route) {?>
									<option value="<?php echo encode($route->id_seq); ?>" 
										<?php echo encode($route->id_seq)==encode($fare->route_id)?"selected":"" ?> >
										<?php echo $route->route_info; ?>
									</option>
									<?php }?>
								</select>
							</div> 

							<div class="col-sm-6 form-group">
								<label>Price <font color="red">*</font></label>
								<input type="number" min="0" name="price" placeholder="Price" value="<?= $fare->price ?>" autocomplete="off" class="form-control" required>
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
			url: '<?php echo site_url(); ?>po/fare/action_edit2',
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