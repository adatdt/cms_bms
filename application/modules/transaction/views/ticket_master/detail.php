
<div class="col-md-8 col-md-offset-2">
	<div class="modal-header bg-angkasa2" style="padding:3px">
		<style type="text/css">
		.stat-number{
			text-align: center;
			background-color: #bcc7d1;
			margin:5px;
		}
		.title{
			font-weight: bold;
			font-size: 20px;
		}
	</style>
	<div class="panel">
		<div class="panel-heading bg-angkasa2">
			<h5 class="panel-title" style="color: white;"><?php echo $title; ?></h5>
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
						<div class="col-md-3" >
							<div class="stat-right">
								<div class="stat-number">
									<div class="title">Time Tap Out</div>
									<div><?php echo format_dateTime($detail2->created_on); ?></div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<div class="stat-right">
								<div class="stat-number">
									<div class="title">UID</div>
									<div ><?php echo $detail2->uid; ?></div>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="stat-right">
								<div class="stat-number">
									<div class="title">PO BUS</div>
									<div ><?php echo $detail2->po_name; ?></div>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="stat-right">
								<div class="stat-number">
									<div class="title">Type</div>
									<div ><?php echo $detail2->type; ?></div>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="stat-right">
								<div class="stat-number">
									<div class="title">Bus Name</div>
									<div ><?php echo $detail2->bus_name; ?></div>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="stat-right">
								<div class="stat-number">
									<div class="title">Plate Number</div>
									<div ><?php echo $detail2->plate_number; ?></div>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="stat-right">
								<div class="stat-number">
									<div class="title">Route</div>
									<div ><?php echo $detail2->route_info; ?></div>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="stat-right">
								<div class="stat-number">
									<div class="title bg-angkasa2">Driver Name</div>
									<div ><?php echo $detail2->driver_name; ?></div>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="stat-right">
								<div class="stat-number">
									<div class="title">Driver Phone</div>
									<div ><?php echo $detail2->driver_phone; ?></div>
								</div>
							</div>
						</div>
					</div>
					<p></p>
					<div class="row">
						<div class="col-md-12">
							<table class="table table-striped">
								<tbody>
									<tr>
										<th>No</th>
										<th>Shelter</th>
									</tr>
									<?php foreach($detail as $detail){ ?>
									<tr>
										<td>
											<?php echo $detail->shelter_order;?>
										</td>
										<td>
											<?php echo $detail->shelter_name;?>
										</td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	$(".title").addClass('bg-angkasa2');
</script>