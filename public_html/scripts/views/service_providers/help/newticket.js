// JavaScript Document
$(document).ready(function () {

	$('#support_form').validate({
		success: function(label) {
     		label.addClass("valid").text("")
		},
		rules: {
			problem: {
				required: true
			},
			subject: {
				required: true
			},
			description: {
				required: true
			},
			contact_telephone: {
				required: true,
			},
			contact_time: {
				required: true
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		}
	});

});
