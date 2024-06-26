<?php $this->load->view('common/jeasyui'); ?>
<?php  $lastweek = date('Y-m-d',strtotime("-7 days"));?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/common/toExcel.js"></script>
<!-- Theme JS files -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/plugins/forms/tags/tagsinput.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/plugins/forms/tags/tokenfield.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
 	

<div class="panel bg-angkasa2">
	<div class="panel-heading ">
		<h5 class="panel-title" ><?php echo $title ?></h5>
	</div>
	<div class="panel-body" style="display: block; padding:5px ">
		<div id="toolbar">
			<div class="row">
				<div class="col col-md-8" style="padding: 5px 15px">
					<?php if ($add) {
						create_btn(site_url('po/route/add'),'Add','plus-circle2');
					} ?>
				</div>
				<div class="col col-md-4 " style="padding: 5px 15px">
					<div class="input-group" align="right">
						<span class="input-group-addon">Search : </span>
						<input type="text" id="search" value="" class="form-control" placeholder="Search...">
						<div class="input-group-btn">
							<button type="submit" onclick="searching()" class="btn bg-angkasa2"><i class="icon-search4"></i></button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<table id="grid" toolbar="#toolbar" class="easyui-datagrid" singleSelect="true"></table>


	</div>

</div>
<script type="text/javascript">

	$("#btndownload").click(function(event){

		var sortFrom= $('#sortFrom').val();
		var sortTo= $('#sortTo').val();
		var search= $('#search').val();

		window.location.href="<?php echo site_url('transaction/route/download?') ?>sortFrom="+sortFrom+"&sortTo="+sortTo+"&search="+search;
	});

	$(".datepicker").datepicker({
		format: 'yyyy-mm-dd',
		autoclose:true,
		orientation: "bottom auto",
	});

	settingDefaultDatagrid()
	$(document).ready(function(){


		$('#grid').datagrid({
			url         : '<?php echo site_url('po/route/getList'); ?>',
			// pagination : true,
			// rownumbers : true,
			// fitColumns : true,
			nowrap:false,
			emptyMsg: 'No Records Found',
			// queryParams: {sortFrom:$('#sortFrom').val(),sortTo:$('#sortTo').val()},
			columns:[[

				// { field: 'route_code', title: 'Route Code', sortable: true, halign:"center",width:100 },
				{ field: 'airport_name', title: 'Airport', sortable: true, halign:"center",width:100 },
				{ field: 'route_info', title: 'Route', sortable: true, halign:"center",width:100 },
				{ field: 'action', title: 'Action', sortable: true, halign:"center",width:50, align:"center" },

			]],

			onLoadSuccess:function(row){
				if (row.total == 0) {
					$('#btndownload').prop('disabled',true);
				}else{
					$('#btndownload').prop('disabled',false);
				}

				$(window).resize(function(){
					setTimeout(function(){
						$('#grid').datagrid('resize');
					},400);
				});

				$(".nav").click(function(){
					setTimeout(function(){
						$('#grid').datagrid('resize');
					},400);
				});

			}
		});
	});

	document.getElementById('search').onkeydown = function(e) {
		if (e.keyCode == 13 || e.which == 13) {
			searching(e);
		}
	}


	function searching()
	{
		$('#grid').datagrid('load',{search: $('#search').val()});
	}

	function edit(id){
		$.magnificPopup.open({
			items: {
				src: "<?php echo site_url('po/route/edit/') ?>"+id,
			},
			modal: true,
			type: 'ajax',
			tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
			showCloseBtn: false,
		});
	}

	function deleteData(id){
		url = "<?php echo site_url('po/route/delete/') ?>"+id;
		confirm_delete(url);
	}

	function validasi(msg,reload=''){
		swal({
			title: "Warning, "+msg,
			text: "",
			type: "warning",
			// showCancelButton: true,
			confirmButtonColor: "#EF5350",
			// confirmButtonText: "Delete",
			// cancelButtonText: "Cancel",
			closeOnConfirm: true,
			closeOnCancel: true,
		});
	}



</script>