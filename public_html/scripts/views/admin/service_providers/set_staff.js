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
				remote: '/admin/service-providers/staff_email/' + $('#sps_id').val()
			},
			email_confirmed: {
				required: true,
				equalTo: '#email'
			}
		},
		messages: {
			email: {
				remote: 'This email address is already registered'
			},
			email_confirmed: {
				equalTo: 'Please confirm your email address.'
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		},
		submitHandler: function (form) {
			if($('span#password').length)
			{
				if(confirm('Have you made a note of the default password?'))
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


	$('#password_form').validate({
		rules: {
			new_password: {
				required: true,
				remote: '/admin/service-providers/check_password_valid/'
			},
			new_password_confirmed: {
				required: true,
				equalTo: "#new_password"
			}
		},
		messages: {
			new_password: {
				remote: "The password must be over 8 characters in length, and contain at least 1 uppercase letter and 1 number."
			},
			new_password_confirmed: {
				equalTo: "Please confirm your password."
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		}
	});

});
