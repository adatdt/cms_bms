
<div class="col-md-8 col-md-offset-2">
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
			<form method="post" action="" id="form_edit">
				<div class="modal-body">
					<div class="form-group">
						<div class="row" id="row_detail">
							<div class="col-md-6">
								<table class="table table-striped">
									<thead>
										<tr>
											<th colspan="3" class="bg-astra">DATA CUSTOMER</th>
										<tr>
									</thead>
									<tbody>
											<tr>
												<td>NAME</td>
												<td>&nbsp:&nbsp</td>
												<td ><?php echo $detail->first_name." ".$detail->last_name ?></td>
											</tr>
											<tr>
												<td>NIK</td>
												<td>&nbsp:&nbsp</td>
												<td id='nik'><?php echo $detail->identity_number ?></td>
											</tr>
											<tr>
												<td>IDENTITY TYPE</td>
												<td>&nbsp:&nbsp</td>
												<td id='type'><?php echo $detail->identity_type ?></td>
											</tr>
											<tr>
												<td>GENDER</td>
												<td>&nbsp:&nbsp</td>
												<td id='gender'><?php echo $detail->gender ?></td>
											</tr>
											<tr>
												<td>EMAIL</td>
												<td>&nbsp:&nbsp</td>
												<td id='email'><?php echo $detail->email_address ?></td>
											</tr>
											<tr>
												<td>PHONE NUMBER</td>
												<td>&nbsp:&nbsp</td>
												<td id='phone'><?php echo $detail->phone_number?></td>
											</tr>
											<tr>
												<td>PLACE AND BIRTHDATE</td>
												<td>&nbsp:&nbsp</td>
												<td id='birthday'><?php echo $detail->birth_place." ".$detail->birth_date?></td>
											</tr>
											<tr>
												<td>ADDRESS</td>
												<td>&nbsp:&nbsp</td>
												<td id='address'><?php echo $detail->address ?></td>
											</tr>
											<tr>
												<td>CITY</td>
												<td>&nbsp:&nbsp</td>
												<td id='city'><?php echo $detail->city_name ?></td>
											</tr>
											<tr>
												<td>PROVINCE</td>
												<td>&nbsp:&nbsp</td>
												<td id='province'><?php echo $detail->province_name?></td>
											</tr>
									</tbody>
								</table>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<table class="table table-striped">
										<thead>
											<tr>
												<th colspan="3" class="bg-astra">DATA OBU</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>OBU NUMBER</td>
												<td>&nbsp:&nbsp</td>
												<td id='obu_number'><?php echo $detail->obu_number ?></td>
											</tr>
											<tr>
												<td>SERIAL</td>
												<td>&nbsp:&nbsp</td>
												<td id='serial'><?php echo $detail->serial ?></td>
											</tr>
											<tr>
												<td>LAST BALANCE (Rp.)</td>
												<td>&nbsp:&nbsp</td>
												<td id='last_balance'><?php echo $detail->last_balance ?></td>
											</tr>
											<tr>
												<td>DEBT (Rp.)</td>
												<td>&nbsp:&nbsp</td>
												<td id='last_balance'><?php echo $detail->debt ?></td>
											</tr>
											<tr>
												<td>STATUS</td>
												<td>&nbsp:&nbsp</td>
												<td id='status'><?php echo $detail->status ?></td>
											</tr>
										</tbody>
									</table>
									<br>
									<table class="table table-striped">
										<thead>
											<tr>
												<th colspan="3" class="bg-astra">DATA VEHICLE</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>BRAND</td>
												<td>&nbsp:&nbsp</td>
												<td id='merek'><?php echo $detail->merek ?></td>
											</tr>
											<tr>
												<td>PLAT NUM</td>
												<td>&nbsp:&nbsp</td>
												<td id='plat_no'><?php echo $detail->no_plat ?></td>
											</tr>
											<tr>
												<td>NO STNK</td>
												<td>&nbsp:&nbsp</td>
												<td id='stnk'><?php echo $detail->no_stnk ?> </td>
											</tr>
											<tr>
												<td>CLASS</td>
												<td>&nbsp:&nbsp</td>
												<td id='vehicle_class'><?php echo $detail->id_golongan?></td>
											</tr>
											<tr>
												<td>COLOR</td>
												<td>&nbsp:&nbsp</td>
												<td id='color'><?php echo $detail->warna ?> </td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>

				</div>
				<div class="modal-footer">
<!-- 					<button type="button" class="btn btn-link" onclick="close_modal()">Cancel</button>
					<button type="submit" class="btn bg-astra">Submit</button> -->
				</div>
			</form>
		</div>
	</div>
</div>


