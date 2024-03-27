function settingDefaultDatagrid(){
	$.extend($.fn.datagrid.defaults, {
		method      : 'POST',
		pageList    : [10,20,30,50,100,200],
		striped     : true,
		fitColumns  : true,
		rownumbers  : true,
		pagination  : true,
		height      : 'auto',
		toolbar     : '#tb',
		emptyMsg    : 'Tidak Ada Data.',
		loadMsg 	: 'Memproses, tunggu sebentar.',
		minHeight   : '200px',
		scrollbarSize: 1,
		nowrap      : true,
		sortable    : false,
		singleSelect: true,
	});
}

function open_modal(url)
{
	$.magnificPopup.open({
			items: {
				src: url,
			},
			modal: true,
			type: 'ajax',
			tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
			showCloseBtn: false,
		});
}

function open_modal_add(url)
{
	$.magnificPopup.open({
			items: {
				src: url,
			},
			modal: true,
			type: 'ajax',
			tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
			showCloseBtn: false,
		});
}

function close_modal()
{
	$.magnificPopup.close()
}

function master_edit(url)
{
	$.magnificPopup.open({
		items: {
			src: url,
		},
		modal: true,
		type: 'ajax',
		tLoading: '<i class="fa fa-refresh fa-spin"></i> Loading...',
		showCloseBtn: false,
	});
}

function master_delete_menu(url){
	swal({
		title: "Hapus data?",
		text: "Data yang sudah dihapus tidak dapat dikembalikan",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#EF5350",
		confirmButtonText: "Hapus",
		cancelButtonText: "Batal",
		closeOnConfirm: true,
		closeOnCancel: true
	},
	function(isConfirm){
		if (isConfirm) {
			blockUI();
			$.ajax({
				url: url,
				type: "POST",
				dataType: 'json',

				success: function(json) {
					unblockUI();
					ni_notif(json.code,json.message);
					$('#grid').treegrid('load');
				},

				error: function(){
					unblockUI();
				}
			});
		}
	});
}

function alerts(title,text,color,type){
	swal({
		title: title,
		text: text,
		confirmButtonColor: color,
		type: type
	});
}

function noselectdata(){
	swal({
		title: "Error...",
		text: "Anda belum memilih data!",
		confirmButtonColor: "#EF5350",
		type: "error"
	});
}

function notif(header,message,theme){
	$.jGrowl(message, {
		header: header,
		theme: theme
	});
}

function ni_notif(code,message){
	if (code == 200) {
		$.jGrowl(message, {
			header: 'Success',
			theme: 'alert-styled-left bg-success',
		});
	}else{
		$.jGrowl(message, {
			header: 'Error',
			theme: 'alert-styled-left bg-danger',
		});
	}
}

function notif_new(header,message){
	if(header.toUpperCase() == 'SUCCESS'){
		theme = 'alert-styled-left bg-success';
	}else{
		theme = 'alert-styled-left bg-danger';
	}

	$.jGrowl(message, {
		header: header,
		theme: theme
	});
}

function blockUI(){
	$.blockUI({ 
		message: '<i class="icon-spinner4 spinner"></i>',
	    // timeout: 2000, //unblock after 2 seconds
	    overlayCSS: {
	    	backgroundColor: '#1b2024',
	    	opacity: 0.8,
	    	cursor: 'wait'
	    },
	    css: {
	    	border: 0,
	    	color: '#fff',
	    	padding: 0,
	    	backgroundColor: 'transparent'
	    }
	});
}

function unblockUI(){
	$.unblockUI({ 
		message: '<i class="icon-spinner4 spinner"></i>',
	    // timeout: 2000, //unblock after 2 seconds
	    overlayCSS: {
	    	backgroundColor: '#1b2024',
	    	opacity: 0.8,
	    	cursor: 'wait'
	    },
	    css: {
	    	border: 0,
	    	color: '#fff',
	    	padding: 0,
	    	backgroundColor: 'transparent'
	    }
	});
}

function blockID(id){
	$(id).block({
		 message: '<span class="text-semibold">Please wait...</span>',
            overlayCSS: {
                backgroundColor: '#fff',
                opacity: 0.8,
                cursor: 'wait'
            },
            css: {
                border: 0,
                padding: 0,
                backgroundColor: 'transparent'
            }
	});
}

function unblockID(id){
	$(id).unblock({
		message: '<i class="icon-spinner spinner"></i>',
		overlayCSS: {
			backgroundColor: '#1B2024',
			opacity: 0.85,
			cursor: 'wait'
		},
		css: {
			border: 0,
			padding: 0,
			backgroundColor: 'none',
			color: '#fff'
		}
	});
}

