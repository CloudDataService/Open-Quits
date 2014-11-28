// JavaScript Document
$(document).ready(function () {

	$('#reply_form').validate({
		success: function(label) {
     		label.addClass("valid").text("")
		},
		rules: {
			ticket_response: {
				required: true
			}
		},
		errorPlacement: function(error, element) {
     		error.appendTo( element.parent("td").next(".e") );
   		}
	});

});
