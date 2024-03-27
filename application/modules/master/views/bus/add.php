<div class="col-md-10 col-md-offset-1">
	<div class="modal-header bg-astra" style="padding:3px">
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
			<form id="custForm" action="">
				<div class="modal-body">
		<div class="form-group">
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>First Name :</label>
						<input type="text" name="fisrt_name" placeholder="" class="form-control" >
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Last Name :</label>
						<input type="text" name="last_name" class="form-control" >
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Email :</label>
						<input type="email" name="email" placeholder="" class="form-control" required>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Pasword :</label>
						<input type="password" name="password" class="form-control" >
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Tempat Lahir :</label>
						<input type="text" name="birthPlace" placeholder="" class="form-control" >
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Tanggal Lahir</label>
						<input type="text" name="birthDate" autocomplete="off" placeholder="" class="form-control datepicker" required>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Gender :</label>
						<select class="form-control" name="gender">
							<option value="L">Laki-laki</option>
							<option value="P">Perempuan</option>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Nomor KTP :</label>
						<input type="text" name="idNumber" class="form-control" required>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Kota :</label>
						<input type="text" class="form-control" name="cityName">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Provinsi :</label>
						<input type="text" name="province" placeholder="" class="form-control" >
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Golongan Kendaraan :</label>
						<select class="form-control select2" name="golKendaraan">
							<?php foreach ($gol_vehicle as $key => $value) { ?>
								<option value="<?php echo $value->NAMA_GOLONGAN ?>"><?php echo $value->NAMA_GOLONGAN ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Obu :</label>
						<select class="form-control select2" name="RFID" required>
							<?php foreach ($obu as $key => $value) { ?>
								<option value="<?php echo $value->id ?>"><?php echo $value->obu_number ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Nomor STNK :</label>
						<input type="text" placeholder="" name="no_stnk" class="form-control">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Merek :</label>
						<input type="text" placeholder="" name="merek" class="form-control">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Warna :</label>
						<input type="text" placeholder="" name="warna" class="form-control">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Plat Nomor :</label>
						<input type="text" placeholder="" name="platNo" class="form-control">
						<span class="help-block"><b>*tanpa spasi</b></span>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Nomor Telepon :</label>
						<input type="text" placeholder="" name="phoneNo" class="form-control" required>
						<span class="help-block"><b>*gunakan format 62</b></span>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label>Expired :</label>
						<input type="text" placeholder="" autocomplete="off" name="expiredDate" class="form-control datepicker" required>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Alamat :</label>
						<textarea class="form-control" name="custAddress"></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-link" onclick="close_modal()">Cancel</button>
					<button type="submit" class="btn bg-astra">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('.select2').select2();
	$('.datepicker').datepicker({
		format: 'yyyy-mm-dd',
		autoclose: true
	});

	$("#custForm").submit(function(event){
		event.preventDefault();
		blockID('#custForm');

		$.ajax({
			url: '<?php echo site_url(); ?>customer/action_add',
			type: "POST",
			data: $("#custForm").serialize(),
			dataType: 'json',

			success: function(json) {
				if (json.code == 200){
					unblockID('#custForm');
					notif(json.header,json.message,json.theme);
					close_modal();
					$('#grid').datagrid('load');
				}else{
					notif(json.header,json.message,json.theme);
				}
			},

			error: function(){
				unblockID('#custForm');
			},

			complete:function(json){
				unblockID('#custForm');
			}
		});
	});
</script>