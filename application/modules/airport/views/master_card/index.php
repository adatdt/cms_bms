<?php $this->load->view('common/jeasyui'); ?>

<div class="panel bg-angkasa2">
	<div class="panel-heading ">
		<h5 class="panel-title" ><?php echo $title ?></h5>
	</div>
	<div class="panel-body" style="display: block; padding:5px ">
		<div id="toolbar">
			<div class="row">
				<div class="col col-md-8" style="padding: 5px 15px">
					<button class="btn bg-angkasa2 btn-sm btn-icon legitRipple" id="btndownload">Download</button>
					<?php if ($add) {
						create_btn(site_url('airport/master_card/add'),'Add','plus-circle2');
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
	$("#btndownload").on("click",function(){
	
	var search= $('#search').val();

	window.location.href="<?php echo site_url('airport/master_card/download?') ?>search="+search;

	});
	settingDefaultDatagrid()
	$(document).ready(function(){
		$('#grid').datagrid({
			url         : '<?php echo site_url('airport/master_card/getList'); ?>',
			fitColumns : true,
			emptyMsg: 'No Records Found',
			columns:[[
				{ field: 'ticket_code', title: 'Ticket Code', sortable: true, halign:"center" },
				{ field: 'pic_name', title: 'Pic Name', sortable: true, width:50, halign:"center"},
				{ field: 'pic_phone', title: 'Pic Phone', sortable: true , halign:"center"},
				{ field: 'qr_code', title: 'Qr Code', sortable: true,width:100, halign:"center" },
				{ field: 'action', title: 'Action', sortable: true ,width:50, halign:"center"},
			]],

			onLoadSuccess:function(row){
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
				src: "<?php echo site_url('airport/master_card/edit/') ?>"+id,
			},
			modal: true,
			type: 'ajax',
			tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
			showCloseBtn: false,
		});
	}

	function deleteData(id){
		url = "<?php echo site_url('airport/master_card/delete/') ?>"+id;
		confirm_delete(url);
	}


</script>