function confirm_delete_old(row,url,data){
	swal({
		title: "Hapus data?",
		text: "Data yang sudah dihapus tidak dapat dikembalikan!",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#EF5350",
		confirmButtonText: "Hapus",
		cancelButtonText: "Batal",
		closeOnConfirm: true,
		closeOnCancel: true
	},
	function(isConfirm){
		if (isConfirm) {
			blockUI();
			$.ajax({
				url: url,
				type: "POST",
				data: data,
				dataType: 'json',

				success: function(json) {
					if (json.code == 200) {
						unblockUI();
						notif(json.header,json.message,json.theme);
						$('#grid').datagrid('load');
					}else{
						unblockUI();
						notif(json.header,json.message,json.theme);
						$('#grid').datagrid('load');
					}
				},

				error: function(){
					unblockUI();
					alerts("Error","Ada kendala saat menghapus data","#EF5350","error");
					$('#grid').datagrid('load');
				}
			});
		}
	});
}

function confirm_delete(url,reload=''){
	swal({
		title: "Hapus data?",
		text: "Data yang sudah dihapus tidak dapat dikembalikan!",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#EF5350",
		confirmButtonText: "Hapus",
		cancelButtonText: "Batal",
		closeOnConfirm: true,
		closeOnCancel: true
	},
	function(isConfirm){
		if (isConfirm) {
			$.ajax({
				url: url,
				type: "GET",
				dataType: "json",
				beforeSend: function(){
					blockUI();
				},

				success: function(json) {
					if (json.code == 200) {
						unblockUI();
						notif(json.header,json.message,json.theme);
						$('#grid').datagrid('load');
					}else{
						unblockUI();
						notif(json.header,json.message,json.theme);
					}
				},

				error: function(){
				},

				complete: function(){
					unblockUI();
				}
			});
		}
	});
}

function global_confirm(url,title,text){
	swal({
		title: title,
		text: text,
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#EF5350",
		confirmButtonText: "OK",
		cancelButtonText: "Batal",
		closeOnConfirm: true,
		closeOnCancel: true
	},
	function(isConfirm){
		if (isConfirm) {
			$.ajax({
				url: url,
				type: "GET",
				dataType: "json",
				beforeSend: function(){
					blockUI();
				},

				success: function(json) {
					if (json.code == 200) {
						unblockUI();
						ni_notif(json.code,json.message);
						$('#grid').datagrid('load');
					}else{
						unblockUI();
						ni_notif(json.code,json.message);
					}
				},

				error: function(){
					unblockUI();
				},

				complete: function(){
					unblockUI();
				}
			});
		}
	});
}

function confirm_delete_menu(url,data){	
	swal({
		title: "Hapus data?",
		text: "Data yang sudah dihapus tidak dapat dikembalikan",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#EF5350",
		confirmButtonText: "Hapus",
		cancelButtonText: "Batal",
		closeOnConfirm: true,
		closeOnCancel: true
	},
	function(isConfirm){
		if (isConfirm) {
			$.ajax({
				url: url,
				type: "POST",
				dataType: "json",
				data: data,

				beforeSend: function(){
					blockUI();
				},

				success: function(json) {
					if (json.code == 200) {
						unblockUI();
						notif_new(json.header,json.message);
						close_modal();
						$('#grid').treegrid('load');
					}else{
						notif_new(json.header,json.message);
					}
				},

				error: function(){
					unblockUI();
				},

				complete: function(){
					unblockUI();
				}
			});
		}
	});
}

function noselectdata(){
	swal({
		title: "Warning",
		text: "No selected data",
		confirmButtonColor: "#FF5722",
		type: "warning"
	});
}

function confirm_whitelist(url,data){
	swal({
		title: "Update Data?",
		text: "Data yang ini akan ini akan diupdate !",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#EF5350",
		confirmButtonText: "Update",
		cancelButtonText: "Batal",
		closeOnConfirm: true,
		closeOnCancel: true
	},
	function(isConfirm){
		if (isConfirm) {
			blockUI();
			$.ajax({
				url: url,
				type: "POST",
				data: data,

				success: function(json) {
					notif("Update!","Data berhasil diupdate","bg-success");
					unblockUI();
					$('#grid').datagrid('load');
				},

				error: function(){
					unblockUI();
					alerts("Error...","Ada kendala saat mengupdate data","#EF5350","error");
					$('#grid').datagrid('load');
				}
			});
		}
	});
}

function privilege_update(url,data){
	blockUI();
	$.ajax({
		url: url,
		type: "POST",
		data: data,
		success: function(json) {
			notif("Success","Privilege berhasil diupdate","alert-styled-left bg-success");
			unblockUI();
			$('#grid').treegrid('load');
		},

		error: function(){
			unblockUI();
					// alerts("Error...","Ada kendala saat mengupdate data","#EF5350","error");
					notif("Error","Hubungi admin","alert-styled-left bg-danger");
					$('#grid').treegrid('load');
				}
			});
}