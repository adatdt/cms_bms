<div class="col-md-8 col-md-offset-2">
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
							<div class="col-sm-3 form-group">
								<label>UID <font color="red">*</font></label>
								<input type="text" name="uid" placeholder="UID" autocomplete="off" class="form-control" value="<?php echo $detail->uid; ?>" required disabled >

							</div>

							<div class="col-sm-3 form-group">
								<label>Driver Name <font color="red">*</font></label>
								<input type="text" name="driver" placeholder="Driver Name" autocomplete="off" class="form-control" required value="<?php echo $detail->driver_name ?>">
							</div>

							<div class="col-sm-3 form-group">
								<label>Driver Phone <font color="red">*</font></label>
								<input type="text" name="phone" placeholder="Driver Phone" autocomplete="off" class="form-control" required value="<?php echo $detail->driver_phone ?>">
							</div>

							<div class="col-sm-3 form-group">
								<label>Bus Name <font color="red">*</font></label>
								<select name="bus" required class="form-control" id="bus">
									<?php foreach ($bus as $bus) {?>
									<option value="<?php echo encode($bus->id_seq); ?>" <?php echo encode($bus->id_seq)==encode($detail->bus_id)?"selected":""; ?> ><?php echo $bus->bus_name; ?></option>
									<?php }?>
								</select>
							</div>

							<div class="col-sm-3 form-group">
								<label>PO Bus <font color="red">*</font></label>
								<input type="text" class="form-control" disabled id="poName" value="<?php echo $detail->po_name ?>">
								<input type="hidden" class="form-control"  id="poId" name="poId" value="<?php echo $detail->po_id ?>">
								<input type="hidden" class="form-control" name="id" value="<?php echo encode($detail->id_seq) ?>">
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
			url: '<?php echo site_url(); ?>po/driver/action_edit',
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

	$("#bus").change(function(){
		var id =$("#bus").val();
		getData(id);
	});

	function getData(id)
	{
		$.ajax({
			type:"post",
			url:"<?php echo site_url('po/driver/getPo')?>",
			dataType:"json",
			data:"id="+id,
			beforeSend: function(){
				blockID('#form_edit');
			},
			success:function(x){
				
				// alert(x.po_name);
				unblockID('#form_edit');
				$("#poName").val(x.po_name);
				$("#poId").val(x.id_seq);
			}

		});	
	}
</script>