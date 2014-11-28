// JavaScript Document
$(document).ready(function() {


	$('form#admin_form').validate({
		success: function(label) {
     		label.addClass("valid").text("")
		},
		rules: {
			email: {
				required: true,
				email: true,
				remote: "/admin/options/admin_email/" + $('#admin_id').val()
			},
			email_confirmed: {
				required: true,
				equalTo: "#email"
			},
			fname: {
				required: true
			},
			sname: {
				required: true
			},
			password: {
				required: (function(){ return $("input[name='email']").val().length === 0 }),
				password_restrict: true
			},
			password_confirmed: {
				required: true,
				equalTo: "#password"
			}
		},
		messages: {
			email: {
				remote: "This email address is already registered."
			},
			email_confirmed: {
				equalTo: "Please confirm your email address."
			},
			password_confirmed: {
				equalTo: "Please confirm your password."
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		},
		submitHandler: function (form) {
			if($('#master:checked').length)
			{
				if(confirm('You are currently the master administrator. Making the following administrator master will log you out.'))
				{
					form.submit();
				}

				return false;
			}
			else
			{
				form.submit();
			}
		}
	});


	$("select[name='pct_id']").on("change", function(e) {
		var allow_master = ($(this).val().length === 0);
		if (allow_master) {
			$(".master-admin-row").show();
		} else {
			$(".master-admin-row").hide();
			$("input[name='master']").removeProp("checked");
		}
	}).trigger("change");


});
