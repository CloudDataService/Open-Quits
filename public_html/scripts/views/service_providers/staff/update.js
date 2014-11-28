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
				remote: '/service-providers/staff/staff_email/' + $('#sps_id').val()
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


	$('#password_form').validate({
		rules: {
			new_password: {
				required: true,
				password_restrict: true
			},
			new_password_confirmed: {
				required: true,
				equalTo: "#new_password"
			}
		},
		messages: {
			new_password_confirmed: {
				equalTo: "Please confirm your password."
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		}
	});


	$('#training_form').validate({
		rules: {
			spst_date: {
				required: true
			},
			spst_title: {
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		}
	});
	$('.datepicker').datepicker();

});
