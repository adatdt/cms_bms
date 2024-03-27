<div class="panel panel-angkasa2">
	<div class="panel-heading">
		<h5 class="panel-title"><?php echo $title ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
		<div class="heading-elements">
			
		</div>
	</div>

	<div class="panel-body">
		<form class="form-horizontal" id="form_add">
			<fieldset class="content-group">
				<?php foreach ($config as $key => $value) { ?>
					<div class="form-group">
						<label class="control-label col-lg-3"><?php echo humanize($value->name) ?></label>
						<div class="col-lg-9">
							<input type="text" class="form-control" name="<?= $value->name ?>" placeholder="<?= $value->description ?>" value="<?=$value->value ?>">
							<span class="help-block"><i><?=$value->description  ?></i></span>
						</div>
					</div>
				<?php } ?>
			</fieldset>
			<div class="text-right">
				<button type="submit" class="btn bg-angkasa2">Submit</i></button>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	$( document ).ready(function() {
		$("#form_add").submit(function(event){
			blockID('#form_add');
			event.preventDefault();

			$.ajax({
				url: '<?php echo site_url(); ?>configuration/parameter/action_add',
				type: "POST",
				data: $("#form_add").serialize(),
				dataType: 'json',

				success: function(json) {
					if (json.code == 200){
						unblockID('#form_add');
						ni_notif(json.code,json.message);
					}else{
						unblockID('#form_add');
						ni_notif(json.code,json.message);
					}
				},

				error: function(){
					unblockID('#form_add');
				},

				complete: function(){
					unblockID('#form_add');
				}
			});
		});
	});
</script>