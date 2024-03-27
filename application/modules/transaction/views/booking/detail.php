
<div class="col-md-8 col-md-offset-2">
	<div class="modal-header bg-astra" style="padding:3px">
		<style type="text/css">
		.stat-number{
			text-align: left;
		}
		.title{
			font-weight: bold;
			font-size: 20px;
		}
	</style>
	<?php foreach ($detail as $key => $v) {} ?>
	<div class="panel">
		<div class="panel-heading bg-angkasa2">
			<h5 class="panel-title" style="color: white;">Booking Detail <?php echo $booking_code ?>
			<a class="heading-elements-toggle"><i class="icon-more"></i></a>
		</h5>
		<div class="heading-elements">
			<ul class="icons-list">
				<li><a data-action="close" onclick="close_modal()"></a></li>
			</ul>
		</div>
	</div>
	<div class="panel-body">
			<div class="modal-body">
				<div class="form-group">
					<div class="row">
						<div class="col-md-12">
							<div class="portlet light" style="padding: 20px">
								<div class="row number-stats margin-bottom-30">
									<div class="col-md-3">
										<div class="stat-right">
											<div class="stat-number">
												<div class="title">Transaction Time</div>
												<div class="my-number"><?= format_dateTime($v->created_on) ?></div>
											</div>
										</div>
									</div>
									<div class="col-md-2">
										<div class="stat-right">
											<div class="stat-number">
												<div class="title">Shelter</div>
												<div class="my-number"><?= $v->shelter_name ?></div>
											</div>
										</div>
									</div>
									<div class="col-md-2">
										<div class="stat-right">
											<div class="stat-number">
												<div class="title">Route</div>
												<div class="my-number" id="total"><?= $v->route_info ?></div>
											</div>
										</div>
									</div>
									<div class="col-md-2">
										<div class="stat-right">
											<div class="stat-number">
												<div class="title">PO</div>
												<div class="my-number" id="total"><?= $v->po_name ?></div>
											</div>
										</div>
									</div>
									<div class="col-md-3">
										<div class="stat-right">
											<div class="stat-number">
												<div class="title">Class</div>
												<div class="my-number" id="total">Eksekutif</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<table style="width: 100%;" class="table table-striped">
								<tr>
									<th>No</th>
									<th>Ticket Number</th>
									<th>Status</th>
								</tr>
								<?php foreach ($detail as $key => $value) {
									$status = "";
									if ($value->status == 1) {
										$status = '<span class="label label-flat border-danger text-danger-600">'.$value->status_name.'</span>';
									}elseif ($value->status == 2) {
										$status = '<span class="label label-flat border-success text-success-600">'.$value->status_name.'</span>';
									}elseif ($value->status == 3) {
										$status = '<span class="label label-flat border-info text-info-600">'.$value->status_name.'</span>';
									}
									?>
									<tr>
										<td>1</td>
										<td><?= $value->ticket_code ?></td>
										<td><?= $status ?></td>
									</tr>
								<?php } ?>
							</table>
						</div>
					</div>
					<div class="modal-footer"></div>
				</form>
			</div>
		</div>