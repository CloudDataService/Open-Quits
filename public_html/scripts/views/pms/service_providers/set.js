// JavaScript Document
$(document).ready(function () {

	$('#service_provider_form').validate({
		success: function(label) {
     		label.addClass("valid").text("")
		},
		rules: {
			name: {
				required: true
			},
			post_code: {
				required: true
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		}
	});

});
