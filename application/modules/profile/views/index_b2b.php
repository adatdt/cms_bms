<?php $this->load->view('common/jeasyui'); ?>
<?php  $lastweek = date('Y-m-d',strtotime("-7 days"));?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/common/toExcel.js"></script>


<div class="panel bg-angkasa2">
	<div class="panel-heading ">
		<h5 class="panel-title" ><?php echo $title ?></h5>
	</div>
	<div class="panel-body" style="display: block; padding:5px ">
			<div class="col col-md-3"></div>
		<div class="col col-md-6" style="padding: 5px 15px" >
			<center>
				<div class="icon-object border-blue-800 bg-angkasa2">
					<i class="icon-reading">
				</i>
				</div> 
			</center>
			<table class="table table-striped">
				<tr>
					<td>User Name</td>
					<td align="center">:</td>
					<td id='username'></td>
				</tr>
				<tr>
					<td>Merchant Name</td>
					<td align="center">:</td>
					<td id="name" style="min-width:200px"></td>
				</tr>
				<tr>
					<td colspan="3" align="center">
					<?php $id=$this->session->userdata('user_id'); ?>
					<button onClick="edit('<?php echo $id ?>')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="Edit">Edit</button>

				<button onClick="change_password('<?php echo $id ?>')" class="updated btn bg-angkasa2 btn-icon btn-xs btn-dtgrid" title="change_password">Change Password</button>
					</td>
				</tr>
			</table>
		</div>
		<div class="col col-md-3"></div>
	</div>

</div>
<script type="text/javascript">

	

	function userData()
	{
		$.ajax({
			dataType:"json",
			url:"<?php echo site_url('profile/getProfile/')?>",
			success:function(x)
			{
				// console.log(x);

				$("#username").html(x.username);
				$("#name").html(x.first_name);
				$("#email").html(x.email);
			}
		});
	}

	userData();

	function edit(id){
		$.magnificPopup.open({
			items: {
				src: "<?php echo site_url('profile/edit_b2b/') ?>"+id,
			},
			modal: true,
			type: 'ajax',
			tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
			showCloseBtn: false,
		});

	}

	function change_password(id){
		
		$.magnificPopup.open({
			items: {
				src: "<?php echo site_url('profile/change_password/') ?>"+id,
			},
			modal: true,
			type: 'ajax',
			tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
			showCloseBtn: false,
		});
	}




</script>