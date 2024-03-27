<!-- Theme JS files -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/plugins/forms/tags/tagsinput.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/plugins/forms/tags/tokenfield.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
 	

<div class="col-md-5 col-md-offset-4">
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

			<!-- <form method="post" id="form_add" action="<?php echo site_url(); ?>po/route/action_add"> -->
			<form method="post" id="form_add">
				<div class="modal-body">
					<div class="form-group">
						<div class="row" >
							<div class="col-sm-12 form-group">
								<label>Airport <font color="red">*</font></label>
								<select class="form-control" name="airportId" id="airport" required>
									<?php foreach ($airport as $key => $value) { ?>
									<option value="<?php echo $value->id_seq ?>"><?php echo $value->airport_name ?></option>
									 <?php } ?>
								</select>
							</div>
							
							<div class="col-sm-12 form-group">
								<label>Route <font color="red">*</font></label>
								<input type="text" name="route" placeholder="Route Name" autocomplete="off" class="form-control" required>
							</div>


							<!-- <div class="col-sm-12 form-group"> -->
							<!-- Typeahead support -->
								<!-- <label>Route Tag</label> -->
								<!-- <input type="text" value="" data-role="tagsinput" class="tagsinput-typeahead" name="routeTag"> -->
							<!-- /typeahead support -->
							<!-- </div>  -->

						</div>
					</div>
				</div>
				<div class="modal-footer">
					<!-- <button type="button" class="btn btn-link" onclick="close_modal()">Cancel</button> -->
					<button type="submit" class="btn bg-angkasa2">Submit</button>
				</div>
			</form>

		</div>
	</div>
</div>

<script type="text/javascript">

	
	$("#form_add").submit(function(event){
		event.preventDefault();

		$.ajax({
			url: '<?php echo site_url(); ?>po/route/action_add',
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


	// var substringMatcher = function(strs) {
 //    return function findMatches(q, cb) {
 //            var matches, substringRegex;

 //            // an array that will be populated with substring matches
 //            matches = [];

 //            // regex used to determine if a string contains the substring `q`
 //            substrRegex = new RegExp(q, 'i');

 //            // iterate through the pool of strings and for any string that
 //            // contains the substring `q`, add it to the `matches` array
 //            $.each(strs, function(i, str) {
 //                if (substrRegex.test(str)) {

 //                    // the typeahead jQuery plugin expects suggestions to a
 //                    // JavaScript object, refer to typeahead docs for more info
 //                    matches.push({ value: str });
 //                }
 //            });
 //            cb(matches);
 //        };
 //    };


 //    var states=<?php //echo $tag ?>

 //    console.log(states);
 //    // Attach typeahead
 //    $('.tagsinput-typeahead').tagsinput('input').typeahead(
 //        {
 //            hint: true,
 //            highlight: true,
 //            minLength: 1
 //        },
 //        {
 //            name: 'states',
 //            displayKey: 'value',
 //            source: substringMatcher(states)
 //        }
 //    ).bind('typeahead:selected', $.proxy(function (obj, datum) {  
 //        this.tagsinput('add', datum.value);
 //        this.tagsinput('input').typeahead('val', '');
 //    }, $('.tagsinput-typeahead')));



</script>