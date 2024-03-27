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
			<div class="modal-body">
				<div class="form-group">
					<div class="row">
						<div class="col-md-3">
							<div class="stat-right">
								<div class="stat-number">
									<div class="title bg-angkasa2">Terminal</div>
									<div><?=$detail['shelter_name'] ?></div>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="stat-right">
								<div class="stat-number">
									<div class="title bg-angkasa2">Booking Code</div>
									<div><?=$detail['booking_code'] ?></div>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="stat-right">
								<div class="stat-number">
									<div class="title bg-angkasa2">Booking Date</div>
									<div><?=format_dateTimeDetik($detail['booking_date']) ?></div>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="stat-right">
								<div class="stat-number">
									<div class="title bg-angkasa2">Ticket Number</div>
									<div><?=$detail['ticket_code'] ?></div>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="stat-right">
								<div class="stat-number">
									<div class="title bg-angkasa2">Payment Channel</div>
									<div><?=$detail['payment_channel'] ?></div>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="stat-right">
								<div class="stat-number">
									<div class="title bg-angkasa2">Fare</div>
									<div><?=$detail['price'] ?></div>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="stat-right">
								<div class="stat-number">
									<div class="title bg-angkasa2">Ref Number</div>
									<div><?= ($detail['trx_id'] != "") ? $detail['trx_id'] : "-" ?></div>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="stat-right">
								<div class="stat-number">
									<div class="title bg-angkasa2">Payment Date</div>
									<div><?=format_dateTimeDetik($detail['created_on']) ?></div>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="modal-footer">
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	document.onkeydown = function(evt) {
		evt = evt || window.event;
		if (evt.keyCode == 27) {
			close_modal();
		}
	};
</script>