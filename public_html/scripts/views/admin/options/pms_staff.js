// JavaScript Document
$(document).ready(function() {


	$('form#pmss_form').validate({
		success: function(label) {
     		label.addClass("valid").text("")
		},
		rules: {
			pmss_email: {
				required: true,
				email: true,
				remote: "/admin/options/pmss_email/" + $('#pmss_id').val()
			},
			pmss_email_confirmed: {
				required: true,
				equalTo: "#pmss_email"
			},
			pmss_fname: {
				required: true
			},
			pmss_sname: {
				required: true
			},
			pmss_password: {
				required: true,
				password_restrict: true
			},
			pmss_password_confirmed: {
				required: true,
				equalTo: "#pmss_password"
			}
		},
		messages: {
			pmss_email: {
				remote: "This email address is already registered."
			},
			pmss_email_confirmed: {
				equalTo: "Please confirm your email address."
			},
			pmss_password_confirmed: {
				equalTo: "Please confirm your password."
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		}
	});


});
