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
				<div class="modal-body ">
					<div class="form-group">
						<div class="row" >
							
<!-- 							<div class="col-sm-6 form-group">
								<label>Airport <font color="red">*</font></label>
								<select name="airport" class="form-control" required>
									<?php foreach($airport as $airport) { ?>
										<option value="<?php echo encode($airport->id_seq); ?>"><?php echo $airport->airport_name; ?></option>
									<?php } ?>
								</select>
							</div> -->

							<div class="col-sm-6 form-group">
								<label>Validator Name <font color="red">*</font></label>
								<input type="text" name="name" placeholder="Name" autocomplete="off" class="form-control" id="name" required>
							</div>

							<div class="col-sm-6 form-group">
								<label>Username <font color="red">*</font></label>
								<input type="text" name="username" placeholder="Username" autocomplete="off" class="form-control" id="username" required>
							</div>

							<div class="col-sm-6 form-group">
								<label>Password <font color="red">*</font></label>
								<input type="password" name="password" placeholder="password" autocomplete="off" class="form-control" required>
							</div>

							<div class="col-sm-6 form-group">
								<label>PO <font color="red">*</font></label>
								<select name="po" placeholder="PO" class="form-control" required>
									<option value="">Select</option>
									<?php foreach($po as $po) { ?>
									<option value="<?php echo encode($po->id_seq) ?>"><?php echo $po->po_name ?> </option>
									<?php } ?>
								</select>
							</div>

							<div class="col-sm-6 form-group">
								<label>Shelter <font color="red">*</font></label>
								<select name="shelter" class="form-control" required>
									<option value="">Select</option>
									<?php foreach($shelter as $shelter ) { ?>
									<option value="<?php echo encode($shelter->id_seq) ?>"><?php echo $shelter->shelter_name ?></option>
									<?php } ?>
								</select>
							</div>

							<div class="col-sm-6 form-group">
								<label>Imei <font color="red">*</font></label>
								<input type="text" name="imei" placeholder="password" autocomplete="off" class="form-control" required>
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
			url: '<?php echo site_url(); ?>device/validator/action_add',
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

	$("#name").on("keyup",function(){
		var x= $('#name').val();
		var y=x.replace(/\s+/g, '_');
		$("#username").val(y.toLowerCase());

	});

</script>