<form action="<?php echo base_url() ?>import/upload" role="form" method="POST" enctype="multipart/form-data">
	<div class="panel panel-nutech">
		<div class="panel-heading">
			<h5 class="panel-title">Import<a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
			<div class="heading-elements">
				<ul class="icons-list">
					<li><a data-action="collapse"></a></li>
					<li><a data-action="reload"></a></li>
					<li><a data-action="close"></a></li>
				</ul>
			</div>
		</div>

		<div class="panel-body">
			<div class="form-group">
				<label>File :</label>
				<input type="file" class="form-control" name="file" required>
				<span class="help-block">Sesuai format file yang sudah disediakan</span>
			</div>
			<div class="form-group">
				<label>Bulan:</label>
				<input type="text" class="form-control datepicker" name="month_salary" autocomplete="off">
				<span class="help-block">Gaji untuk bulan apa</span>
			</div>
			<div class="text-right">
				<button type="submit" class="btn bg-nutech">Upload <i class="icon-cloud-upload position-right"></i></button>
			</div>
		</div>
	</div>
</form>

<script type="text/javascript">
	$(".datepicker").datepicker( {
		autoclose:true,
		orientation: "bottom auto",
		format: "yyyy/mm",
		startView: "months", 
		minViewMode: "months",
	});
</script>