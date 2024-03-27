<div class="col-md-6 col-md-offset-3">
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
			<form method="post" id="form_edit" >
				<div class="modal-body">
					<div class="form-group">
						<div class="row" >

							<div class="col-sm-3 form-group">
								<label>UID</label>
								<input type="text" name="uid" autocomplete="off" class="form-control" required value="<?php echo $detail->uid; ?>" disabled >
								<input type="hidden" name="id" value="<?php echo encode($detail->id_seq); ?>">
							</div>

							<div class="col-sm-3 form-group">
								<label>PO Bus</label>
								<input type="text" name="po" autocomplete="off" class="form-control" required value="<?php echo $detail->po_name; ?>" disabled>
							</div>

							<div class="col-sm-3 form-group">
								<label>Bus Name</label>
								<input type="text" name="busName" autocomplete="off" class="form-control" required  value="<?php echo $detail->bus_name; ?>" disabled>
							</div>

							<div class="col-sm-3 form-group">
								<label>Type</label>
								<input type="text" name="routeCode" autocomplete="off" class="form-control" required value="<?php echo $detail->type; ?>" disabled>
							</div>

							<div class="col-sm-12 form-group">
								<label>Route</label>
								<select class="form-control" name="route" id="route" required>
									<option value="">Select</option>
									<?php foreach ($route as $key => $value) { ?>
									<option value="<?php echo encode($value->id_seq) ?>" <?php echo encode($value->id_seq)==encode($detail->route_id) ?"selected":""; ?>><?php echo $value->route_info ?></option>
									 <?php } ?>
								</select>

								<input type="hidden" name="routeName" id="routeName" value="<?php echo $detail->route_info ?>">
								<input type="hidden" name="busId" id="busId" value="<?php echo encode($detail->bus_id) ?>">
							</div>

							<?php
								$countShelter=count($shelter);

								$index=1;
								for($i=0;$i<$countShelter;$i++)
								{

								$search=$this->db->query("select * from trx.t_trx_journey_cycle where status=1 and tap_out_id=$detail->id_seq and shelter_order=$index")->row();
							?>
							<div class="col-sm-12 form-group">
								<label>Shelter No <?php echo $index ?></label>
								<select class="form-control" name="shelter[]" id="route" >
									<option value="">Select</option>
									<?php $no=0; foreach($shelter as $shelter[$no] ) {?>
									<option value="<?php echo $shelter[$no]->id_seq ?>"
										<?php 
											if(empty($search->shelter_id))
											{
												$shelterId="";
											}
											else
											{
												$shelterId=$search->shelter_id;
											}

											echo $shelter[$no]->id_seq==$shelterId?"selected":"";
										?>
										><?php echo $shelter[$no]->shelter_name; ?></option>
								<?php $no++; } ?>
								</select>
								<input type="hidden" name="terminalCode[]" value="<?php echo $detail->terminal_code?>">
								<input type="hidden" name="tapOutId[]" value="<?php echo $detail->id_seq?>">
								<input type="hidden" name="shelterOrder[]" value="<?php echo $index ?>">
								<input type="hidden" name="status[]" value="1">
								<input type="hidden" name="createdBy[]" value="<?php echo $this->session->userdata('username') ?>">
								<input type="hidden" name="createdOn[]" value="<?php echo date('Y-m-d H:i:s')?>">								
							</div>
							<?php $index++; } ?>
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
			url: '<?php echo site_url(); ?>transaction/tap_out/action_edit',
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

	$("#route").change(function(){
		var id= $("#route").val();
		getData(id);
		// $("#routeName").val('halo');
	});

	function getData(id)
	{
		$.ajax({
			type:"POST",
			url:"<?php echo site_url(); ?>transaction/tap_out/getNameRoute",
			data:"id="+id,
			dataType:"json",
			success:function(x){
				$("#routeName").val(x);
			},
		});
	}
</script>
