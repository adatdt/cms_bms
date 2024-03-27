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

						<div class="row">
							<div class="col-sm-12" style="padding-bottom:5px">
								<label>User Group</label><input type="text" name="user_group" placeholder="User Group" autocomplete="off" class="form-control" disabled value="<?php echo $user->group_name ?>" >
								<input type="hidden" name="groupId" id="groupId" value="<?php echo $user->user_group_id ?>">
								<input type="hidden" name="id" id="id" value="<?php echo encode($user->id_seq) ?>">
							</div>
						</div>

						<div class="row" >
							<div class="col-sm-3" id='colUsername'></div>

							<div class="col-sm-3" id='colEmail'></div> 

							<div class="col-sm-3" id='colFirstName' style="display: none"></div>
							
							<div class="col-sm-3" id="colLastName" style="display: none"></div>

							<div class="col-sm-3" id="colSheter" style="display: none"></div>

							<div class="col-sm-3" id="colLane" style="display: none"></div>  

							<div class="col-sm-3" id='colDeviceTerminal' style="display: none"></div>

							<div class="col-sm-3" id="colPo" style="display: none"></div>  

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
				notif(json.header,json.message,json.theme);
			},

			complete: function(){
				unblockID('#form_edit');
			}
		});
	});

	var valLastName="<?php echo $user->last_name ?> ";
	var valFirstName="<?php echo $user->first_name ?> ";
	var valUserName="<?php echo $user->username ?>"

	var userName='<label>Username</label><input type="text" name="userName" placeholder="Username" autocomplete="off" class="form-control" required value="'+valUserName+'" disabled> ';

	var email='<label>Email</label><input type="email" name="email" placeholder="Email" autocomplete="off" class="form-control" required value="<?php echo $user->email ?>">';

	var firstName='<label>First Name</label><input type="text" name="firstName" placeholder="First Name" autocomplete="off" class="form-control" required value="'+valFirstName+'" >';

	var lastName ='<label>Last Name</label><input type="text" name="lastName" placeholder="Last Name" autocomplete="off" class="form-control" required value="'+valLastName+'">';

	var shelter='<label>Shelter</label><select class="form-control" name="shelter" id="shelterId">'+
							'<?php foreach ($shelter as $key => $value) { ?>
								<option value="<?php echo $value->id_seq ?>" <?php echo $value->id_seq==$user->shelter_id?"selected":""; ?>><?php echo $value->shelter_name ?></option> <?php } ?>'+
						'</select>';

	var deviceTerminal='<label>Terminal</label><select class="form-control" name="deviceTerminal" id="deviceTerminal">'+
							'<?php foreach ($deviceTerminal as $key => $value) { ?>
								<option value="<?php echo $value->id_seq ?>" <?php echo $value->id_seq==$user->terminal_id?"selected":""; ?>> <?php echo $value->terminal_name ?></option><?php } ?>'+
						'</select>';
	var po='<label>PO</label><select class="form-control" name="po" id="po" >'+
							'<?php foreach ($po as $key => $value) { ?>
								<option value="<?php echo $value->id_seq ?>" <?php echo $value->id_seq==$user->po_id?"selected":""; ?>><?php echo $value->po_name ?></option><?php } ?>
						</select>';

	var lane='<label>Lane</label>'+
			'<select class="form-control" name="lane" id="lane" required> <?php foreach($lane as $lane ) { ?>'+
			'<option value="<?php echo $lane->id_seq; ?>" <?php echo $lane->id_seq==$user->lane_id?"selected":""; ?>><?php echo $lane->lane_name; ?></option>'+
			'<?php } ?> </select>';


	var userId= $("#groupId").val();

	if(userId=='3' || userId=='6' )
	{
		$("#colUsername").show().html(userName);
		$("#colEmail").show().html(email);
		$("#colFirstName").show().html(firstName);
		$("#colLastName").show().html(lastName);
		$("#colSheter").show().html(shelter);
		$("#colDeviceTerminal").show().html(deviceTerminal);
		$("#colPo").hide().html('');
		$("#colLane").hide().html('');
	}

	else if ( userId=='7')
	{
		$("#colUsername").show().html(userName);
		$("#colEmail").show().html(email);
		$("#colFirstName").show().html(firstName);
		$("#colLastName").show().html(lastName);
		$("#colSheter").show().html(shelter);
		$("#colDeviceTerminal").show().html(deviceTerminal);
		$("#colPo").hide().html('');
		$("#colLane").show().html(lane);
	}
	else if(userId=='8' )
	{
		$("#colUsername").show().html(userName);
		$("#colEmail").show().html(email);
		$("#colFirstName").show().html(firstName);
		$("#colLastName").show().html(lastName);
		$("#colSheter").hide().html('');
		$("#colDeviceTerminal").show().html(deviceTerminal);
		$("#colPo").hide().html('');
		$("#colLane").hide().html('');
	}
	else if (userId=='5')
	{
		$("#colUsername").show().html(userName);
		$("#colEmail").show().html(email);
		$("#colFirstName").show().html(firstName);
		$("#colLastName").show().html(lastName);
		$("#colSheter").show().html(shelter);
		$("#colDeviceTerminal").hide().html('');
		$("#colPo").show().html(po);
		$("#colLane").hide().html('');	
	}
	else if (userId=='4')
	{
		$("#colUsername").show().html(userName);
		$("#colEmail").show().html(email);
		$("#colFirstName").show().html(firstName);
		$("#colLastName").show().html(lastName);
		$("#colSheter").hide().html('');
		$("#colDeviceTerminal").show().html(deviceTerminal);
		$("#colPo").hide().html('');
		$("#colLane").hide().html('');	
	}
	else
	{
		$("#colUsername").show().html(userName);
		$("#colEmail").show().html(email);
		$("#colFirstName").show().html(firstName);
		$("#colLastName").show().html(lastName);
		$("#colSheter").hide().html('');
		$("#colDeviceTerminal").hide().html('');
		$("#colPo").hide().html('');
		$("#colLane").hide().html('');
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