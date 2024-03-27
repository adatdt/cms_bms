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

			<form method="post" id="form_edit">
				<div class="modal-body">
					<div class="form-group">

						<div class="row" style="padding-bottom:10px">
							<div class="col-sm-12 form-group" style="padding-bottom:5px">
								<label>User Group <font color="red">*</font> </label><input type="text" name="user_group" placeholder="User Group" autocomplete="off" class="form-control" disabled value="<?php echo $user->group_name ?>" >
								<input type="hidden" name="groupId" id="groupId" value="<?php echo $user->user_group_id ?>">
								<input type="hidden" name="id" id="id" value="<?php echo encode($user->id_seq) ?>">
							</div>
						</div>

						<div class="row" >

							<div class="col-sm-3 form-group" id="colMerchant" style="display: none">
								<label>Merchant Name <font color="red">*</font></label>
								<input type="text" class="form-control" id="merchant" name="merchant" placeholder="Merchant Name" value="<?php echo $user->merchant_name ?>" required>
							</div>

							<div class="col-sm-3 form-group" id='colUsername' style="display: none">
								<label>Username <font color="red">*</font></label><input type="text" name="userName" placeholder="Username" autocomplete="off" class="form-control" required value="<?php echo $user->username ?>" disabled> 
							</div>

							<div class="col-sm-3 form-group" id='colEmail' style="display: none">
								<label>Email</label><input type="email" name="email" placeholder="Email" autocomplete="off" class="form-control"  value="<?php echo $user->email ?>" >
							</div> 

							<div class="col-sm-3 form-group" id='colFirstName' style="display: none">
								<label>First Name <font color="red">*</font></label><input type="text" name="firstName" placeholder="First Name" autocomplete="off" class="form-control" required value="<?php echo $user->first_name ?>" >
							</div>
							
							<div class="col-sm-3 form-group" id="colLastName" style="display: none">
								<label>Last Name</label><input type="text" id="lastName" name="lastName" placeholder="Last Name" autocomplete="off" class="form-control"  value="<?php echo $user->last_name ?>">
							</div>

							<div class="col-sm-3 form-group" id="colSheter" style="display: none">
								<label>Shelter <font color="red">*</font></label>
								<select class="form-control" name="shelter" id="shelterId">
								<?php foreach ($shelter as $key => $value) { ?>
									<option value="<?php echo $value->id_seq ?>" <?php echo $value->id_seq==$user->shelter_id?"selected":""; ?>><?php echo $value->shelter_name ?></option>
								 <?php } ?>
								</select>
							</div>

							<div class="col-sm-3 form-group" id="colLane" style="display: none">
								<label>Lane <font color="red">*</font></label>
								<select class="form-control" name="lane" id="lane" required>
									<?php foreach($lane as $lane ) { ?>
									<option value="<?php echo $lane->id_seq; ?>" <?php echo $lane->id_seq==$user->lane_id?"selected":""; ?>><?php echo $lane->lane_name; ?>
									</option>
									<?php } ?>
								</select>
							</div>  

							<div class="col-sm-3 form-group" id='colDeviceTerminal' style="display: none">
								<label>Device POS<font color="red">*</font></label>
								<select class="form-control" name="deviceTerminal" id="deviceTerminal">
									<?php foreach ($deviceTerminal as $key => $value) { ?>
									<option value="<?php echo $value->id_seq ?>" <?php echo $value->id_seq==$user->terminal_id?"selected":""; ?>> <?php echo $value->terminal_name ?></option><?php } ?>
									</select>
							</div>

							<div class="col-sm-3 form-group" id="colPo" style="display: none">
								<label>PO <font color="red">*</font></label>
								<select class="form-control" name="po" id="po" >
									<?php foreach ($po as $key => $value) { ?>
									<option value="<?php echo $value->id_seq ?>" <?php echo $value->id_seq==$user->po_id?"selected":""; ?>>
										<?php echo $value->po_name ?>
									</option>
									<?php } ?>
								</select>
							</div>  

							<div class="col-sm-3 form-group" id="dataTerminal">
								
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
		blockID('#form_edit');
		event.preventDefault();

		$.ajax({
			url: '<?php echo site_url(); ?>configuration/user/update',
			type: "POST",
			data: $("#form_edit").serialize(),
			dataType: 'json',

			success: function(json) {
				if (json.code == 200){
					unblockID('#form_edit');
					close_modal();
					notif(json.header,json.message,json.theme);
					$('#grid').datagrid('load');
				}else{
					unblockUI();
					notif(json.header,json.message,json.theme);
				}
			},

			error: function(json){
				unblockID('#form_edit');
				notif('Error','Error','alert-styled-left bg-danger');
			},

			complete: function(){
				unblockID('#form_edit');
			}
		});
	});


	var userId= $("#groupId").val();

	if(userId=='3' || userId=='6' )
	{
		$("#colUsername").show();
		$("#colEmail").show();
		$("#colFirstName").show();
		$("#colLastName").show();
		$("#colSheter").show();
		$("#colDeviceTerminal").show();
		$("#colPo").hide().html('');
		$("#colLane").hide().html('');
		$("#colMerchant").hide().html('');
	}

	else if ( userId=='7')
	{
		$("#colUsername").show();
		$("#colEmail").show();
		$("#colFirstName").show();
		$("#colLastName").show();
		$("#colSheter").show();
		$("#colDeviceTerminal").show();
		$("#colPo").hide().html('');
		$("#colLane").show();
		$("#colMerchant").hide().html('');
	}
	else if(userId=='8' )
	{
		$("#colUsername").show();
		$("#colEmail").show();
		$("#colFirstName").show();
		$("#colLastName").show();
		$("#colSheter").hide().html('');
		$("#colDeviceTerminal").show();
		$("#colPo").hide().html('');
		$("#colLane").hide().html('');
		$("#colMerchant").hide().html('');
	}
	else if (userId=='5')
	{
		$("#colUsername").show();
		$("#colEmail").show();
		$("#colFirstName").show();
		$("#colLastName").show();
		$("#colSheter").show();
		$("#colDeviceTerminal").hide().html('');
		$("#colPo").show();
		$("#colLane").hide().html('');
		$("#colMerchant").hide().html('');	

	}
	else if (userId=='4')
	{
		$("#colUsername").show();
		$("#colEmail").show();
		$("#colFirstName").show();
		$("#colLastName").show();
		$("#colSheter").hide().html('');
		$("#colDeviceTerminal").show();
		$("#colPo").hide().html('');
		$("#colLane").hide().html('');	
		$("#colMerchant").hide().html('');
	}

	else if (userId=='9')
	{
		$("#colUsername").show();
		$("#colEmail").hide().html('');
		$("#colPassword").hide().html('');
		$("#colFirstName").hide().html('');
		$("#colLastName").hide().html('');
		$("#colSheter").hide().html('');
		$("#colLane").hide().html('');
		$("#colDeviceTerminal").hide().html('');
		$("#colPo").hide().html('');
		$("#colMerchant").show();

		$("#lastName").removeAttr('required');
	}

	// user po
	else if (userId=='11')
	{
		$("#colUsername").show();
		$("#colEmail").show();
		$("#colPassword").show();
		$("#colFirstName").show();
		$("#colLastName").show();
		$("#colSheter").hide().html('');
		$("#colLane").hide().html('');
		$("#colDeviceTerminal").hide().html('');
		$("#colPo").show();
		$("#colMerchant").hide().html('');

		$("#lastName").removeAttr('required');
	}

	else
	{
		$("#colUsername").show();
		$("#colEmail").show();
		$("#colFirstName").show();
		$("#colLastName").show();
		$("#colSheter").hide().html('');
		$("#colDeviceTerminal").hide().html('');
		$("#colPo").hide().html('');
		$("#colLane").hide().html('');
		$("#colMerchant").hide().html('');
	}

	$('#shelterId').on('change',function(){
		var id=$("#shelterId").val();

		$.ajax({
			type:"post",
			url:"<?php echo site_url('configuration/user/dataLane')?>",
			dataType:"json",
			data:"id="+id,
			success:function(x)
			{
				var html="<option value=''>Select</option>";

				for(var i=0; i < x.length; i++)
				{

					html+="<option value='"+x[i].id_seq+"'>"+x[i].lane_name+"</option>";
				}

				$("#lane").html(html);
			}
		});
	});

</script>