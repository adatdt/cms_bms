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
							
							<div class="col-sm-3">
								<label>Po Code</label>
								<input type="text"   autocomplete="off" class="form-control" value="<?php echo $po->po_code; ?>" disabled>
								<input type="hidden" name="id" value="<?php echo encode($po->id_seq); ?>">
							</div>

							<div class="col-sm-3">
								<label>Po Bus</label>
								<input type="text" name="poName" placeholder="PO Bus" autocomplete="off" class="form-control" value="<?php echo $po->po_name; ?>" required>
							</div>
							
							<div class="col-sm-3">
								<label>PIC Name</label>
								<input type="text" name="picName" placeholder="PIC Name" autocomplete="off" class="form-control" value="<?php echo $po->pic_name; ?>" required>
							</div> 

							<div class="col-sm-3">
								<label>PIC Email</label>
								<input type="email" name="picEmail" placeholder="Email" autocomplete="off" class="form-control" value="<?php echo $po->pic_email; ?>" required>
							</div> 

							<div class="col-sm-3">
								<label>PIC Phone</label>
								<input type="text" name="picPhone" placeholder="PIC Phone" autocomplete="off" class="form-control" value="<?php echo $po->pic_phone; ?>" required>
							</div>
							<div class="col-sm-9">
								<label>PO Address</label>
								<input type="text" name="poAddress" placeholder="Address" autocomplete="off" class="form-control" value="<?php echo $po->address; ?>" required>
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
			url: '<?php echo site_url(); ?>master/po/action_edit',
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