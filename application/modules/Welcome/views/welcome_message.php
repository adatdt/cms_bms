<!DOCTYPE html>
<html>
<title>SLIP</title>
<?php $this->load->view('common/head'); ?>
<style>
	table, th,td{
   white-space: nowrap;
}
</style>
<body class="hold-transition skin-blue sidebar-collapse sidebar-mini">
	<div class="wrapper">

		<?php $this->load->view('common/header'); ?>
		<?php $this->load->view('common/menu'); ?>

		<div class="content-wrapper">
			<section class="content">
				<div class="row">
					<div class="col-md-12">
						<?php echo json_encode($_SESSION) ?>
						<?php if ($_SESSION['level'] == '1') { ?>
							<a href="<?php echo base_url(); ?>import" class="btn btn-primary">Import</a><br><br>
						<?php } ?>
						
						<!-- Custom Tabs (Pulled to the right) -->
						<div class="nav-tabs-custom">
							<ul class="nav nav-tabs pull-right">
								<li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">Detail Salary</a></li>
								<li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Detail Employee</a></li>
								<li class="pull-left header"><i class="fa fa-print"></i> Employee</li>
							</ul>
							<div class="tab-content">
								<div class="tab-pane" id="tab_2">
									<div class="box-body table-responsive no-padding">
										<table class="table table-bordered">
											<div class="pull-right">
												<div class="input-group input-group-sm" style="width: 150px;">
													<input type="text" name="table_search" class="form-control pull-right" placeholder="Search">
													<div class="input-group-btn">
														<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
													</div>
												</div>
											</div><br><br>
											<tbody><tr>
												<th>No</th>
												<th>NIK</th>
												<th>Nama</th>
												<th>Hire Date</th>
												<th>Akhir Kontrak</th>
												<th>Salary (Rp)</th>
												<th>Extra Pay (Rp)</th>
												<th>Total (Rp)</th>
											</tr>
											<tr>
												<td>1</td>
												<td>123</td>
												<td>Ilham Ramadhan</td>
												<td>17 Mei 2018</td>
												<td>16 Mei 2021</td>
												<td><?php echo number_format("3650000",0,",","."); ?></td>
												<td><?php echo number_format("600000",0,",","."); ?></td>
												<td><?php echo number_format("600000",0,",","."); ?></td>
											</tr>
										</tbody></table>
										<div class="box-footer clearfix">
										<ul class="pagination pagination-sm no-margin pull-right">
											<li><a href="">«</a></li>
											<li><a href="">1</a></li>
											<li><a href="">2</a></li>
											<li><a href="">3</a></li>
											<li><a href="">»</a></li>
										</ul>
									</div>
									</div>
								</div>
								<div class="tab-pane active" id="tab_1">
									<div class="box-body table-responsive no-padding">
										<table class="table table-bordered">
											<!-- <div class="pull-right">
												<div class="input-group input-group-sm" style="width: 150px;">
													<input type="text" name="table_search" class="form-control pull-right" placeholder="Search">
													<div class="input-group-btn">
														<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
													</div>
												</div>
											</div><br><br> -->
											<tbody><tr>
												<th rowspan="2" style="padding-top: 25px;text-align:center;">No</th>
												<th rowspan="2" style="padding-top: 25px;text-align:center;">NIK</th>
												<th rowspan="2" style="padding-top: 25px;text-align:center;">Nama</th>
												<th colspan="8"><center>Gaji Pokok</center></th>
												<th colspan="4"><center>Tunjangan Tidak Tetap</center></th>
												<th colspan="8"><center>Potongan</center></th>
												<th rowspan="2" style="padding-top: 25px;text-align:center;">Total Dibayar</th>
											</tr>
											<tr>
												<th>Basic</th>
												<th>Cola</th>
												<th>Jabatan</th>
												<th>Prestasi</th>
												<th>Keahlian</th>
												<th>Lembur</th>
												<th>Lain-lain</th>
												<th>Total</th>
												<th>Proyek</th>
												<th>Lembur</th>
												<th>Lebih bayar</th>
												<th>Total</th>
												<th>Koperasi</th>
												<th>Pinjaman</th>
												<th>Lain-lain</th>
												<th>Materai</th>
												<th>Jamsostek</th>
												<th>BPJS</th>
												<th>Asuransi Kesehatan</th>
												<th>Total</th>
											</tr>
											<tr>
												<td>1</td>
												<td>123</td>
												<td>Ilham</td>
												<td>3.650.000</td>
												<td>20.000</td>
												<td>5000</td>
												<td>20000</td>
												<td>105000</td>
												<td>105000</td>
												<td>105000</td>
												<td>105000</td>
												<td>105000</td>
												<td>105000</td>
												<td>105000</td>
												<td>105000</td>
												<td>105000</td>
												<td>105000</td>
												<td>105000</td>
												<td>105000</td>
												<td>105000</td>
												<td>wkwk</td>
												<td>wkwk</td>
												<td>wkwk</td>
												<td>Detail</td>
											</tr>
										</tbody></table>
										<!-- <div class="box-footer clearfix">
										<ul class="pagination pagination-sm no-margin pull-right">
											<li><a href="#">«</a></li>
											<li><a href="#">1</a></li>
											<li><a href="#">2</a></li>
											<li><a href="#">3</a></li>
											<li><a href="#">»</a></li>
										</ul>
									</div> -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
		<div class="control-sidebar-bg"></div>
	</div>

	<?php $this->load->view('common/footer'); ?>
</body>
</html>