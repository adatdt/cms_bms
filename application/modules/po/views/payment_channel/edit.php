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
							<div class="col-sm-12 form-group">
								<label>PO</label>
								<input type="hidden" name="id" value="<?=encode($detail->id_seq) ?>">
								<select class="form-control" name="po">
									<?php foreach ($po as $key => $value) { ?>
										<option value="<?php echo encode($value->id_seq) ?>" <?php echo $value->id_seq==$detail->po_id?"selected":""; ?> ><?php echo $value->po_name; ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="col-sm-12 form-group">
								<label>Payment Channel</label>
								<select class="form-control" name="pc">
									<?php foreach ($pc as $key => $value) { ?>
										<option value="<?php echo encode($value->id_seq) ?>" <?php echo $value->id_seq==$detail->payment_channel_id?"selected":""; ?> ><?php echo $value->payment_channel; ?></option>
									<?php } ?>
								</select>
							</div>
							
							<div class="col-sm-12 form-group">
								<label>Merchant ID</label>
								<input type="text" value="<?=$detail->mid ?>" name="mid" placeholder="Merchant ID" autocomplete="off" class="form-control">
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
			url: '<?php echo site_url(); ?>po/payment_channel/action_edit',
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