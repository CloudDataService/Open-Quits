// JavaScript Document
$(document).ready(function () {

	$('#service_provider_staff_form').validate({
		success: function(label) {
     		label.addClass("valid").text("")
		},
		rules: {
			fname: {
				required: true
			},
			sname: {
				required: true
			},
			email: {
				required: true,
				email: true,
				remote: '/service-providers/staff/staff_email'
			},
			email_confirmed: {
				required: true,
				equalTo: '#email'
			},
			password: {
				required: true,
				password_restrict: true
			},
			password_confirmed: {
				required: true,
				equalTo: "#password"
			}
		},
		messages: {
			email: {
				remote: 'This email address is already registered'
			},
			email_confirmed: {
				equalTo: 'Please confirm your email address.'
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
				if(confirm('You are currently the administrator. Making the following staff member administrator will log you out.'))
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


});
