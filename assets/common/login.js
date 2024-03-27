function showPassword() {
	var x = document.getElementById("niPassword");
	if (x.type === "password") {
		x.type = "text";
	} else {
		x.type = "password";
	}
}

$("#form_login").submit(function(event){
	blockUI();
	event.preventDefault();

	$.ajax({
		url: url_login,  
		type: "POST",
		data: {
			username: $("input[name=username]").val(),
			password: $("input[name=password]").val()
		},
		dataType: 'json',
		
		success: function(json) {
			if (json.code == 0){
				window.location = json.data;
			}else{
				unblockUI();
				notif(json.header,json.message,json.theme);
			}
		},

		error: function(json){
			unblockUI();
			notif(json.header,json.message,json.theme);
		},

		complete: function(){
			unblockUI();
		}
	});
});