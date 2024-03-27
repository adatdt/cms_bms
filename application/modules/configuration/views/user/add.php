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

			<form method="post" id="form_add">
				<div class="modal-body">
					<div class="form-group">

						<div class="row" style="padding-bottom:10px">
							<div class="col-sm-12 form-group" style="padding-bottom:5px">
								<label>User Group <font color="red">*</font></label>
								<select class="form-control" name="user_group" id="userGroup">
									<option value="">Select</option>
									<?php foreach ($user_group as $key => $value) { ?>
									<option value="<?php echo $value->id_seq ?>"><?php echo $value->group_name ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

						<div class="row" >

							<div class="col-sm-3 form-group" id="colMerchant" style="display: none">
								<label>Merchant Name <font color="red">*</font></label>
								<input type="text" class="form-control" id="merchant" name="merchant" placeholder="Merchant Name">
							</div>

							<div class="col-sm-3 form-group" id='colUsername'>
								<label>Username <font color="red">*</font></label><input type="text" name="userName" placeholder="Username" id="userName" autocomplete="off" class="form-control" required>
							</div>

							<div class="col-sm-3 form-group" id='colEmail'>
								<label>Email</label><input type="email" name="email" placeholder="Email" autocomplete="off" class="form-control"  id="email">
							</div> 

							<div class="col-sm-3 form-group" id='colPassword'>
								<label>Password <font color="red">*</font></label><input type="password" name="password" placeholder="Password" autocomplete="off" class="form-control" required id="password">
							</div>

							<div class="col-sm-3 form-group" id='colFirstName' style="display: none">
								<label>First Name <font color="red">*</font></label><input type="text" name="firstName" placeholder="First Name" autocomplete="off" class="form-control" required id="firstName">
							</div>
							
							<div class="col-sm-3 form-group" id="colLastName" style="display: none">
								<label>Last Name</label><input type="text" name="lastName" placeholder="Last Name" autocomplete="off" class="form-control"  id="lastName">
							</div>

							<div class="col-sm-3 form-group" id="colSheter" style="display: none">
								<label>Shelter <font color="red">*</font></label>
								<select class="form-control" name="shelter" id="shelterId" >
									<option value="">Select</option>
									<?php foreach ($shelter as $key => $value) { ?>
									<option value="<?php echo $value->id_seq ?>"><?php echo $value->shelter_name ?>		
									</option> 
									<?php } ?>
									</select>

							</div>

							<div class="col-sm-3 form-group" id='colDeviceTerminal' style="display: none">
								<label>Device POS <font color="red">*</font></label>
								<select class="form-control" name="deviceTerminal" id="deviceTerminal" >
									<option value="">Select</option>
									<?php foreach ($deviceTerminal as $key => $value) { ?>
									<option value="<?php echo $value->id_seq ?>"><?php echo $value->terminal_name ?>		
									</option> 
									<?php } ?>
								</select>
							</div>

							<div class="col-sm-3 form-group" id="colLane" style="display: none">
								<label>Lane <font color="red">*</font></label>
								<select class="form-control" name="lane" id="lane">
								<option value="">Select</option>
								</select>
							</div>  

							<div class="col-sm-3 form-group" id="colPo" style="display: none">
								<label>PO <font color="red">*</font></label>
								<select class="form-control" name="po" id="po" >
									<option value="">Select</option>
									<?php foreach ($po as $key => $value) { ?>
									<option value="<?php echo $value->id_seq ?>" ><?php echo $value->po_name ?>
									</option><?php } ?>
								</select>
							</div>

							<div class="col-sm-6 form-group" id="colMerchantPassword" style="display: none">
								<label>Password <font color="red">*</font></label>
								<div class="input-group">
									<input type="password" id="generatePass" value="" class="form-control" placeholder="Password" name="generatePass" >
									<div class="input-group-btn">
										<button class="btn bg-angkasa2 " onclick="generate()" type="button" style="margin-top:-5px">Generate</button>
									</div>
									&nbsp;
									&nbsp;
									<input type="checkbox" name="openPass" id="openPass" > Show Password
								</div>
							</div>

						</div>


					</div>
				</div>
				<div class="modal-footer">
					<!-- <button type="button" class="btn btn-link" onclick="close_modal()">Cancel</button> -->
					<button type="submit" class="btn bg-angkasa2" id="tombol">Submit</button>
				</div>
			</form>

		</div>
	</div>
</div>

<script type="text/javascript">

	$("#form_add").submit(function(event){
		event.preventDefault();

		$.ajax({
			url: '<?php echo site_url(); ?>configuration/user/action_add',
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



	$("#colUsername").hide();
	$("#colEmail").hide();
	$("#colPassword").hide();
	$("#colFirstName").hide();
	$("#colLastName").hide();
	$("#tombol").hide();

	$('#userGroup').on('change',function(){

		var userId=$('#userGroup').val();
		
		if(userId=='3' || userId=='6')
		{
			$("#colUsername").show();
			$("#colEmail").show();
			$("#colPassword").show();
			$("#colFirstName").show();
			$("#colLastName").show();
			$("#colSheter").show();
			$("#colLane").hide();
			$("#colDeviceTerminal").show();
			$("#colPo").hide();
			$("#colMerchant").hide();
			$("#colMerchantPassword").hide();
			$("#tombol").show();

			$("#userName").attr('required','required');
			// $("#email").attr('required','required');
			$("#password").attr('required','required');
			$("#firstName").attr('required','required');
			// $("#lastName").attr('required','required');

			$("#shelterId").attr('required','required');
			$("#deviceTerminal").attr('required','required');
			$("#po").removeAttr('required');
			$("#lane").removeAttr('required');
			$("#merchant").removeAttr('required');
			$("#generatePass").removeAttr('required');


		}
		else if (userId=='7')
		{
			$("#colUsername").show();
			$("#colEmail").show();
			$("#colPassword").show();
			$("#colFirstName").show();
			$("#colLastName").show();
			$("#colSheter").show();
			$("#colLane").show();
			$("#colDeviceTerminal").show();
			$("#colPo").hide()
			$("#colMerchant").hide();
			$("#colMerchantPassword").hide();
			$("#tombol").show();

			$("#userName").attr('required','required');
			// $("#email").attr('required','required');
			$("#password").attr('required','required');
			$("#firstName").attr('required','required');
			// $("#lastName").attr('required','required');

			$("#shelterId").attr('required','required');
			$("#deviceTerminal").attr('required','required');
			$("#po").removeAttr('required');
			$("#lane").attr('required','required');
			$("#merchant").removeAttr('required');
			$("#generatePass").removeAttr('required');
		}

		else if(userId=="")
		{
			$("#colUsername").hide();
			$("#colEmail").hide();
			$("#colPassword").hide();
			$("#colFirstName").hide();
			$("#colLastName").hide();
			$("#colSheter").hide();
			$("#colLane").hide();
			$("#colDeviceTerminal").hide();
			$("#colPo").hide();
			$("#colMerchant").hide();
			$("#colMerchantPassword").hide();
			$("#tombol").hide();			
		}

		else if(userId=='8' )
		{
			$("#colUsername").show();
			$("#colEmail").show();
			$("#colPassword").show();
			$("#colFirstName").show();
			$("#colLastName").show();
			$("#colSheter").hide();
			$("#colLane").hide();
			$("#colDeviceTerminal").show();
			$("#colPo").hide();
			$("#colMerchant").hide();
			$("#colMerchantPassword").hide();
			$("#tombol").show();

			$("#userName").attr('required','required');
			// $("#email").attr('required','required');
			$("#password").attr('required','required');
			$("#firstName").attr('required','required');
			// $("#lastName").attr('required','required');

			$("#shelterId").removeAttr('required');
			$("#deviceTerminal").attr('required','required');
			$("#po").removeAttr('required');
			$("#lane").removeAttr('required');
			$("#merchant").removeAttr('required');
			$("#generatePass").removeAttr('required');
		}
		else if (userId=='5')
		{
			$("#colUsername").show();
			$("#colEmail").show();
			$("#colPassword").show();
			$("#colFirstName").show();
			$("#colLastName").show();
			$("#colSheter").show();
			$("#colLane").hide();
			$("#colDeviceTerminal").hide();
			$("#colPo").show();
			$("#colMerchant").hide();
			$("#colMerchantPassword").hide();
			$("#tombol").show();


			$("#userName").attr('required','required');
			// $("#email").attr('required','required');
			$("#password").attr('required','required');
			$("#firstName").attr('required','required');
			// $("#lastName").attr('required','required');

			$("#shelterId").attr('required','required');
			$("#deviceTerminal").removeAttr('required');
			$("#po").attr('required','required');
			$("#lane").removeAttr('required');
			$("#merchant").removeAttr('required');
			$("#generatePass").removeAttr('required');
		}
		else if (userId=='4')
		{
			$("#colUsername").show();
			$("#colEmail").show();
			$("#colPassword").show();
			$("#colFirstName").show();
			$("#colLastName").show();
			$("#colSheter").hide();
			$("#colLane").hide();
			$("#colDeviceTerminal").show();
			$("#colPo").hide();
			$("#colMerchant").hide();
			$("#colMerchantPassword").hide();
			$("#tombol").show();

			$("#userName").attr('required','required');
			// $("#email").attr('required','required');
			$("#password").attr('required','required');
			$("#firstName").attr('required','required');
			// $("#lastName").attr('required','required');

			$("#shelterId").removeAttr('required');
			$("#deviceTerminal").attr('required','required');
			$("#po").removeAttr('required');
			$("#lane").removeAttr('required');
			$("#merchant").removeAttr('required');
			$("#generatePass").removeAttr('required');	

			getDevicePos();	

		}
		else if (userId=='9')
		{
			$("#colUsername").show();
			$("#colEmail").hide();
			$("#colPassword").hide();
			$("#colFirstName").hide();
			$("#colLastName").hide();
			$("#colSheter").hide();
			$("#colLane").hide();
			$("#colDeviceTerminal").hide();
			$("#colPo").hide();
			$("#colMerchant").show();
			$("#colMerchantPassword").show();
			$("#tombol").show();


			$("#userName").attr('required','required');
			// $("#email").removeAttr('required');
			$("#password").removeAttr('required');
			$("#firstName").removeAttr('required');
			// $("#lastName").removeAttr('required');

			$("#shelterId").removeAttr('required');
			$("#deviceTerminal").removeAttr('required');
			$("#po").removeAttr('required');
			$("#lane").removeAttr('required');
			$("#merchant").attr('required','required');
			$("#generatePass").attr('required','required');	

			
			$("#generatePass").keydown(function(e){
        		e.preventDefault();
    		});
		}

		else if (userId=='11')
		{
			$("#colUsername").show();
			$("#colEmail").hide();
			$("#colPassword").show();
			$("#colFirstName").show();
			$("#colLastName").show();
			$("#colSheter").hide();
			$("#colLane").hide();
			$("#colDeviceTerminal").hide();
			$("#colPo").show();
			$("#colMerchant").hide();
			$("#colMerchantPassword").hide();
			$("#tombol").show();


			$("#userName").attr('required','required');
			// $("#email").removeAttr('required');
			$("#password").attr('required','required');
			$("#firstName").attr('required','required');
			// $("#lastName").removeAttr('required');

			$("#shelterId").removeAttr('required');
			$("#deviceTerminal").removeAttr('required');
			$("#po").attr('required','required');
			$("#lane").removeAttr('required');
			$("#merchant").removeAttr('required');
			$("#generatePass").removeAttr('required');	

		}
		else
		{
			$("#colUsername").show();
			$("#colEmail").show();
			$("#colPassword").show();
			$("#colFirstName").show();
			$("#colLastName").show();
			$("#colSheter").hide();
			$("#colLane").hide();
			$("#colDeviceTerminal").hide();
			$("#colPo").hide();
			$("#colMerchant").hide();
			$("#colMerchantPassword").hide();
			$("#tombol").show();

			$("#userName").attr('required','required');
			// $("#email").attr('required','required');
			$("#password").attr('required','required');
			$("#firstName").attr('required','required');
			// $("#lastName").attr('required','required');

			$("#shelterId").removeAttr('required');
			$("#deviceTerminal").removeAttr('required');
			$("#po").removeAttr('required');
			$("#lane").removeAttr('required');
			$("#merchant").removeAttr('required');
			$("#generatePass").removeAttr('required');

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

		// getTerminalDevice();
		// function getTerminalDevice()
		// {
		// 	$.ajax({
		// 		type:"post",
		// 		url:"<?php echo site_url('configuration/user/getTerminalDevice'); ?>",
		// 		dataType:"json",
		// 		data:"id="+userId,
		// 		success:function(x)
		// 		{
		// 			// console.log(x);
		// 			var isi="<option value=''>Select</option>";
		// 			for(var i=0; i< x.length ; i++)
		// 			{
		// 				isi +="<option>"+x[i].terminal_name+"</option>";
		// 			}
		// 			 $("#deviceTerminal").html(isi);
		// 		}
		// 	});
		// }

		

		function getDevicePos()
		{
			$.ajax({
				type:"post",
				url:"<?php echo site_url()?>configuration/user/getDevicePos",
				dataType:"json",
				success:function(x){
					// console.log(x);

					var isi="<option value=''>Select</option>";

					for(var i=0;i<x.length;i++)
					{
						isi +="<option value='"+x[i].id_seq+"'>"+x[i].terminal_name+"</option>"
					}

					$('#deviceTerminal').html(isi);

					// console.log(isi);

				}
			});	
		}

	});

	function generate()
	{	
		$.ajax({
			url:"<?php echo site_url() ?>configuration/user/generateCode",
			dataType:"json",
			beforeSend: function(){
				blockID('#form_add');
			},
			success:function(x)
			{
				$("#generatePass").val(x);
				unblockID('#form_add');
			}
		});
	}

	$("#openPass").on("change",function(){
		if(this.checked)
		{
			$("#generatePass").attr('type','text');
		}
		else
		{
			$('#generatePass').attr('type','password');	
		}
	});

	$("#merchant").on("keyup",function(){

		// console.log($('#merchant').val());
		var x= $('#merchant').val();

		//remove space
		// var y=x.replace(/\s+/g, '');
		var y=x.replace(/\s+/g, '.');

		$("#userName").val(y.toLowerCase());

	});

</script>