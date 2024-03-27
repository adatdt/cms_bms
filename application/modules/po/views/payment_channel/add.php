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

			<form method="post" id="form_add" enctype="multipart/form-data">
				<div class="modal-body">
					<div class="form-group">
						<div class="row" >							
							<div class="col-sm-12 form-group">
								<label>PO</label>
								<select class="form-control" name="po" required>
									<option value="">Select</option>
									<?php foreach ($po as $key => $value) { ?>
										<option value="<?php echo encode($value->id_seq) ?>"><?php echo $value->po_name ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="col-sm-12 form-group">
								<label>Payment Channel</label>
								<select class="form-control" name="pc" required>
									<option value="">Select</option>
									<?php foreach ($pc as $key => $value) { ?>
										<option value="<?php echo encode($value->id_seq) ?>"><?php echo $value->payment_channel ?></option>
									<?php } ?>
								</select>
							</div>
							
							<div class="col-sm-12 form-group">
								<label>Merchant ID</label>
								<input type="text" name="mid" placeholder="Merchant ID" autocomplete="off" class="form-control">
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
			url: '<?php echo site_url(); ?>po/payment_channel/action_add',
			type: "POST",
			data: new FormData($('form')[0]),
			processData: false,
        	contentType: false,
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
				notif('Error','Error failed add data','alert-styled-left bg-danger');
			},

			complete: function(){
				unblockID('#form_add');
			}
		});
	});

	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function(e) {
				$('#blah').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	$("#imgInp").change(function() {
		$('#blah').show();
		readURL(this);
	});
</script>