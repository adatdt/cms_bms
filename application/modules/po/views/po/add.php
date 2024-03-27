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

			<form method="post" id="form_add" enctype="multipart/form-data">
				<div class="modal-body">
					<div class="form-group">
						<div class="row" >
							
							<div class="col-sm-3 form-group">
								<label>Prefix <font color="red">*</font></label>

									<input type="text" name="prefix" placeholder="Ex.AA" autocomplete="off" class="form-control" required>
							</div>

							<div class="col-sm-3 form-group">
								<label>Po Bus <font color="red">*</font></label>
								<input type="text" name="poName" placeholder="PO Bus" autocomplete="off" class="form-control" required>
							</div>
							
							<div class="col-sm-3 form-group">
								<label>PIC Name <font color="red">*</font></label>
								<input type="text" name="picName" placeholder="PIC Name" autocomplete="off" class="form-control" required>
							</div> 

							<div class="col-sm-3 form-group">
								<label>PIC Email <font color="red">*</font></label>
								<input type="email" name="picEmail" placeholder="Email" autocomplete="off" class="form-control" required>
							</div> 

							<div class="col-sm-3 form-group">
								<label>PIC Phone <font color="red">*</font></label>

									<input type="text" name="picPhone" placeholder="Ex. O81XXXXX" autocomplete="off" class="form-control" required>
							</div>

							<div class="col-sm-3 form-group">
								<label>Prefix Integration</label>
									<input type="number" name="prefix_qr" placeholder="Prefix Integration" autocomplete="off" class="form-control">
							</div>
							
							<div class="col-sm-6 form-group">
								<label>PO Address <font color="red">*</font></label>
								<input type="text" name="poAddress" placeholder="Address" autocomplete="off" class="form-control" required>
							</div>
							
							<div class="col-sm-12 form-group">
								<label>PO Image <font color="red">*</font></label>
								<input type="file" name="icon" placeholder="Image" autocomplete="off" class="form-control" id="imgInp">
							</div>

							<div class="col-sm-12 form-group">
								<img id="blah" src="#" alt="your image" style="max-height: 300px;max-width: 300px;display: none" />
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
			url: '<?php echo site_url(); ?>po/po/action_add',
			type: "POST",
			// data: $("#form_add").serialize(),
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