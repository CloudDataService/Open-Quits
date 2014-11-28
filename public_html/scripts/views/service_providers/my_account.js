// JavaScript Document
$(document).ready(function() {


	$('form#my_account_form').validate({
		success: function(label) {
			label.addClass("valid").text("");
		},
		rules: {
			email: {
				required: true,
				email: true,
				remote: "/service-providers/my-account/email"
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
			advisor_code: {
				maxlength: 7
			}
		},
		messages: {
			email: {
				remote: "This email address is already registered."
			},
			email_confirmed: {
				equalTo: "Please confirm your email address."
			}
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent("td").next(".e") );
		}
	});


	$('#my_account_password_form').validate({
		success: function(label) {
			label.addClass("valid").text("");
		},
		rules: {
			current_password: {
				required: true,
				remote: "/service-providers/my-account/check_password_current/"
			},
			new_password: {
				required: true,
				remote: "/service-providers/my-account/check_password_valid"
			},
			new_password_confirmed: {
				required: true,
				equalTo: "#new_password"
			}
		},
		messages: {
			current_password: {
				remote: "This is not your current password."
			},
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
